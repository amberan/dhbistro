<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	if (isset($_SESSION['sid'])) {
		auditTrail(5, 3, 0);
	}
	pageStart ('Přidáno');
	mainMenu (1);
	sparklets ('<a href="./index.php">aktuality</a> &raquo; <a href="./newnews.php">nová aktualita</a> &raquo; <strong>přidáno</strong>');
	if ($_POST['insertnews'] && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['nadpis']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['obsah']) && is_numeric($_POST['kategorie'])) {
		mysqli_query ($database,"INSERT INTO ".DB_PREFIX."news VALUES('','".Time()."','".$usrinfo['id']."','".$_POST['kategorie']."','".mysqli_real_escape_string ($database,safeInput($_POST['nadpis']))."','".mysqli_real_escape_string ($database,$_POST['obsah'])."')");
		unreadRecords (5,0);
		echo '<div id="obsah"><p>Aktualita vložena.</p></div>';
	} else {
		echo '<div id="obsah"><p>Chyba při přidávání, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	}
	pageEnd ();
?>
