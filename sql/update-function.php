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
        $checkNewSql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$new'";
        $checkOldSql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$old'";
        $checkNew = mysqli_query($database,$checkNewSql);
        $checkOld = mysqli_query($database,$checkOldSql);
        if ((mysqli_num_rows($checkNew) == 0) AND (mysqli_num_rows($checkOld) != 0)) {
            $renameSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$old RENAME TO ".$config['dbdatabase'].".".DB_PREFIX."$new";
            mysqli_query($database,$renameSql);
            unset ($checkNew, $checkOld);
             
            $checkNew = mysqli_query($database,$checkNewSql);
            $checkOld = mysqli_query($database,$checkOldSql);
            if ((mysqli_num_rows($checkNew) != 0) AND (mysqli_num_rows($checkOld) == 0)) {
                Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$renameSql);
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
            $checkSql = mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
            $checkTable = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'");
            if ((mysqli_num_rows($checkTable) != 0) and (mysqli_num_rows($checkSql) == 0)) {
                $alterSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table ADD COLUMN `$column` ".$data[$table][$column];
                Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$alterSql);
                mysqli_query($database,$alterSql);
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
        foreach ($data[$table] as $value ) { // =>
            $checkSql = mysqli_query($database,"SHOW INDEX FROM ".$config['dbdatabase'].".".DB_PREFIX."$table WHERE index_type = 'FULLTEXT' and column_name='$value'");
            $checkTable = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'");
            if ((mysqli_num_rows($checkTable) != 0) and (mysqli_num_rows($checkSql) == 0)) {
                $alterSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table ADD FULLTEXT (`$value`)";
                Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$alterSql);
                mysqli_query($database,$alterSql);
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
            $checkSql = mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
            $checkTable = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'");
            if ((mysqli_num_rows($checkTable) != 0) and (mysqli_num_rows($checkSql) != 0)) {  //existuje > updatnout
                $alterSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table CHANGE `$column` ".$data[$table][$column];
                mysqli_query($database,$alterSql);
                if (mysqli_affected_rows($database) > 0) {
                    Debugger::log('UPDATER '.$config['version'].' DB CHANGE: '.$alterSql);
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
    $alterPassword = $alter = 0;
    $passwordSql = mysqli_query($database,"SELECT pwd FROM ".$config['dbdatabase'].".".DB_PREFIX."user");
    while ($passwordData = mysqli_fetch_array($passwordSql)) {
        if (mb_strlen($passwordData['pwd']) != 32) {
            $alterPassword++;
        }
    }
    unset ($passwordSql);
    if ($alterPassword > 0) {
        $passwordSql = mysqli_query($database,"SELECT pwd,id,login FROM ".$config['dbdatabase'].".".DB_PREFIX."user");
        while ($passwordData = mysqli_fetch_array($passwordSql)) {
            mysqli_query($database,"UPDATE ".$config['dbdatabase'].".".DB_PREFIX."user set pwd=md5('".$passwordData['pwd']."') where id=".$passwordData['id']);
            Debugger::log('UPDATER '.$config['version'].' Hashing password for userid: '.$passwordData['login']);
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
    foreach ($data as $value) { //$data as $key => $value
        $preMarkdownSql = mysqli_query($database,"SELECT ".$value[1].", ".$value[2]." FROM ".$config['dbdatabase'].".".DB_PREFIX.$value[0]." WHERE ".$value[3]." is null");
        while ($preMarkdown = mysqli_fetch_array($preMarkdownSql)) {
            $markdownColumn = $converter->convert( str_replace('\'', '', $preMarkdown[$value[2]]));
            Debugger::log('UPDATER '.$config['version'].' Markdown conversion ['.DB_PREFIX.$value[0].'.'.$preMarkdown[$value[1]].']: '.$preMarkdown[$value[2]].' ##### TO ##### '.$markdownColumn);
            mysqli_query($database,"UPDATE ".DB_PREFIX.$value[0]." SET ".$value[3]."='".$markdownColumn."' WHERE ".$value[1]."=".$preMarkdown[$value[1]]);
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
    foreach ($data as $value) { //$data as $key => $value
        $dropSql = "DROP TABLE ".$config['dbdatabase'].".".DB_PREFIX.$value;
        mysqli_query($database,$dropSql);
        $checkTable = mysqli_query($database,"SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX.$value."'");
        if (mysqli_num_rows($checkTable) == 0) {
            Debugger::log('UPDATER '.$config['version'].' DB CHANGE: DELETE TABLE '.DB_PREFIX.$value);
            $alter++;
        }
    }

    return $alter;
}
