<?php
require_once ('./inc/func_main.php');

	MySQL_Query ("CREATE TABLE nw_unread (id int NOT NULL PRIMARY KEY AUTO_INCREMENT, idtable int, idrecord int, iduser int)");
        $sql="SELECT id as 'id' FROM ".DB_PREFIX."users";
	$res=MySQL_Query ($sql);
	while ($rec_utc=MySQL_Fetch_Assoc($res)) {
		MySQL_Query ("INSERT INTO nw_unread (idtable,idrecord) SELECT idtable,idrecord FROM nw_unread_".$rec_utc['id']);
                MySQL_Query ("UPDATE nw_unread SET iduser = ".$rec_utc['id']." WHERE iduser IS NULL;");
                MySQL_Query ("DROP TABLE nw_unread".$rec_utc['id']);
		echo 'vysledek='.$rec_utc;
		print_r($rec_utc);
		debug_zval_dump($rec_utc);
		echo '<br />';
	}

?>