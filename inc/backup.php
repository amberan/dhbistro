<?php

use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

//vytvoreni zalohy
global $database,$config;

function  backup_data($soubor = "")
{
    global $database,$config;
    function  keys($prefix,$array)
    {
        if (empty($array)) {
            $pocet = 0;
        } else {
            $pocet = count ($array);
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
        } else { //MULL
            for ($i = 0; $i < $pocet; $i++) {
                $radky .= "`".$array[$i]."`".($i != $pocet - 1 ? "," : "");
            }

            return  ",\n".$prefix."(".$radky.")";
        }
    }
    //fast import
    $text = 'SET autocommit=0; SET unique_checks=0; SET foreign_key_checks=0;';
    $sql = mysqli_query ($database,"SHOW table status  FROM ".$config['dbdatabase']);
    while ($data = mysqli_fetch_row ($sql)) {
        if (!isset($text)) {
            $text = '';
        }
        $text .= (empty ($text) ? "" : "\n\n")."--\n-- Struktura tabulky ".$data[0]."\n--\n\n\n";
        $text .= "CREATE TABLE `".$data[0]."`(\n";
        $sqll = mysqli_query ($database,"SHOW columns  FROM ".$data[0]);
        $e = true;
        while ($dataa = mysqli_fetch_row ($sqll)) {
            if ($e) {
                $e = false;
            } else {
                $text .= ",\n";
            }
            $null = ($dataa[2] == "NO") ? "NOT NULL" : "NULL";
            $default = !empty ($dataa[4]) ? " DEFAULT '".$dataa[4]."'" : "";
            if ($default == " DEFAULT 'CURRENT_TIMESTAMP'") {
                $default = " DEFAULT CURRENT_TIMESTAMP";
            }
            if ($dataa[3] == "PRI") {
                $PRI[] = $dataa[0];
            }
            if ($dataa[3] == "UNI") {
                $UNI[] = $dataa[0];
            }
            if ($dataa[3] == "MUL") {
                $MUL[] = $dataa[0];
            }
            $extra = !empty ($dataa[5]) ? " ".$dataa[5] : "";
            $text .= "`$dataa[0]` $dataa[1] $null$default$extra";
        }
        if (!isset($UNI)) {
            $UNI = '';
        }
        if (!isset($PRI)) {
            $PRI = '';
        }
        if (!isset($MUL)) {
            $MUL = '';
        }
        $primary = keys("PRIMARY KEY",$PRI);
        $unique = keys("UNIQUE KEY",$UNI);
        $mul = keys("FULLTEXT",$MUL);
        $text .= $primary.$unique.$mul."\n) ENGINE=".$data[1]." COLLATE=".$data[14].";\n\n";
        unset ($PRI,$UNI,$MUL);
        $text .= "--\n-- Data tabulky ".$data[0]."\n--\n\n";
        $query = mysqli_query ($database,"SELECT  * FROM ".$data[0]."");
        while ($fetch = mysqli_fetch_row ($query)) {
            $pocet_sloupcu = count ($fetch);
            for ($i = 0;$i < $pocet_sloupcu;$i++) {
                @$values .= "'".mysqli_escape_string ($database,$fetch[$i])."'".($i < $pocet_sloupcu - 1 ? "," : "");
            }
            $text .= "\nINSERT INTO `".$data[0]."` VALUES(".$values.");";
            unset ($values);
        }
    }
    //fast import
    $text .= 'COMMIT; SET unique_checks=1; SET foreign_key_checks=1;';
    if (!empty ($soubor)) {
        $gztext = gzencode($text, 9);
        $fp = @fopen ($soubor,"w+");
        $fw = @fwrite ($fp,$gztext);
        @fclose ($fp);
    }

    return  $text;
}

function backup_process()
{
    global $_SERVER, $database, $config, $update_file;
    $backup_file = $_SERVER['DOCUMENT_ROOT'].$config['folder_backup']."backup".time().".sql.gz";
    backup_data($backup_file);
    //pouze pokud je zaloha vetsi 2kB
    if (filesize($backup_file) > 1024) {
        Debugger::log("BACKUP GENERATED: ".$config['folder_backup'].basename($backup_file)." [".round((filesize($backup_file) / 1024))." kB]");
        $check_sql = mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."user' and column_name='sid'");
        if (mysqli_num_rows($check_sql) == 0) { //old backup 1.5.2>
            $sql_bck = "INSERT INTO ".DB_PREFIX."backup (time, file) VALUES(".Time().",'".$backup_file."')";
        } else { //new backup 1.5.2<
            $sql_bck = "INSERT INTO ".DB_PREFIX."backup (time, file, version) VALUES(".Time().",'".$backup_file."','".$config['version']."')";
        }
        mysqli_query ($database,$sql_bck);
        //optimizace tabulek
        $tablelist_sql = mysqli_query($database,"SHOW table status FROM ".$config['dbdatabase']);
        while ($tablelist = mysqli_fetch_row($tablelist_sql)) {
            mysqli_query($database,"OPTIMIZE TABLE ".$tablelist[0]);
        }
        // pokud existuje update soubor - spustit a prejmenovat
        if (file_exists($update_file)) {
            Debugger::log("RUNNING UPDATE SCRIPT: /sql/".basename($update_file));
            require_once($update_file);
        }
        //odmazani UNREAD pro smazane uzivatele
        $deletedusers_sql = mysqli_query($database,"select id from ".DB_PREFIX."user where deleted=1");
        while ($deletedusers = mysqli_fetch_row($deletedusers_sql)) {
            mysqli_query ($database,"DELETE FROM ".DB_PREFIX."unread WHERE iduser = ".$deletedusers[0]);
        }
    }
}

	$sql_check = "SELECT time FROM ".DB_PREFIX."backup ORDER BY time DESC LIMIT 1";
	$fetch_check = mysqli_fetch_assoc (mysqli_query ($database,$sql_check));
	$last_backup = $fetch_check['time'];
	$update_file = $_SERVER['DOCUMENT_ROOT']."/sql/update-".$config['version'].".php";
	if (round($last_backup,-5) < round(time(),-5) or file_exists($update_file)) {
	    backup_process();
	}
?>
