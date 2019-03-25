<?php

function personRead($personid) {
	//vraci jednu osobu; aplikuje prava podle uzivatele
	global $database, $usrinfo; 
	$sqlwhere = " id = $personid AND secret <=".$usrinfo['right_power'];
	if ($usrinfo['right_admin'] > 0) {
		$sqlwhere .= " AND deleted = 1";
	} else {
		$sqlwhere .= " AND deleted = 0";
	}
	$sql = "SELECT * FROM ".DB_PREFIX."persons WHERE $sqlwhere";
	$person = mysqli_fetch_assoc(mysqli_query($database,$sql));
	return $person;
}

function personList($where = 1, $order = 1) {
	//vraci seznam osob; aplikuje SQL WHERE podle $where a prava podle uzivatele; radi podle $order
	global $database, $usrinfo; 
	$sqlwhere = " $where AND secret <=".$usrinfo['right_power'];
	if ($usrinfo['right_admin'] > 0) {
		$sqlwhere .= " AND deleted = 1";
	} else {
		$sqlwhere .= " AND deleted = 0";
	}
	$sql = "SELECT * FROM ".DB_PREFIX."persons WHERE $sqlwhere ORDER BY $order";
	$query = mysqli_query($database,$sql);
	while ($person = mysqli_fetch_assoc ($query)) {
		$personList[] = $person;
	}
	return $personList;
}

?>