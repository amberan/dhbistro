<?php
require_once ('./inc/func_main.php');

mysql_query ("SET NAMES  'utf8'");
$xsoubor="backup".date ("-d-m-Y").".sql";
$fsoubor="files/backups/".$xsoubor;
$sql_bck="INSERT INTO ".DB_PREFIX."backups VALUES('','".Time()."','".$xsoubor."')";
echo $sql_bck;
MySql_Query ($sql_bck);

function  zalohuj($db,$soubor=""){
	global $dbusr;
	
	function  keys($prefix,$array){
		if (empty($array)) { $pocet=0; } else {	$pocet = count ($array); }
		if (!isset($radky)) { $radky=''; }
		if ($pocet == 0)
			return ;
		for ($i = 0; $i<$pocet; $i++)
			$radky .= "`".$array[$i]."`".($i != $pocet-1 ? ",":"");
			return  ",\n".$prefix."(".$radky.")";
	}

	$sql = mysql_query ("SHOW table status  FROM ".$db);


	while ($data = mysql_fetch_row ($sql)){

	if (!isset($text)) { $text = '';}
	$text .= (empty ($text)?"":"\n\n")."--\n-- Struktura tabulky ".$data[0]."\n--\n\n\n";
    $text .= "CREATE TABLE `".$data[0]."`(\n";
    $sqll = mysql_query ("SHOW columns  FROM ".$data[0]);
    		$e = true;

	while ($dataa = mysql_fetch_row ($sqll)){
	if ($e) $e = false;
		else  $text .= ",\n";

		$null = ($dataa[2] == "NO")? "NOT NULL":"NULL";
		$default = !empty ($dataa[4])? " DEFAULT '".$dataa[4]."'":"";


		if ($default == " DEFAULT 'CURRENT_TIMESTAMP'") $default = " DEFAULT CURRENT_TIMESTAMP";
      if ($dataa[3] == "PRI") $PRI[] = $dataa[0];
      		if ($dataa[3] == "UNI") $UNI[] = $dataa[0];
      		if ($dataa[3] == "MUL") $MUL[] = $dataa[0];
      		$extra = !empty ($dataa[5])? " ".$dataa[5]:"";
      		$text .= "`$dataa[0]` $dataa[1] $null$default$extra";
	}
	if (!isset($UNI)) $UNI='';
	if (!isset($PRI)) $PRI='';
	if (!isset($MUL)) $MUL='';
	$primary = keys("PRIMARY KEY",$PRI);
	$unique = keys("UNIQUE KEY",$UNI);
    $mul = keys("INDEX",$MUL);
    $text .= $primary.$unique.$mul."\n) ENGINE=".$data[1]." COLLATE=".$data[14].";\n\n";
    unset ($PRI,$UNI,$MUL);

    		$text .= "--\n-- Data tabulky ".$data[0]."\n--\n\n";
	$query = mysql_query ("SELECT  * FROM ".$data[0]."");
	while ($fetch = mysql_fetch_row ($query)){
	$pocet_sloupcu = count ($fetch);

	for ($i = 0;$i < $pocet_sloupcu;$i++)
		@$values .= "'".mysql_escape_string ($fetch[$i])."'".($i < $pocet_sloupcu-1?",":"");
		$text .= "\nINSERT INTO `".$data[0]."` VALUES(".$values.");";
		unset ($values);
	}
	}

	if (!empty ($soubor)){
	$fp = @fopen ($soubor,"w+");
	$fw = @fwrite ($fp,$text);
	@fclose ($fp);
	}

	return  $text;
	}


//	MySQL_Query ("INSERT INTO ".DB_PREFIX."groups VALUES('','".mysql_real_escape_string(safeInput($_POST['title']))."','".mysql_real_escape_string($_POST['contents'])."','".Time()."','".$usrinfo['id']."','0','".$_POST['secret']."')");
//	$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','2','".$_POST['groupid']."','".$_POST['secret']."')";
//	MySQL_Query ($sql);
	zalohuj($dbusr,$fsoubor);

?>


