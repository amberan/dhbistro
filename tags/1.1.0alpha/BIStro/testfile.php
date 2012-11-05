<?php
require_once ('./inc/func_main.php');

// natazeni tabulky neprectenych zaznamu do promenne
$sql_r="SELECT * FROM ".DB_PREFIX."unread_".$usrinfo['id'];
$res_r=MySQL_Query($sql_r);
while ($unread[]=mysql_fetch_array($res_r));

// vyhledani tabulky v neprectenych zaznamech
function searchTable ($tablenum) { 
	global $unread;
	foreach ($unread as $record) {
   		if ($record['idtable'] == $tablenum)
       		return true;
		}
    	return false;
}

// vyhledani zaznamu v neprectenych zaznamech
function searchRecord ($tablenum, $recordnum) {
	global $unread;
	foreach ($unread as $record) {
		if ($record['idtable'] == $tablenum && $record['idrecord'] == $recordnum)
			return true;
	}
	return false;
}


if (searchTable(4) == true)
		echo 'Ano';

if (searchRecord(4,56) == true)
	echo 'Ano';

// http://stackoverflow.com/questions/6990855/php-check-if-value-and-key-exist-in-multidimensional-array

?>