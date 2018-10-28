<?php



// zaznam do tabulek neprectenych
function unreadRecords ($tablenum,$rid) {
	global $database,$usrinfo, $_POST;
	$secret=0;
	if (isset($_POST['secret'])) {
		$secret=$_POST['secret'];
	} 
	if (isset($_POST['nsecret'])) {
		$secret=$_POST['nsecret'];
	}
	$sql_ur="SELECT ".DB_PREFIX."users.id as 'id', ".DB_PREFIX."users.right_power as 'right_power', ".DB_PREFIX."users.deleted as 'deleted' FROM ".DB_PREFIX."users";
	$res_ur=mysqli_query ($database,$sql_ur);
	while ($rec_ur=mysqli_fetch_assoc ($res_ur)) {
		if ($secret > 0 && $rec_ur['deleted'] <> 1) {
			if ($rec_ur['id'] <> $usrinfo['id'] && $rec_ur['right_power'] > 0) {
				$srsql="INSERT INTO ".DB_PREFIX."unread (idtable, idrecord, iduser) VALUES('".$tablenum."', '".$rid."', '".$rec_ur['id']."')";
				mysqli_query ($database,$srsql);
			}
		} else if ($secret == 0 && $rec_ur['deleted'] <> 1) {
			if ($rec_ur['id'] <> $usrinfo['id']) {
				$srsql="INSERT INTO ".DB_PREFIX."unread (idtable, idrecord, iduser) VALUES('".$tablenum."', '".$rid."', '".$rec_ur['id']."')";
				mysqli_query ($database,$srsql);
			}
		}
	}
}

// vymaz z tabulek neprectenych pri precteni
function deleteUnread ($tablenum,$rid) {
	global $database,$usrinfo;
	if ($rid<>'none') {
		$sql_ur="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND idrecord=".$rid." AND iduser=".$usrinfo['id'];
	} else {
		$sql_ur="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND iduser=".$usrinfo['id'];
	}
	mysqli_query ($database,$sql_ur);
}

// vymaz z tabulek neprectenych pri smazani zaznamu
function deleteAllUnread ($tablenum,$rid) {
	global $database;
	$sql_ur="SELECT ".DB_PREFIX."users.id as 'id', ".DB_PREFIX."users.right_power as 'right_power' FROM ".DB_PREFIX."users";
	$res_ur=mysqli_query ($database,$sql_ur);
	while ($rec_ur=mysqli_fetch_assoc ($res_ur)) {
		$srsql="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND idrecord=".$rid." AND iduser=".$rec_ur['id'];
		mysqli_query ($database,$srsql);
	}
}

// natazeni tabulky neprectenych zaznamu do promenne
if (isset($_SESSION['sid'])) {
		$sql_r="SELECT * FROM ".DB_PREFIX."unread WHERE iduser=".$usrinfo['id'];
		$res_r=mysqli_query ($database,$sql_r);
		while ($unread[]=mysqli_fetch_array ($res_r));
}

?>