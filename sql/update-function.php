<?php

    use League\HTMLToMarkdown\HtmlConverter;
    use Tracy\Debugger;

    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

/**
 * CREATE database.table;.
 *
 * @param array create table $table['key'] ($table['value'] auto_increment primary)
 *
 * @return int of created tables
 */
function bistroDBTableCreate($table): int
{
    global $database,$config;
    $alter = 0;
    foreach ($table as $key => $value) {
        if (DBtableExist($key) == 0) {
            $sqlCreate = "CREATE TABLE ".DB_PREFIX.$key." (".$value." int NOT NULL AUTO_INCREMENT PRIMARY KEY)";
            mysqli_query($database,$sqlCreate);
            if (DBtableExist($key) != 0) {
                Debugger::log('UPDATER '.$config['version'].': '.$sqlCreate);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * RENAME TABLE database.oldtable RENAME TO database.newtable';.
 *
 * @param array $data rename_table['table'] = "tableNew";
 *
 * @return int of changed items
 */
function bistroDBTableRename($data): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $old => $new) {
        if (DBtableExist($new) == 0 and DBtableExist($old) != 0) {
            $renameSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$old RENAME TO ".$config['dbdatabase'].".".DB_PREFIX."$new";
            mysqli_query($database,$renameSql);
            if (DBtableExist($new) != 0 and DBtableExist($old) == 0) {
                Debugger::log('UPDATER '.$config['version'].': '.$renameSql);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * ALTER TABLE database.table ADD COLUMN column params;.
 *
 * @param array $data add_column['table']['column'] = "VARCHAR(32) NOT NULL AFTER columnPrevious";
 *
 * @return int of changed items
 */
function bistroDBColumnAdd($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            if (DBtableExist($table) != 0 and DBcolumnExist($table,$column) == 0) {
                $alterSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table ADD COLUMN $column ".$data[$table][$column];
                mysqli_query($database,$alterSql);
                if (DBcolumnExist($table,$column) != 0) {
                    Debugger::log('UPDATER '.$config['version'].': '.$alterSql);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * ALTER TABLE database.table CHANGE oldcolumn newcolumn newparams;.
 *
 * @param array $data alter_column['table']['column'] = " columnNew varchar(32) NULL AFTER columnPrevious";
 *
 * @return int of changed items
 */
function bistroDBColumnAlter($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            if (DBcolumnExist($table,$column) != 0) {  //existuje > updatnout
                $alterSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table CHANGE $column ".$data[$table][$column];
                mysqli_query($database,$alterSql);
                if (DBcolumnExist($table,strtok($column,' ')) != 0 && DBcolumnExist($table,strtok($data[$table][$column],' ')) == 0) {
                    Debugger::log('UPDATER '.$config['version'].': '.$alterSql);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * if user.password != 32
 * UPDATE database.user set pwd=md5(password) where id=user_id);.
 *
 * @return int of changed items
 */
function bistroDBPasswordEncrypt(): int
{
    global $database,$config;
    $alterPassword = $alter = 0;
    $passwordSql = "SELECT userPassword FROM ".$config['dbdatabase'].".".DB_PREFIX."user";
    $passwordQuery = mysqli_query($database,$passwordSql);
    while ($passwordData = mysqli_fetch_array($passwordQuery)) {
        if (mb_strlen($passwordData['userPassword']) != 32) {
            $alterPassword++;
        }
    }
    unset($passwordSql);
    if ($alterPassword > 0) {
        $passwordSql = "SELECT userPassword,userId,userName FROM ".$config['dbdatabase'].".".DB_PREFIX."user";
        $passwordQuery = mysqli_query($database,$passwordSql);
        while ($passwordData = mysqli_fetch_array($passwordQuery)) {
            mysqli_query($database,"UPDATE ".$config['dbdatabase'].".".DB_PREFIX."user set userPassword=md5('".$passwordData['userPassword']."') where userId=".$passwordData['userId']);
            Debugger::log('UPDATER '.$config['version'].': Hashing '.$passwordData['userName'].'.userPassword');
            $alter++;
        }
    }

    return $alter;
}

/**
 * UPDATE database.table SET columnMarkdown='contentMarkdown' WHERE id = id;.
 *
 * @param array $data data[] = ['table','id','htmlColumn','markdownColumn'];
 *
 * @return int of changed items
 */
function bistroDBColumnMarkdown($data): int
{
    global $database,$config;
    $alter = 0;
    $converter = new HtmlConverter(['strip_tags' => true]); //https://github.com/thephpleague/html-to-markdown
    foreach ($data as $value) { //$data as $key => $value
        $preMarkdownSql = "SELECT ".$value[1].", ".$value[2]." FROM ".$config['dbdatabase'].".".DB_PREFIX.$value[0]." WHERE (length(".$value[3].") = 0  or length(".$value[3].") is null) and length(".$value[2].") > 0";
        if (DBcolumntNotEmpty($value[0],$value[3]) == 0 && DBcolumnExist($value[0],$value[2]) > 0 && DBcolumnExist($value[0],$value[3]) > 0) {
            $preMarkdownQuery = mysqli_query($database,$preMarkdownSql);
            while ($preMarkdown = mysqli_fetch_array($preMarkdownQuery)) {
                $markdownColumn = $converter->convert(str_replace('\'', '', $preMarkdown[$value[2]]));
                Debugger::log('UPDATER '.$config['version'].' CONVERTING '.DB_PREFIX.$value[0].'.'.$value[2].'.'.$preMarkdown[$value[1]].' TO MARKDOWN '.$value[3]);
                mysqli_query($database,"UPDATE ".DB_PREFIX.$value[0]." SET ".$value[3]."='".$markdownColumn."' WHERE ".$value[1]."=".$preMarkdown[$value[1]]);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * MIGRATE ACCESS RIGHTS.
 *
 * @param mixed $data
 */
function bistroMigratePermissions($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $old) {
        foreach ($data[$old] as $new) {
            if (DBcolumnExist('user',$new) and DBcolumnExist('user',$old)) {
                $alterSql = "UPDATE ".$config['dbdatabase'].".".DB_PREFIX."user SET $new=$old;";
                mysqli_query($database,$alterSql);
                if (mysqli_affected_rows($database) > 0) {
                    Debugger::log('UPDATER '.$config['version'].': PERMISSIONS '.$old.' => '.$new);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * ALTER TABLE database.table ADD FULLTEXT (column)".
 *
 * @param array $data add_fulltext['table'] = ['column1', 'column2', 'column3'];
 *
 * @return int of changed items
 */
function bistroDBFulltextAdd($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $value) {
            $checkSql = "SHOW INDEX FROM ".$config['dbdatabase'].".".DB_PREFIX."$table WHERE index_type = 'FULLTEXT' and column_name='$value'";
            if (DBtableExist($table) != 0 and (mysqli_num_rows(mysqli_query($database,$checkSql)) == 0)) {
                $alterSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX."$table ADD FULLTEXT ($value)";
                mysqli_query($database,$alterSql);
                Debugger::log('UPDATER '.$config['version'].': '.$alterSql);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * ALTER TABLE ADD INDEX.
 *
 * @param mixed $data
 */
// function bistroDBIndexAdd($data): int
// {
//     global $database, $config;
//     $alter = 0;

//     return $alter++;
// }

/**
 * DROP table.column.
 *
 * @return int of droped columns
 */
function bistroDBColumnDrop($data): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $column) {
            if (DBcolumnExist($table,$column) != 0) {
                $dropSql = "ALTER TABLE ".$config['dbdatabase'].".".DB_PREFIX.$table." DROP $column";
                mysqli_query($database,$dropSql);
                if (DBColumnExist($table,$column) == 0) {
                    Debugger::log('UPDATER '.$config['version'].': '.$dropSql);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * DROP database.table;.
 *
 * @param array $data
 *
 * @return int of deleted tables
 */
function bistroDBTableDrop($data): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $value) { //$data as $key => $value
        if (DBtableExist($value) != 0) {
            $dropSql = "DROP TABLE ".$config['dbdatabase'].".".DB_PREFIX.$value;
            mysqli_query($database,$dropSql);
            if (DBtableExist($value) == 0) {
                Debugger::log('UPDATER '.$config['version'].': '.$dropSql);
                $alter++;
            }
        }
    }

    return $alter;
}
