<?php

function personRead($personid) {
	//one person array
	global $database, $usrinfo, $text; 
	$sqlwhere = " id = $personid AND secret <=".$usrinfo['right_power'];
	if (isset($usrinfo['right_admin']) AND $usrinfo['right_admin'] > 0) {
		$sqlwhere .= " AND deleted = 1";
	} else {
		$sqlwhere .= " AND deleted = 0";
	}
	$sql = "SELECT * FROM ".DB_PREFIX."persons WHERE $sqlwhere";
	$query = mysqli_query($database,$sql);
	if (mysqli_num_rows($query) > 0) {
       $person = mysqli_fetch_assoc($query);
       unset ($person['deleted']);
	} else {
		$person[] = $text['zaznamnenalezen'];
    }
	return $person;
}

function personList($where = 1, $order = 1) {
	//person list array, filtere by $where, sorted by $order
    global $database, $usrinfo, $text;
    if (strlen($where) < 1) { $where = 1;}
    if (strlen($order) < 1) { $order = 1;}
	$sqlwhere = " $where AND secret <=".$usrinfo['right_power'];
	if (isset($usrinfo['right_admin']) AND $usrinfo['right_admin'] > 0) {
		$sqlwhere .= " AND deleted = 1";
	} else {
		$sqlwhere .= " AND deleted = 0";
	}
	$sql = "SELECT * FROM ".DB_PREFIX."persons WHERE $sqlwhere ORDER BY $order";
	$query = mysqli_query($database,$sql);
	if (mysqli_num_rows($query)> 0) {
		while ($person = mysqli_fetch_assoc ($query)) {
            unset ($person['deleted']);
			$personList[] = $person;
		}
	} else {
		$personList[] = $text['prazdnyvypis'];
	}
	return $personList;
}

?>