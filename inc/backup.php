<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

//vytvoreni zalohy

function backupData($soubor = "")
{
    global $database,$config;
    function keys($prefix,$array)
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
    $sql = mysqli_query($database,"SHOW table status  FROM ".$config['dbdatabase']);
    while ($data = mysqli_fetch_row($sql)) {
        if (!isset($text)) {
            $text = '';
        }
        $text .= (empty($text) ? "" : "\n\n")."--\n-- Struktura tabulky ".$data[0]."\n--\n\n\n";
        $text .= "CREATE TABLE `".$data[0]."`(\n";
        $sqll = mysqli_query($database,"SHOW columns  FROM ".$data[0]);
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
        $primarymary = keys("PRIMARY KEY",$primary);
        $uniqueque = keys("UNIQUE KEY",$unique);
        $fulltext = keys("FULLTEXT",$fulltext);
        $text .= $primarymary.$uniqueque.$fulltext."\n) ENGINE=".$data[1]." COLLATE=".$data[14].";\n\n";
        unset($primary,$unique,$fulltext);
        $text .= "--\n-- Data tabulky ".$data[0]."\n--\n\n";
        $query = mysqli_query($database,"SELECT  * FROM ".$data[0]."");
        while ($fetch = mysqli_fetch_row($query)) {
            $columnCount = count($fetch);
            for ($i = 0; $i < $columnCount; $i++) {
                @$values .= "'".mysqli_escape_string($database,$fetch[$i])."'".($i < $columnCount - 1 ? "," : "");
            }
            $text .= "\nINSERT INTO `".$data[0]."` VALUES(".$values.");";
            unset($values);
        }
    }
    //fast import
    $text .= 'COMMIT; SET unique_checks=1; SET foreign_key_checks=1;';
    if (!empty($soubor)) {
        $gztext = gzencode($text, 9);
        $filePointer = @fopen($soubor,"w+");
        @fwrite($filePointer,$gztext);
        @fclose($filePointer);
    }

    return  $text;
}

function backup_process(): void
{
    global $_SERVER, $database, $config, $updateFile;
    $backupFile = $config['folder_backup']."backup".time().".sql.gz";
    backupData($backupFile);
    //pouze pokud je zaloha vetsi 2kB
    if (filesize($backupFile) > 1024) {
        Debugger::log("BACKUP GENERATED: ".$config['folder_backup'].basename($backupFile)." [".round(filesize($backupFile) / 1024)." kB]");
        $checkSql = mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."user' and column_name='sid'");
        if (mysqli_num_rows($checkSql) == 0) { //old backup 1.5.2>
            $backupSql = "INSERT INTO ".DB_PREFIX."backups (time, file) VALUES(".time().",'".$backupFile."')";
        } else { //new backup 1.5.2<
            $backupSql = "INSERT INTO ".DB_PREFIX."backup (time, file, version) VALUES(".time().",'".$backupFile."','".$config['version']."')";
        }
        mysqli_query($database,$backupSql);
        //optimizace tabulek
        $tablelistSql = mysqli_query($database,"SHOW table status FROM ".$config['dbdatabase']);
        while ($tablelist = mysqli_fetch_row($tablelistSql)) {
            mysqli_query($database,"OPTIMIZE TABLE ".$tablelist[0]);
        }
        // pokud existuje update soubor - spustit a prejmenovat
        if (file_exists($updateFile)) {
            Debugger::log("RUNNING UPDATE SCRIPT: /sql/".basename($updateFile));
            require_once $updateFile;
        }
    }
}

if (DBtableExist("backups")) {
    $checkSql = "SELECT time FROM ".DB_PREFIX."backups ORDER BY time DESC LIMIT 1";
}
if (DBtableExist("backup")) {
    $checkSql = "SELECT time FROM ".DB_PREFIX."backup ORDER BY time DESC LIMIT 1";
}
    $checkFetch = mysqli_fetch_assoc(mysqli_query($database,$checkSql));
    $backupLast = $checkFetch['time'];
    $updateFile = $_SERVER['DOCUMENT_ROOT']."/sql/update-".$config['version'].".php";
    if (round($backupLast,-5) < round(time(),-5) or file_exists($updateFile)) {
        backup_process();
    }
