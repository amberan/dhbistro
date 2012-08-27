<?php
require_once ('./inc/func_main.php');

	$sql="SELECT id as 'id' FROM ".DB_PREFIX."users";
	$res=MySQL_Query ($sql);
	while ($rec_utc=MySQL_Fetch_Assoc($res)) {
		MySQL_Query ("CREATE TABLE nw_unread_".$rec_utc['id']." (id int NOT NULL PRIMARY KEY AUTO_INCREMENT, idtable int, idrecord int)");
		echo 'vysledek='.$rec_utc;
		print_r($rec_utc);
		debug_zval_dump($rec_utc);
		echo '<br />';
	}

?>