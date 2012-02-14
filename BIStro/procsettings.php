<?php
	require_once ('./inc/func_main.php');
	if (isset($_POST['editsettings']) && !eregi ('^[[:blank:]]*$',$_POST['heslo'])) {
	  pageStart ('Uložení změn');
		mainMenu (6);
		sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
		MySQL_Query ("UPDATE ".DB_PREFIX."users SET pwd='".mysql_real_escape_string($_POST['heslo'])."', plan='".mysql_real_escape_string($_POST['plan'])."' WHERE id=".$usrinfo['id']);
		echo '<div id="obsah"><p>Nastavení uloženo.</p></div>';
		pageEnd ();
	} else {
	  if (isset($_POST['editsettings'])) {
		  pageStart ('Uložení změn');
			mainMenu (6);
			sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
?>
