<?php

	if (isset($_REQUEST['editsettings']) && !is_numeric($_REQUEST['timeout'])) {
		$_SESSION['message'] = "Timeout není číslo, nastavení nebylo uloženo.";
	} else if (isset($_REQUEST['editsettings']) && ($_REQUEST['timeout'] > 1800 || $_REQUEST['timeout'] < 30)) {
		$_SESSION['message'] = "Timeout nesouhlasí, je buď příliš malý nebo příliš velký.";
	} elseif (isset($_REQUEST['editsettings']) && isset($_REQUEST['soucheslo']) && $_REQUEST['soucheslo']<>'') {
		$currentpwd=mysqli_fetch_assoc (mysqli_query ($database,"SELECT pwd FROM ".DB_PREFIX."users WHERE sid='".$_SESSION['sid']."'"));
		if ($currentpwd['pwd'] == md5($_REQUEST['soucheslo'])) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET pwd=md5('".$_POST['heslo']."'), plan='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
			$_SESSION['message'] = "Nastavení s novým heslem uloženo.";
		} else {
			$_SESSION['message'] = "Nesouhlasí staré heslo, nastavení nebylo uloženo.";
		}
	} elseif (isset($_REQUEST['editsettings'])) {
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET plan='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
		$_SESSION['message'] = "Nastavení uloženo.";
	}
?>
