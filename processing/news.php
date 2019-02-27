<?php
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