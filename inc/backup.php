<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);


/**
 * list all the present backups in database
 */
function backupListDatabase($empty = null)
{
    global $config,$database,$text;
    $backups_sql = "SELECT ".DB_PREFIX."backup.* FROM ".DB_PREFIX."backup ".sortingGet('backup');
    $backups_query = mysqli_query($database, $backups_sql);
    while (mysqli_num_rows($backups_query)> 0 && $backup_record = mysqli_fetch_assoc($backups_query)) {
        unset($backup);
        $file = end(explode("/", $backup_record['file']));
        if (file_exists($config['folder_backup'].$file) && !$empty) {
            $backup['file'] = "file/backup/".$backup_record['id'];
            $backup['datetime'] = webDateTime($backup_record['time']);
            $backup['version'] = $backup_record['version'];
            if (file_exists($config['folder_backup'].$file)) {
                $backup['filesize'] = human_filesize(filesize($config['folder_backup'].$file))."B";
            } else {
                $backup['filesize'] = $text['soubornenalezen'];
            }
        }
        $backup_array[] = $backup;
    }
    if (sizeof($backup_array) < 1) {
        $backup_array = false;
    }
    return $backup_array;
}

//vytvoreni zalohy
function backupData($soubor = "")
{
    global $database,$config;
    function keys($prefix, $array)
    {
        if (empty($array)) {
            $pocet = 0;
        } else {
            $pocet = count($array);
        }
        if (!isset($radky)) {
            $radky = '';
        }
        if ($pocet == 0) {
            return;
        }
        if ($prefix == 'FULLTEXT') {
            for ($i = 0; $i < $pocet; $i++) {
                $radky .= 'FULLTEXT (`'.$array[$i].'`)';
                if ($i < $pocet - 1) {
                    $radky .= ', ';
                }
            }

            return ",\n".$radky;
        }   //MULL
        for ($i = 0; $i < $pocet; $i++) {
            $radky .= "`".$array[$i]."`".($i != $pocet - 1 ? "," : "");
        }

        return  ",\n".$prefix."(".$radky.")";
    }
    //fast import
    $text = 'SET autocommit=0; SET unique_checks=0; SET foreign_key_checks=0;';
    $sql = mysqli_query($database, "SHOW table status  FROM ".$config['dbDatabase']);
    while ($data = mysqli_fetch_row($sql)) {
        if (!isset($text)) {
            $text = '';
        }
        $text .= (empty($text) ? "" : "\n\n")."--\n-- Struktura tabulky ".$data[0]."\n--\n\n\n";
        $text .= "CREATE TABLE `".$data[0]."`(\n";
        $sqll = mysqli_query($database, "SHOW columns  FROM ".$data[0]);
        $endline = true;
        while ($dataa = mysqli_fetch_row($sqll)) {
            if ($endline) {
                $endline = false;
            } else {
                $text .= ",\n";
            }
            $null = $dataa[2] == "NO" ? "NOT NULL" : "NULL";
            $default = !empty($dataa[4]) ? " DEFAULT '".$dataa[4]."'" : "";
            if ($default == " DEFAULT 'CURRENT_TIMESTAMP'") {
                $default = " DEFAULT CURRENT_TIMESTAMP";
            }
            if ($dataa[3] == "PRI") {
                $primary[] = $dataa[0];
            }
            if ($dataa[3] == "UNI") {
                $unique[] = $dataa[0];
            }
            if ($dataa[3] == "MUL") {
                $fulltext[] = $dataa[0];
            }
            $extra = !empty($dataa[5]) ? " ".$dataa[5] : "";
            $text .= "`$dataa[0]` $dataa[1] $null$default$extra";
        }
        if (!isset($unique)) {
            $unique = '';
        }
        if (!isset($primary)) {
            $primary = '';
        }
        if (!isset($fulltext)) {
            $fulltext = '';
        }
        $primarymary = keys("PRIMARY KEY", $primary);
        $uniqueque = keys("UNIQUE KEY", $unique);
        $fulltext = keys("FULLTEXT", $fulltext);
        $text .= $primarymary.$uniqueque.$fulltext."\n) ENGINE=".$data[1]." COLLATE=".$data[14].";\n\n";
        unset($primary,$unique,$fulltext);
        $text .= "--\n-- Data tabulky ".$data[0]."\n--\n\n";
        $query = mysqli_query($database, "SELECT  * FROM ".$data[0]."");
        while ($fetch = mysqli_fetch_row($query)) {
            $columnCount = count($fetch);
            $values = null;
            for ($i = 0; $i < $columnCount; $i++) {
                $values .= "'".mysqli_escape_string($database, $fetch[$i])."'".($i < $columnCount - 1 ? "," : "");
            }
            $text .= "\nINSERT INTO `".$data[0]."` VALUES(".$values.");";
            unset($values);
        }
    }
    //fast import
    $text .= 'COMMIT; SET unique_checks=1; SET foreign_key_checks=1;';
    if (!empty($soubor)) {
        $gztext = gzencode($text, 9);
        $filePointer = @fopen($soubor, "w+");
        @fwrite($filePointer, $gztext);
        @fclose($filePointer);
    }

    return  $text;
}

function backup_process(): void
{
    global $database, $config;
    $backupFile = $config['folder_backup']."backup".time().".sql.gz";
    backupData($backupFile);
    if (filesize($backupFile) > 1024) {
        Debugger::log("BACKUP GENERATED: ".$config['folder_backup'].basename($backupFile)." [".round(filesize($backupFile) / 1024)." kB]");
        $backupTable = 'backup';
        $backupColumns = 'time, file, version';
        $backupValues = '"'.time().'","'.$backupFile.'","'.$config['version'].'"';
        if (DBcolumnExist("backups", "version")) { // 1.5.2> && <1.7.3
            $backupTable = 'backups';
            $backupColumns = 'time, file, version';
            $backupValues = '"'.time().'","'.$backupFile.'","'.$config['version'].'"';
        } elseif (DBtableExist("backups") && !DBcolumnExist("backup", "version")) { // <1.5.2
            $backupTable = 'backups';
            $backupColumns = 'time, file';
            $backupValues = '"'.time().'","'.$backupFile.'"';
        }
        $backupSql = 'INSERT INTO '.DB_PREFIX.$backupTable.' ('.$backupColumns.') VALUES('.$backupValues.')';
        mysqli_query($database, $backupSql);
        $tablelistSql = mysqli_query($database, "SHOW table status FROM ".$config['dbDatabase']);
        while ($tablelist = mysqli_fetch_row($tablelistSql)) {
            mysqli_query($database, "OPTIMIZE TABLE ".$tablelist[0]);
        }
    }
}

function updatesToRun($file)
{
    global $lastBackup,$config;
    if (!isset($lastBackup['version'])) {
        $lastBackup['version']= '1.5.2';
    }
    return (strpos($file, 'php') && "update-".$lastBackup['version'].".php" < $file && "update-".$config['version'].".php" >= $file) ;
}

/**
 * returns all update*php files in sql that are never than last backup but at most current version
 */
function updatesToApply($updateFiles, $lastBackup)
{
    global $config;
    $files = array();
    foreach ($updateFiles as $file) {
        if (preg_match('/update-[0-9.]{1,}php/', $file) != null && version_compare($lastBackup, substr($file, 7, -4)) < 0
        && version_compare($config['version'], substr($file, 7, -4)) >= 0) {
            $files[] = $file;
        }
    }
    return $files;
}


if (DBtableExist("backup") || DBtableExist("backups")) {
    $lastBackupSql = "SELECT time,version FROM ".DB_PREFIX."backup ORDER BY time DESC LIMIT 1";
    if (DBcolumnExist("backups", "version")) { // 1.5.2> && <1.7.3
        $lastBackupSql = "SELECT time,version FROM ".DB_PREFIX."backups ORDER BY time DESC LIMIT 1";
    } elseif (DBtableExist("backups") && !DBcolumnExist("backup", "version")) { // <1.5.2
        $lastBackupSql = "SELECT time FROM ".DB_PREFIX."backups ORDER BY time DESC LIMIT 1";
    }
    $lastBackup = mysqli_fetch_assoc(mysqli_query($database, $lastBackupSql));
    $updatesToRun = updatesToApply(array_diff(scandir($_SERVER['DOCUMENT_ROOT']."/sql"), array('.', '..')), @$lastBackup['version']);
    if (round($lastBackup['time'], -5) < round(time(), -5) || sizeof($updatesToRun)>0) {
        backup_process();
        foreach ($updatesToRun as $key => $file) {
            require_once 'lib/update.php';
            bistroMyisamToInnodb();
            unset($tableCreate,$tableRename,$columnAdd,$columnAlter,$columnAddFulltext,$columnToMD,$rightsToUpdate,$convertTime,$columnDrop,$tableDrop);
            require_once $_SERVER['DOCUMENT_ROOT']."/sql/".$file;
            if (isset($tableCreate)) {
                bistroDBTableCreate($tableCreate, substr($file, 7, -4));
            }
            if (isset($tableRename)) {
                bistroDBTableRename($tableRename, substr($file, 7, -4));
            }
            if (isset($columnAdd)) {
                bistroDBColumnAdd($columnAdd, substr($file, 7, -4));
            }
            if (isset($columnAlter)) {
                bistroDBColumnAlter($columnAlter, substr($file, 7, -4));
            }
            if (isset($columnToMD)) {
                bistroDBColumnMarkdown($columnToMD, substr($file, 7, -4));
            }
            if (isset($columnAddFulltext)) {
                bistroDBFulltextAdd($columnAddFulltext, substr($file, 7, -4));
            }
            if (DBcolumnExist("user", "userPassword")) {
                bistroDBPasswordEncrypt();
            }
            if (isset($rightsToUpdate)) {
                bistroMigratePermissions($rightsToUpdate, substr($file, 7, -4));
            }
            if (isset($convertTime)) {
                bistroIntToTimestamp($convertTime, substr($file, 7, -4));
            }
            if (isset($columnDrop)) {
                bistroDBColumnDrop($columnDrop, substr($file, 7, -4));
            }
            if (isset($tableDrop)) {
                bistroDBTableDrop($tableDrop, substr($file, 7, -4));
            }
        }
    }
}
