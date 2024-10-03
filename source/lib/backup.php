<?php

function bistroBackup()
{
    global $database;
    $lastBackupSql = "SELECT time,version FROM " . DB_PREFIX . "backup ORDER BY time DESC LIMIT 1";
    if (DBcolumnExist("backups", "version")) { // 1.5.2> && <1.7.3
        $lastBackupSql = "SELECT time,version FROM " . DB_PREFIX . "backups ORDER BY time DESC LIMIT 1";
    } elseif (DBtableExist("backups") && !DBcolumnExist("backup", "version")) { // <1.5.2
        $lastBackupSql = "SELECT time FROM " . DB_PREFIX . "backups ORDER BY time DESC LIMIT 1";
    }
    $lastBackup = mysqli_fetch_assoc(mysqli_query($database, $lastBackupSql));
    if (!isset($lastBackup['version'])) {
        $sqlDefaultFiles = filterDirectory(SERVER_ROOT . "sql", "default");
        $lastBackup['version'] = substr(end($sqlDefaultFiles), 8, -4);
        $lastBackup['time'] = time();
    }
    $sqlUpdateFiles = filterDirectory(SERVER_ROOT . "sql", "update");
    $updatesToRun = bistroUpdatesList($sqlUpdateFiles, @$lastBackup['version']);
    if (round($lastBackup['time'], -5) < round(time(), -5) || sizeof($updatesToRun) > 0) {
        bistroBackupGenerate();
    }
    bistroUpdate($updatesToRun);
}

/**
 * list all the present backups in database and on disk.
 */
function bistroBackupList($empty = null)
{
    global $config,$database,$text;
    $backup = $backups_sql = "SELECT " . DB_PREFIX . "backup.* FROM " . DB_PREFIX . "backup " . sortingGet('backup');
    $backups_query = mysqli_query($database, $backups_sql);
    while (mysqli_num_rows($backups_query) > 0 && $backup_record = mysqli_fetch_assoc($backups_query)) {
        unset($backup);
        $file = basename($backup_record['file']);
        if (file_exists($config['folder_backup'] . $file) || $empty) {
            $backup['file'] = "file/backup/" . $backup_record['id'];
            $backup['datetime'] = webDateTime($backup_record['time']);
            $backup['version'] = $backup_record['version'];
            if (file_exists($config['folder_backup'] . $file)) {
                $backup['filesize'] = human_filesize(filesize($config['folder_backup'] . $file)) . "B";
            } else {
                $backup['filesize'] = $text['notificationRecordNotFound'];
            }
            $backup_array[] = $backup;
        }
    }
    if (sizeof($backup_array) < 1) {
        $backup_array = false;
    }
    return $backup_array;
}

function backupBackupGetData()
{
    global $database;
    $sqlScript = 'SET autocommit=0; SET unique_checks=0; SET foreign_key_checks=0;';
    $tables = DBListTables();

    foreach ($tables as $table) {
        $sqlScript .= "-- Table `" . $table . "` --\n";

        $results = mysqli_query($database, "SHOW CREATE TABLE " . $table);
        while ($row = mysqli_fetch_array($results)) {
            $sqlScript .= $row[1] . ";\n\n";
        }

        $results = mysqli_query($database, "SELECT * FROM " . $table);
        $row_count = mysqli_num_rows($results);
        $fields = mysqli_fetch_fields($results);
        $fields_count = count($fields);

        $insert_head = "INSERT INTO `" . $table . "` (";
        for ($i = 0; $i < $fields_count; $i++) {
            $insert_head .= "`" . $fields[$i]->name . "`";
            if ($i < $fields_count - 1) {
                $insert_head .= ', ';
            }
        }
        $insert_head .= ")";
        $insert_head .= " VALUES\n";

        if ($row_count > 0) {
            $r = 0;
            while ($row = mysqli_fetch_array($results)) {
                if (($r % 400) == 0) {
                    $sqlScript .= $insert_head;
                }
                $sqlScript .= "(";
                for ($i = 0; $i < $fields_count; $i++) {
                    $row_content = str_replace("\n", "\\n", mysqli_real_escape_string($database, $row[$i] . ' '));
                    //TODO mysqli_real_escape_string deprecated?
                    //PHP Deprecated: mysqli_real_escape_string(): Passing null to parameter #2 ($string) of type string is deprecated in .../charles/workspace/alembiq/bistro/htdocs/lib/backup.php:94

                    switch ($fields[$i]->type) {
                        case 8: case 3: //int bigint
                            $sqlScript .= $row_content;
                            break;
                        case 7: //timestamp
                            if (strlen($row_content) < 1) {
                                $sqlScript .= ' NULL ';
                            } else {
                                $sqlScript .= "'" . $row_content . "'";
                            }
                            break;
                        default:
                            $sqlScript .= "'" . $row_content . "'";
                    }
                    if ($i < $fields_count - 1) {
                        $sqlScript .= ', ';
                    }
                }
                if (($r + 1) == $row_count || ($r % 400) == 399) {
                    $sqlScript .= ");\n\n";
                } else {
                    $sqlScript .= "),\n";
                }
                $r++;
            }
        }
    }
    $sqlScript .= "\n COMMIT; SET unique_checks=1; SET foreign_key_checks=1;";
    return $sqlScript;
}

function backupBackupSave($sqlScript, $file)
{
    $sqlScriptGz = gzencode($sqlScript, 9);
    $fileHandler = fopen($file, 'w+');
    fwrite($fileHandler, $sqlScriptGz);
    fclose($fileHandler);
}

function bistroBackupGenerate(): void
{
    global $database, $configDB, $config;
    $backupFile = $config['folder_backup'] . "backup" . time() . ".sql.gz";
    backupBackupSave(backupBackupGetData(), $backupFile);
    if (filesize($backupFile) > 1024) {
        DebuggerLog("BACKUP GENERATED: ".$config['folder_backup'].basename($backupFile)." [".round(filesize($backupFile) / 1024)." kB]","N");
        $backupTable = 'backup';
        $backupColumns = 'time, file, version';
        $backupValues = '"' . time() . '","' . $backupFile . '","' . $config['version'] . '"';
        if (DBcolumnExist("backups", "version")) { // 1.5.2> && <1.7.3
            $backupTable = 'backups';
            $backupColumns = 'time, file, version';
            $backupValues = '"' . time() . '","' . $backupFile . '","' . $config['version'] . '"';
        } elseif (DBtableExist("backups") && !DBcolumnExist("backup", "version")) { // <1.5.2
            $backupTable = 'backups';
            $backupColumns = 'time, file';
            $backupValues = '"' . time() . '","' . $backupFile . '"';
        }
        $backupSql = 'INSERT INTO ' . DB_PREFIX . $backupTable . ' (' . $backupColumns . ') VALUES(' . $backupValues . ')';
        mysqli_query($database, $backupSql);
        $tablelistSql = mysqli_query($database, "SHOW table status FROM " . $configDB['dbDatabase']);
        while ($tablelist = mysqli_fetch_row($tablelistSql)) {
            mysqli_query($database, "OPTIMIZE TABLE " . $tablelist[0]);
        }
    }
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
    global $database;
    $alter = 0;
    foreach ($table as $key => $value) {
        if (DBtableExist($key) == 0) {
            $sqlCreate = "CREATE TABLE " . DB_PREFIX . $key . " (" . $value . " int NOT NULL AUTO_INCREMENT PRIMARY KEY)";
            mysqli_query($database, $sqlCreate);
            if (DBtableExist($key) != 0) {
                DebuggerLog($file.': '.$sqlCreate,"N");
                $alter++;
            } else {
                DebuggerLog($file.': '.$sqlCreate, "E");
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
    global $database,$configDB;
    $alter = 0;
    foreach ($data as $old => $new) {
        if (DBtableExist($new) == 0 && DBtableExist($old) != 0) {
            $renameSql = "ALTER TABLE " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$old RENAME TO " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$new";
            mysqli_query($database, $renameSql);
            if (DBtableExist($new) != 0 && DBtableExist($old) == 0) {
                ($file.': '.$renameSql);
                $alter++;
            } else {
                DebuggerLog($file.': '.$renameSql,"N");
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
    global $database,$configDB;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            if (DBtableExist($table) != 0 && DBcolumnExist($table, $column) == 0) {
                $alterSql = "ALTER TABLE " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$table ADD COLUMN $column " . $data[$table][$column];
                mysqli_query($database, $alterSql);
                if (DBcolumnExist($table, $column) != 0) {
                    DebuggerLog($file.': '.$alterSql,"N");
                    $alter++;
                } else {
                    DebuggerLog($file.': '.$alterSql,"E");
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
    global $database,$configDB;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach (array_keys($data[$table]) as $column) {
            if (DBcolumnExist($table, $column) != 0) {  //existuje > updatnout
                $alterSql = "ALTER TABLE " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$table CHANGE $column " . $data[$table][$column];
                mysqli_query($database, $alterSql);
                if (($column == explode(' ', trim($data[$table][$column]))[0]) || DBcolumnExist($table, $column) == 0 && DBcolumnExist($table, explode(' ', trim($data[$table][$column]))[0]) != 0) {
                    DebuggerLog($file.': '.$alterSql,"N");
                    $alter++;
                } else {
                    DebuggerLog($file.': '.$alterSql,"E");
                }
            }
        }
    }

    return $alter;
}

function bistroMyisamToInnodb(): int
{
    global $database,$config;
    $alter = 0;
    $myisamDbsql = "select table_name from information_schema.tables tab
    where engine = 'MyISAM' and table_type = 'BASE TABLE' and table_schema not in ('information_schema', 'sys', 'performance_schema','mysql')
    and table_schema = 'bistro' order by table_schema, table_name";
    $myisamDbQuery = mysqli_query($database, $myisamDbsql);
    while ($mysqisamDb = mysqli_fetch_assoc($myisamDbQuery)) {
        $innoDbQuery = "ALTER TABLE ".$mysqisamDb['table_name']." engine='InnoDB'";
        DebuggerLog($mysqisamDb['table_name'].' converted from MyISAM to InnoDB',"N");
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
    global $database,$configDB;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $value) {
            $checkSql = "SHOW INDEX FROM " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$table WHERE index_type = 'FULLTEXT' and column_name='$value'";
            if (DBtableExist($table) != 0 && (mysqli_num_rows(mysqli_query($database, $checkSql)) == 0)) {
                $alterSql = "ALTER TABLE " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$table ADD FULLTEXT ($value)";
                mysqli_query($database, $alterSql);
                DebuggerLog($file.': '.$alterSql,"N");
                $alter++;
            }
        }
    }

    return $alter;
}

function bistroDBIndexAdd($data, $file = null): int
{
    global $database,$configDB;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $indexName => $column) {
            //SHOW INDEX FROM bistro.nw_person WHERE index_type = 'BTREE' and Key_name='side_spec_power_dead'
            $checkSql = "SHOW INDEX FROM " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$table WHERE index_type = 'BTREE' and Key_name='$indexName'";
            if (DBtableExist($table) != 0 && (mysqli_num_rows(mysqli_query($database, $checkSql)) == 0)) {
                $alterSql = "ALTER TABLE " . $configDB['dbDatabase'] . "." . DB_PREFIX . "$table ADD INDEX $indexName (" . implode(',', $column) . ")";
                mysqli_query($database, $alterSql);
                DebuggerLog($file.': '.$alterSql,"N");
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
    global $database,$configDB;
    $alter = 0;
    foreach (array_keys($data) as $table) {
        foreach ($data[$table] as $column) {
            if (DBcolumnExist($table, $column) != 0) {
                $dropSql = "ALTER TABLE " . $configDB['dbDatabase'] . "." . DB_PREFIX . $table . " DROP $column";
                mysqli_query($database, $dropSql);
                if (DBColumnExist($table, $column) == 0) {
                    DebuggerLog($file.': '.$dropSql,"N");
                    $alter++;
                } else {
                    DebuggerLog($file.': '.$dropSql,"E");
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
    foreach ($data as $value) {
        if (DBtableExist($value) != 0) {
            $dropSql = "DROP TABLE " . $config['dbDatabase'] . "." . DB_PREFIX . $value;
            mysqli_query($database, $dropSql);
            if (DBtableExist($value) == 0) {
                DebuggerLog($file.': '.$dropSql,"N");
                $alter++;
            } else {
                DebuggerLog($file.': '.$dropSql,"E");
            }
        }
    }

    return $alter;
}
