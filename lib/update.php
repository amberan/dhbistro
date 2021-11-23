<?php

    use League\HTMLToMarkdown\HtmlConverter;
    use Tracy\Debugger;

    Debugger::enable(Debugger::DETECT, $config['folder_logs']);

/**
 * converts configuration from password file and platform definition to one
 */
function bistroConvertPlatform()
{
    global $config,$latteParameters;
    if (isset($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])
    && DBTest($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])) {
        // form posted and connection tested
        bistroConfigFile(
            $_POST['dbHost'],
            $_POST['dbUser'],
            $_POST['dbPassword'],
            $_POST['dbDatabase'],
            $_POST['dbPrefix'],
            $_POST['themeColor'],
            $_POST['themeBackground'],
            $_POST['themeNavbar'],
            $_POST['themeCustom']
        );
    } elseif (file_exists(SERVER_ROOT.'/inc/platform.php')) {
        // convert old files
        $config['dbHost'] = 'localhost';
        require_once SERVER_ROOT.'/inc/platform.php';
        if (file_exists($config['dbpass'])) {
            $lines = file($config['dbpass'], FILE_IGNORE_NEW_LINES) or die("fail pwd");
            $config['dbPassword'] = $lines[2];
        }
        if (DBTest($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase'])) {
            //connection tested
            bistroConfigFile(
                $config['dbHost'],
                $config['dbUser'],
                $config['dbPassword'],
                $config['dbDatabase'],
                DB_PREFIX,
                $config['themeColor'],
                $config['themeBg'],
                $config['themeNavbar'],
                $config['themeCustom']
            );
        } else {
            // old files, unable to connect to db
            $config['dbPrefix'] = DB_PREFIX;
            $latteParameters['config'] = $config;
            latteDrawTemplate('installer');
            die();
        }
    } else {
        $latteParameters['config'] = $_POST;
        latteDrawTemplate('installer');
        die();
    }
}

/**
 * save instance configuration in file
 */
function bistroConfigFile($dbHost, $dbUser, $dbPassword, $dbDatabase, $dbPrefix = 'nw_', $themeColor = 'dark', $themeBg = 'dark', $themeNavbar = 'dark', $themeCustom = 'NH')
{
    global $config;
    $newConfigFile = fopen($config['platformConfig'], "w") or die("Unable to write configuration file!");
    echo $configList = '<?php
        define(\'DB_PREFIX\', \''.$dbPrefix.'\');
        $'.'config[\'dbHost\']            = \''.$dbHost.'\';
        $'.'config[\'dbUser\']            = \''.$dbUser.'\';
        $'.'config[\'dbPassword\']          = \''.$dbPassword.'\';
        $'.'config[\'dbDatabase\']        = \''.$dbDatabase.'\';
        $'.'config[\'themeColor\']             = \''.$themeColor.'\';
        $'.'config[\'themeCustom\']            = \''.$themeCustom.'\';
        $'.'config[\'themeBg\']          = \''.$themeBg.'\';
        $'.'config[\'themeNavbar\']      = \''.$themeNavbar.'\';
        ';
    fwrite($newConfigFile, $configList);
    fclose($newConfigFile);
}

/**
 * CREATE database.table;.
 *
 * @param array create table $table['key'] ($table['value'] auto_increment primary)
 *
 * @return int of created tables
 */
function bistroDBTableCreate($table, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach ($table as $key => $value) {
        if (DBtableExist($key) == 0) {
            $sqlCreate = "CREATE TABLE ".DB_PREFIX.$key." (".$value." int NOT NULL AUTO_INCREMENT PRIMARY KEY)";
            mysqli_query($database, $sqlCreate);
            if (DBtableExist($key) != 0) {
                Debugger::log('UPDATER '.$file.': '.$sqlCreate);
                $alter++;
            } else {
                Debugger::log('ERROR '.$file.': '.$sqlCreate);
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
function bistroDBTableRename($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $old => $new) {
        if (DBtableExist($new) == 0 && DBtableExist($old) != 0) {
            $renameSql = "ALTER TABLE ".$config['dbDatabase'].".".DB_PREFIX."$old RENAME TO ".$config['dbDatabase'].".".DB_PREFIX."$new";
            mysqli_query($database, $renameSql);
            if (DBtableExist($new) != 0 && DBtableExist($old) == 0) {
                Debugger::log('UPDATER '.$file.': '.$renameSql);
                $alter++;
            } else {
                Debugger::log('ERROR '.$file.': '.$renameSql);
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
function bistroDBColumnAdd($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            if (DBtableExist($table) != 0 && DBcolumnExist($table, $column) == 0) {
                $alterSql = "ALTER TABLE ".$config['dbDatabase'].".".DB_PREFIX."$table ADD COLUMN $column ".$data[$table][$column];
                mysqli_query($database, $alterSql);
                if (DBcolumnExist($table, $column) != 0) {
                    Debugger::log('UPDATER '.$file.': '.$alterSql);
                    $alter++;
                } else {
                    Debugger::log('ERROR '.$file.': '.$alterSql);
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
function bistroDBColumnAlter($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            if (DBcolumnExist($table, $column) != 0) {  //existuje > updatnout
                $alterSql = "ALTER TABLE ".$config['dbDatabase'].".".DB_PREFIX."$table CHANGE $column ".$data[$table][$column];
                mysqli_query($database, $alterSql);
                if (($column == explode(' ', trim($data[$table][$column]))[0]) || DBcolumnExist($table, $column) == 0 && DBcolumnExist($table, explode(' ', trim($data[$table][$column]))[0]) != 0) {
                    Debugger::log('UPDATER '.$file.': '.$alterSql);
                    $alter++;
                } else {
                    Debugger::log('ERROR '.$file.': '.$alterSql);
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
    $passwordSql = "SELECT userPassword FROM ".$config['dbDatabase'].".".DB_PREFIX."user";
    $passwordQuery = mysqli_query($database, $passwordSql);
    if (mysqli_num_rows($passwordQuery) > 0) {
        while ($passwordData = mysqli_fetch_array($passwordQuery)) {
            if (mb_strlen($passwordData['userPassword']) != 32) {
                $alterPassword++;
            }
        }
    }
    unset($passwordSql);
    if ($alterPassword > 0) {
        $passwordSql = "SELECT userPassword,userId,userName FROM ".$config['dbDatabase'].".".DB_PREFIX."user";
        $passwordQuery = mysqli_query($database, $passwordSql);
        while ($passwordData = mysqli_fetch_array($passwordQuery)) {
            mysqli_query($database, "UPDATE ".$config['dbDatabase'].".".DB_PREFIX."user set userPassword=md5('".$passwordData['userPassword']."') where userId=".$passwordData['userId']);
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
function bistroDBColumnMarkdown($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    $converter = new HtmlConverter(['strip_tags' => true]); //https://github.com/thephpleague/html-to-markdown
    foreach ($data as $value) { //$data as $key => $value
        $preMarkdownSql = "SELECT ".$value[1].", ".$value[2]." FROM ".$config['dbDatabase'].".".DB_PREFIX.$value[0]." WHERE (length(".$value[3].") = 0  or length(".$value[3].") is null) and length(".$value[2].") > 0";
        if (DBcolumntNotEmpty($value[0], $value[3]) == 0 && DBcolumnExist($value[0], $value[2]) > 0 && DBcolumnExist($value[0], $value[3]) > 0) {
            $preMarkdownQuery = mysqli_query($database, $preMarkdownSql);
            while ($preMarkdown = mysqli_fetch_array($preMarkdownQuery)) {
                $markdownColumn = $converter->convert(str_replace('\'', '', $preMarkdown[$value[2]]));
                Debugger::log('UPDATER '.$file.' CONVERTING '.DB_PREFIX.$value[0].'.'.$value[2].'.'.$preMarkdown[$value[1]].' TO MARKDOWN '.$value[3]);
                mysqli_query($database, "UPDATE ".DB_PREFIX.$value[0]." SET ".$value[3]."='".$markdownColumn."' WHERE ".$value[1]."=".$preMarkdown[$value[1]]);
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
function bistroMigratePermissions($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $old) {
        foreach ($data[$old] as $new) {
            if (DBcolumnExist('user', $new) && DBcolumnExist('user', $old)) {
                $alterSql = "UPDATE ".$config['dbDatabase'].".".DB_PREFIX."user SET $new=$old;";
                mysqli_query($database, $alterSql);
                if (mysqli_affected_rows($database) > 0) {
                    Debugger::log('UPDATER '.$file.': PERMISSIONS '.$old.' => '.$new);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * CONVERT int to timestamp
 */
function bistroIntToTimestamp($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $change) {
        if (DBcolumnExist($change[0], $change[1]) && DBcolumnExist($change[0], $change[2])) {
            $alterSql = "UPDATE ".$config['dbDatabase'].".".DB_PREFIX.$change[0]." SET ".$change[2]."=FROM_UNIXTIME(".$change[1].") where ".$change[1].">0 ;";
            mysqli_query($database, $alterSql);
            if (mysqli_affected_rows($database) > 0) {
                Debugger::log('UPDATER '.$file.': TIME CONVERSION nw'.$change[0].':  '.$change[1].' => '.$change[2]);
                $alter++;
            } else {
                Debugger::log('ERROR '.$file.': '.$alterSql);
            }
        }
    }
    return $alter;
}

function bistroMyisamToInnodb(): int
{
    global $database,$config;
    $alter =0;
    $myisamDbsql = "select table_name from information_schema.tables tab
    where engine = 'MyISAM' and table_type = 'BASE TABLE' and table_schema not in ('information_schema', 'sys', 'performance_schema','mysql')
    and table_schema = 'bistro' order by table_schema, table_name";
    $myisamDbQuery = mysqli_query($database, $myisamDbsql);
    while ($mysqisamDb = mysqli_fetch_assoc($myisamDbQuery)) {
        $innoDbQuery = "ALTER TABLE ".$mysqisamDb['table_name']." engine='InnoDB'";
        Debugger::log('UPDATER '.$config['version'].': '.$mysqisamDb['table_name'].' converted from MyISAM to InnoDB');
        mysqli_query($database, $innoDbQuery);
        $alter++;
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
function bistroDBFulltextAdd($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $value) {
            $checkSql = "SHOW INDEX FROM ".$config['dbDatabase'].".".DB_PREFIX."$table WHERE index_type = 'FULLTEXT' and column_name='$value'";
            if (DBtableExist($table) != 0 && (mysqli_num_rows(mysqli_query($database, $checkSql)) == 0)) {
                $alterSql = "ALTER TABLE ".$config['dbDatabase'].".".DB_PREFIX."$table ADD FULLTEXT ($value)";
                mysqli_query($database, $alterSql);
                Debugger::log('UPDATER '.$file.': '.$alterSql);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * DROP table.column.
 *
 * @return int of droped columns
 */
function bistroDBColumnDrop($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $column) {
            if (DBcolumnExist($table, $column) != 0) {
                $dropSql = "ALTER TABLE ".$config['dbDatabase'].".".DB_PREFIX.$table." DROP $column";
                mysqli_query($database, $dropSql);
                if (DBColumnExist($table, $column) == 0) {
                    Debugger::log('UPDATER '.$file.': '.$dropSql);
                    $alter++;
                } else {
                    Debugger::log('ERROR '.$file.': '.$dropSql);
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
function bistroDBTableDrop($data, $file = null): int
{
    global $database,$config;
    $alter = 0;
    foreach ($data as $value) { //$data as $key => $value
        if (DBtableExist($value) != 0) {
            $dropSql = "DROP TABLE ".$config['dbDatabase'].".".DB_PREFIX.$value;
            mysqli_query($database, $dropSql);
            if (DBtableExist($value) == 0) {
                Debugger::log('UPDATER '.$file.': '.$dropSql);
                $alter++;
            } else {
                Debugger::log('ERROR '.$file.': '.$dropSql);
            }
        }
    }

    return $alter;
}
