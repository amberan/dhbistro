<?php
	require_once ('./inc/func_main.php');
	pageStart ('Přidáno');
	mainMenu (1);
	sparklets ('<a href="./index.php">aktuality</a> &raquo; <a href="./newnews.php">nová aktualita</a> &raquo; <strong>přidáno</strong>');
	if ($_POST['insertnews'] && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['nadpis']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['obsah']) && is_numeric($_POST['kategorie'])) {
		MySQL_Query ("INSERT INTO ".DB_PREFIX."news VALUES('','".Time()."','".$usrinfo['id']."','".$_POST['kategorie']."','".mysql_real_escape_string(safeInput($_POST['nadpis']))."','".mysql_real_escape_string($_POST['obsah'])."')");
		echo '<div id="obsah"><p>Aktualita vložena.</p></div>';
	} else {
		echo '<div id="obsah"><p>Chyba při přidávání, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	}
	pageEnd ();
?>
