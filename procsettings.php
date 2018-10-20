<?php
	require_once ('./inc/func_main.php');
	if (isset($_POST['editsettings']) && !is_numeric($_POST['timeout'])) {
		pageStart ('Uložení změn');
		mainMenu (6);
		sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Timeout není číslo, nastavení nebylo uloženo.</p></div>';
		pageEnd ();
	} else if (isset($_POST['editsettings']) && ($_POST['timeout'] > 1800 || $_POST['timeout'] < 30)) {
		pageStart ('Uložení změn');
		mainMenu (6);
		sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Timeout nesouhlasí, je buď příliš malý nebo příliš velký.</p></div>';
		pageEnd ();
	} else {
		if (isset($_POST['editsettings']) && isset($_POST['soucheslo']) && $_POST['soucheslo']<>'') {
			pageStart ('Uložení změn');
			$currentpwd=mysqli_fetch_assoc (mysqli_query ($database,"SELECT pwd FROM ".DB_PREFIX."users WHERE id=".$usrinfo['id']));
			if ($currentpwd['pwd'] == $_POST['soucheslo']) {
				mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET pwd='".mysqli_real_escape_string ($database,$_POST['heslo'])."', plan='".mysqli_real_escape_string ($database,$_POST['plan'])."', timeout='".$_POST['timeout']."' WHERE id=".$usrinfo['id']);
				pageStart ('Uložení změn');
				mainMenu (6);
				sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
				echo '<div id="obsah"><p>Nastavení s novým heslem uloženo.</p></div>';
				pageEnd ();
			} else {
				pageStart ('Uložení změn');
				mainMenu (6);
				sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
				echo '<div id="obsah"><p>Nesouhlasí staré heslo, nastavení nebylo uloženo.</p></div>';
				pageEnd ();
			}
		} else {
	  	if (isset($_POST['editsettings'])) {
			pageStart ('Uložení změn');
				mainMenu (6);
				sparklets ('<a href="./settings.php">nastavení</a> &raquo; <strong>uložení změn</strong>');
				mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET plan='".mysqli_real_escape_string ($database,$_POST['plan'])."', timeout='".$_POST['timeout']."' WHERE id=".$usrinfo['id']);
				echo '<div id="obsah"><p>Nastavení uloženo.</p></div>';
				pageEnd ();
			}
		}
	}
?>
