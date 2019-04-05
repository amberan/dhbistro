<?php
/* example usage:
$searchPerson = personList(" name LIKE '%pepa%' OR surname LIKE '%pepa%','datum DESC' );
if (is_string($searchPerson)) {
	$latteParameters['searchPersonMessage'] = $searchPerson; 
} else {
	$latteParameters['searchPersonList'] = $searchPerson;
}
*/


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
	$query = mysqli_query($database,$sql);
	if (mysqli_num_rows($query) > 0) {
		$person = mysqli_fetch_assoc();
	} else {
		$person = "Požadovaný záznam nebyl nalezen!";
	}
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
	if (mysqli_num_rows($query)> 0) {
		while ($person = mysqli_fetch_assoc ($query)) {
			$personList[] = $person;
		}
	} else {
		$personList = "Výpis neobsahuje žádné položky!";
	}
	return $personList;
}

?>