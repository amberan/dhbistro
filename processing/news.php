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
	$sql = "SELECT * FROM ".DB_PREFIX."persons WHERE $sqlwhere";
	$query = mysqli_query($database,$sql);
	if (mysqli_num_rows($query) > 0) {
		$person = mysqli_fetch_assoc();
	} else {
		$person = "Požadovaný záznam nebyl nalezen!";
	}
	return $person;
}

function newsList() {
	//vraci seznam novinek
	global $database, $usrinfo; 
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
    ".DB_PREFIX."users.login AS 'autor'
        FROM ".DB_PREFIX."news, ".DB_PREFIX."users
        WHERE ".DB_PREFIX."news.iduser=".DB_PREFIX."users.id $sql_where $fsql_cat
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

if (isset($_GET['newsdelete']) && $usrinfo['right_power']) {  //DELETE
	mysqli_query ($database,"UPDATE ".DB_PREFIX."news set deleted=1 where id='".$_GET['newsdelete']."'");
	if (mysqli_affected_rows($database) == 1) {
		auditTrail(5, 11, $_GET['newsdelete']);
		$_SESSION['message'] = "Aktualita odebrána.";
	} else {
		$_SESSION['message'] = "Aktualitu se nepodařilo odebrat.";
	}
} elseif (isset($_GET['newsdelete']))  {
	$_SESSION['message'] = "Pokus o odebrání aktuality zaznamenán.";
	unauthorizedAccess(5, 0, 0, $_GET['newsdelete']);
}

if (isset($_GET['newsadd']) && $usrinfo['right_power']) { //ADD
	if ($_POST['insertnews'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['nadpis']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['obsah']) && is_numeric($_POST['kategorie'])) {
		mysqli_query ($database,"INSERT INTO ".DB_PREFIX."news ( datum, iduser, kategorie, nadpis, obsah, deleted) VALUES('".Time()."','".$usrinfo['id']."','".$_POST['kategorie']."','".$_POST['nadpis']."','".$_POST['obsah']."',0)");
		if (mysqli_affected_rows($database) == 1) {
			auditTrail(5, 3, 0);
			$_SESSION['message'] = "Aktualita vložena.";
			unreadRecords (5,0);
		} else {
			$_SESSION['message'] = "Aktualitu se nepodařilo vložit.";
		}
	} else {
		$_SESSION['message'] = "Chyba při přidávání, ujistěte se, že jste vše provedli správně a máte potřebná práva.";
	}
}

?>