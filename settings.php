<?php
$latteParameters['title'] = $text['nastaveni'];

if ((isset($_POST['userid']) AND isset($_POST['edituser']) AND !is_numeric($_REQUEST['timeout'])) AND ($usrinfo['id'] == $_POST['userid'] )) {
		$latteParameters['message'] = $text['timeoutnenicislo'];
	} else if (isset($_REQUEST['editsettings']) && ($_REQUEST['timeout'] > 1800 || $_REQUEST['timeout'] < 30)) {
		$latteParameters['message'] = $text['timeoutspatne'];
	} elseif (isset($_REQUEST['editsettings']) && isset($_REQUEST['soucheslo']) && $_REQUEST['soucheslo']<>'') {
		$currentpwd=mysqli_fetch_assoc (mysqli_query ($database,"SELECT pwd FROM ".DB_PREFIX."user WHERE sid='".$_SESSION['sid']."'"));
		if ($currentpwd['pwd'] == md5($_REQUEST['soucheslo'])) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET pwd=md5('".$_POST['heslo']."'), plan_md='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
			$latteParameters['message'] = $text['nastaveniulozeno'];
		} else {
			$latteParameters['message'] = $text['puvodniheslospatne'];
		}
	} elseif (isset($_REQUEST['editsettings'])) {
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET plan_md='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
		$latteParameters['message'] = $text['nastaveniulozeno'];
		read_user();
} 



$latteParameters['settings_timeout'] = $usrinfo['timeout'];
$latteParameters['settings_plan'] = stripslashes($usrinfo['plan_md']);

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'settings.latte', $latteParameters);
?>
