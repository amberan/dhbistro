<?php

    use League\HTMLToMarkdown\HtmlConverter;
    use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

/** 
* ALTER TABLE `database`.`oldtable` RENAME TO `database`.`newtable';
* @param array $data rename_table['table'] = "tableNew";
* @return int of changed items
*/
function bistroDBTableRename ($data): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $old => $new) {
        $check_new_sql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$new'";
        $check_old_sql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$old'";
        $check_new = mysqli_query($database,$check_new_sql);
        $check_old = mysqli_query($database,$check_old_sql);
        if ((mysqli_num_rows($check_new) == 0) AND (mysqli_num_rows($check_old) != 0)) {
            $rename_sql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$old RENAME TO ".$config['dbdatabase'].".".DB_PREFIX."$new";
            mysqli_query($database,$rename_sql);
            unset ($check_new, $check_old);
             
            $check_new = mysqli_query($database,$check_new_sql);
            $check_old = mysqli_query($database,$check_old_sql);
            if ((mysqli_num_rows($check_new) != 0) AND (mysqli_num_rows($check_old) == 0)) {
                Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$rename_sql);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * ALTER TABLE `database`.`table` ADD COLUMN `column` params; 
 * @param array $data add_column['table']['column'] = "VARCHAR(32) NOT NULL AFTER `columnPrevious`";
 * @return int of changed items
 */
function bistroDBColumnAdd($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            $check_sql = mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
            $check_table = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'");
            if ((mysqli_num_rows($check_table) != 0) and (mysqli_num_rows($check_sql) == 0)) {
                $alter_sql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table ADD COLUMN `$column` ".$data[$table][$column];
                Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$alter_sql);
                mysqli_query($database,$alter_sql);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * ALTER TABLE `database`.`table` ADD FULLTEXT (`column`)"
 * @param array $data add_fulltext['table'] = ['column1', 'column2', 'column3'];
 * @return int of changed items
 */
function bistroDBFulltextAdd($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $key => $value ) {
            $check_sql = mysqli_query($database,"SHOW INDEX FROM ".$config['dbdatabase'].".".DB_PREFIX."$table WHERE index_type = 'FULLTEXT' and column_name='$value'");
            $check_table = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'");
            if ((mysqli_num_rows($check_table) != 0) and (mysqli_num_rows($check_sql) == 0)) {
                $alter_sql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table ADD FULLTEXT (`$value`)";
                Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$alter_sql);
                mysqli_query($database,$alter_sql);
                $alter++;
            }
        }
    }

    return $alter;
}


/**
 * ALTER TABLE `database`.`table` CHANGE `oldcolumn` `newcolumn` newparams;
 * @param array $data alter_column['table']['column'] = " `columnNew` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `columnPrevious`";
 * @return int of changed items
 */
function bistroDBColumnAlter($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            $check_sql = mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
            $check_table = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'");
            if ((mysqli_num_rows($check_table) != 0) and (mysqli_num_rows($check_sql) == 0)) {
                $alter_sql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table CHANGE `$column` ".$data[$table][$column];
                mysqli_query($database,$alter_sql);
                if (mysqli_affected_rows($database) > 0) {
                    Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$alter_sql);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * if user.password != 32 
 * UPDATE `database`.user set pwd=md5(`password`) where id=`user_id`);
 * @return int of changed items
 */
function bistroDBPasswordEncrypt(): int
{
    global $database,$config;
    $alter_password = $alter = 0;
    $password_sql = mysqli_query($database,"SELECT pwd FROM ".$config['dbdatabase'].".".DB_PREFIX."user");
    while ($password_data = mysqli_fetch_array($password_sql)) {
        if (mb_strlen($password_data['pwd']) != 32) {
            $alter_password++;
        }
    }
    unset ($password_sql);
    if ($alter_password > 0) {
        $password_sql = mysqli_query($database,"SELECT pwd,id,login FROM ".$config['dbdatabase'].".".DB_PREFIX."user");
        while ($password_data = mysqli_fetch_array($password_sql)) {
            mysqli_query($database,"UPDATE ".$config['dbdatabase'].".".DB_PREFIX."user set pwd=md5('".$password_data['pwd']."') where id=".$password_data['id']);
            Debugger::log('UPDATER '.$config['version'].' Hashing password for userid: '.$password_data['login']);
            $alter++;
        }
    }

    return $alter;
}


/**
 * UPDATE `database`.`table` SET columnMarkdown='contentMarkdown' WHERE id = id;
 * @param array $data data[] = ['table','id','htmlColumn','markdownColumn'];
 * @return int of changed items
 */
function bistroDBColumnMarkdown($data): int
{
    global $database,$config;
    $alter = 0;
    $converter = new HtmlConverter(array('strip_tags' => true)); //https://github.com/thephpleague/html-to-markdown
    foreach ($data as $key => $value) {
        $preMD_sql = mysqli_query($database,"SELECT ".$value[1].", ".$value[2]." FROM ".$config['dbdatabase'].".".DB_PREFIX.$value[0]." WHERE ".$value[3]." is null");
        while ($preMD = mysqli_fetch_array($preMD_sql)) {
            $MDcolumn = $converter->convert( str_replace('\'', '', $preMD[$value[2]]));
            Debugger::log('UPDATER '.$config['version'].' Markdown conversion ['.DB_PREFIX.$value[0].'.'.$preMD[$value[1]].']: '.$preMD[$value[2]].' ##### TO ##### '.$MDcolumn);
            mysqli_query($database,"UPDATE ".DB_PREFIX.$value[0]." SET ".$value[3]."='".$MDcolumn."' WHERE ".$value[1]."=".$preMD[$value[1]]);
            $alter++;
        }
    }

    return $alter;
}

/**
 * DROP `database`.`table`;
 * @param array $data
 * @return int of deleted tables
 */
function bistroDBTableDrop($data): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $key => $value) {
        $drop_sql = "DROP TABLE ".$config['dbdatabase'].".".DB_PREFIX.$value;
        mysqli_query($database,$drop_sql);
        $check_table = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX.$value."'");
        if (mysqli_num_rows($check_table) == 0) {
            Debugger::log('UPDATER '.$config['version'].' DB CHANGE: DELETE TABLE '.DB_PREFIX.$value);
            $alter++;
        }
    }

    return $alter;
}
