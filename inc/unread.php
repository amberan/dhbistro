<?php

if (isset($_REQUEST['delallnew'])) {
	mysqli_query ($database,"DELETE FROM ".DB_PREFIX."unread WHERE iduser = ".$usrinfo['id']);
	$_SESSION['message'] = "Označeno jako přečtené";
}

//UNREAD duplicity remove
$duplicity = mysqli_query($database,"SELECT idtable,idrecord,iduser, COUNT(*) FROM ".DB_PREFIX."unread GROUP BY idtable,idrecord,iduser HAVING COUNT(*) > 1");
foreach ($duplicity as $record) {
	$duplicity_detail = mysqli_query($database,"SELECT * from ".DB_PREFIX."unread WHERE idtable=".$record['idtable']." AND idrecord=".$record['idrecord']." AND iduser=".$record['iduser']." limit 1,999;");
		foreach ($duplicity_detail as $delete) {
		mysqli_query($database,"DELETE FROM ".DB_PREFIX."unread WHERE id=".$delete['id']);
	}
}

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
	$sql_ur="SELECT ".DB_PREFIX."user.id as 'id', ".DB_PREFIX."user.right_power as 'right_power', ".DB_PREFIX."user.deleted as 'deleted' FROM ".DB_PREFIX."user";
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
	$sql_ur="SELECT ".DB_PREFIX."user.id as 'id', ".DB_PREFIX."user.right_power as 'right_power' FROM ".DB_PREFIX."user";
	$res_ur=mysqli_query ($database,$sql_ur);
	while ($rec_ur=mysqli_fetch_assoc ($res_ur)) {
		$srsql="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND idrecord=".$rid." AND iduser=".$rec_ur['id'];
		mysqli_query ($database,$srsql);
	}
}

// natazeni tabulky neprectenych zaznamu do promenne
if (isset($_SESSION['sid'])) {
		$sql_r="SELECT * FROM ".DB_PREFIX."unread WHERE iduser=".$usrinfo['id'];
		$sql_r="SELECT idtable, count(*) as count FROM ".DB_PREFIX."unread WHERE iduser=".$usrinfo['id']." GROUP BY idtable";
		$res_r=mysqli_query ($database,$sql_r);
		while ($unread[]=mysqli_fetch_array ($res_r));
}

// vyhledani zaznamu v neprectenych zaznamech - cases, groups, persons, reports, symbols,
function searchRecord ($tablenum, $recordnum) {
	global $database,$unread,$usrinfo;
	$sql_r="SELECT * FROM ".DB_PREFIX."unread WHERE iduser=".$usrinfo['id']." and idtable=".$tablenum." and idrecord=".$recordnum;
	$res_r=mysqli_num_rows(mysqli_query ($database,$sql_r));
	if ($res_r > 0) { 
		return true;
	} else {
		return false;
	}
}

// vyhledani tabulky v neprectenych zaznamech
function searchTable ($tablenum) { 
    global $database,$unread;
    
	foreach ((array) $unread as $record) {
		if ($record['idtable'] == $tablenum) {
			return $record['count'];
		}
	}
	return false;
}
?>
