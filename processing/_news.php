<?php

/* function newsRead($newsid) {
	//vraci jednu osobu; aplikuje prava podle uzivatele
	global $database, $usrinfo; 
	$sqlwhere = " id = $personid AND secret <=".$usrinfo['right_power'];
	if (isset($usrinfo['right_admin']) AND $usrinfo['right_admin'] > 0) {
		$sqlwhere .= " AND deleted = 1";
	} else {
		$sqlwhere .= " AND deleted = 0";
	}
	$sql = "SELECT * FROM ".DB_PREFIX."person WHERE $sqlwhere";
	$query = mysqli_query($database,$sql);
	if (mysqli_num_rows($query) > 0) {
		$person = mysqli_fetch_assoc();
	} else {
		$person = "Požadovaný záznam nebyl nalezen!";
	}
	return $person;
}

function newsList($fsql_cat = 1, $fsql_sort = 1) {
	//vraci seznam novinek
    global $database, $usrinfo; 
    $sqlwhere = $fsql_cat;
	if (isset($usrinfo['right_admin']) AND $usrinfo['right_admin'] > 0) {
		$sqlwhere .= " AND deleted = 1";
	} else {
		$sqlwhere .= " AND deleted = 0";
	}
	$sql = "SELECT
    ".DB_PREFIX."news.id AS 'id',
    ".DB_PREFIX."news.datum AS 'datum',
    ".DB_PREFIX."news.nadpis AS 'nadpis',
    ".DB_PREFIX."news.obsah AS 'obsah',
    ".DB_PREFIX."news.deleted AS 'deleted',
    ".DB_PREFIX."news.kategorie AS 'kategorie',
    ".DB_PREFIX."user.login AS 'autor'
        FROM ".DB_PREFIX."news, ".DB_PREFIX."user
        WHERE ".DB_PREFIX."news.iduser=".DB_PREFIX."user.id $sql_where $fsql_cat
        ORDER BY $fsql_sort LIMIT 10";
	$query = mysqli_query($database,$sql);
	if (mysqli_num_rows($query)> 0) {
		while ($person = mysqli_fetch_assoc ($query)) {
			$personList[] = $person;
		}
	} else {
		$personList = "Výpis neobsahuje žádné položky!";
	}
	return $personList;
}*/


?>
