<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');

function randomPassword() {
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$pass = array(); 
	$alphaLength = strlen($alphabet) - 1; 
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); 
}

// smazat uzivatele
if (isset($_REQUEST['user_delete']) && is_numeric($_REQUEST['user_delete'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 1, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_delete']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET deleted=1 WHERE id=".$_REQUEST['user_delete']);
		$_SESSION['message'] = "Uživatelský účet odstraněn!";
	}
}// zamknout uzivatele
elseif (isset($_REQUEST['user_lock']) && is_numeric($_REQUEST['user_lock'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_lock']);
		error_log("UPDATE ".DB_PREFIX."users SET deleted=1 WHERE id=".$_REQUEST['user_lock']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET suspended=1 WHERE id=".$_REQUEST['user_lock']);
		$_SESSION['message'] = "Uživatelský účet zablokován!";
	}
}// odemknout uzivatele
elseif (isset($_REQUEST['user_unlock']) && is_numeric($_REQUEST['user_unlock'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_unlock']);
		error_log("UPDATE ".DB_PREFIX."users SET deleted=1 WHERE id=".$_REQUEST['user_unlock']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET suspended=0 WHERE id=".$_REQUEST['user_unlock']);
		$_SESSION['message'] = "Uživatelský účet odblokován!";
	}
}// reset hesla uzivatele
elseif (isset($_REQUEST['user_reset']) && is_numeric($_REQUEST['user_reset'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
		$_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	} else {
		$newpassword = randomPassword();
		auditTrail(8, 11, $_REQUEST['user_reset']);
		error_log("UPDATE ".DB_PREFIX."users SET pwd=md5('".$newpassword."') WHERE id=".$_REQUEST['user_reset']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET pwd=md5('".$newpassword."') WHERE id=".$_REQUEST['user_reset']);
		$_SESSION['message'] = "Nové heslo nastaveno: ".$newpassword; 
	}
}

// vytvorit uzivatele
if (isset($_POST['insertuser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['heslo']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	$ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."users WHERE UCASE(login)=UCASE('".$_POST['login']."')");
	if (mysqli_num_rows ($ures)) {
		$_SESSION['message']= "Uživatel již existuje, změňte jeho jméno.";
	} else {
		mysqli_query ($database,"INSERT INTO ".DB_PREFIX."users (login,pwd,right_power,right_text,timeout) VALUES('".$_POST['login']."','md5(".$_POST['heslo'].")','".$_POST['power']."','".$_POST['texty']."','600')");
		$uidarray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."users WHERE UCASE(login)=UCASE('".$_POST['login']."')"));
		if ($usrinfo['right_aud'] > 0) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users set right_aud='".$_POST['auditor']."' WHERE id=".$uidarray['id']);
		}
		if ($usrinfo['right_org'] > 0) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users set right_org='".$_POST['organizator']."' WHERE id=".$uidarray['id']);
		}
		auditTrail(8, 3, $uidarray['id']);
		$_SESSION['message']= "Uživatel ".$_POST['login']." vytvořen.";
	}
} else {
  	if (isset($_POST['insertuser'])) {
$_SESSION['message']= "Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.";
	}
}

// upravit uzivatele
if (isset($_POST['userid']) && isset($_POST['edituser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	auditTrail(8, 2, $_POST['userid']);
	$ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."users WHERE UCASE(login)=UCASE('".$_POST['login']."') AND id<>".$_POST['userid']);
	if (mysqli_num_rows ($ures)) {
		$_SESSION['message']= "Uživatel již existuje, změňte jeho jméno.";
	} else {
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET login='".$_POST['login']."', right_power='".$_POST['power']."', right_text='".$_POST['texty']."', idperson='".$_POST['idperson']."' WHERE id=".$_POST['userid']);
		if ($usrinfo['right_aud'] > 0) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users set right_aud='".$_POST['auditor']."' WHERE id=".$_POST['userid']);
			debug ("update audit");
		}
		if ($usrinfo['right_org'] > 0) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users set right_org='".$_POST['organizator']."' WHERE id=".$_POST['userid']);
	
		}
		$_SESSION['message']= "Uživatel ".$_POST['login']." upraven.";
	}
} else {
	if (isset($_POST['edituser'])) {
		$_SESSION['message']= "Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.";
	}
}
?>
