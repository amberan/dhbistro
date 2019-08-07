<?php
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

// zpracovani login formulare
if (isset($_REQUEST['logmein'])) { 
    $logres=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."user WHERE login='".$_REQUEST['loginname']."' AND pwd=md5('".$_POST['loginpwd']."') and deleted=0 and suspended=0 "); 
	if($logrec=mysqli_fetch_array ($logres)) {
		$_SESSION['sid']=session_id(); 
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user set sid='' where sid='".$_SESSION['sid']."'");
		Debugger::log("LOGIN SUCCESS: ".$_REQUEST['loginname']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET sid='".$_SESSION['sid']."', lastlogon=".Time().", ip='".$_SERVER['REMOTE_ADDR']."', user_agent='".$_SERVER['HTTP_USER_AGENT']."' WHERE id=".$logrec['id']);
	} else {
		Debugger::log("LOGIN FAILED: ".$_REQUEST['loginname']);
	}
}

// prihlasi uzivatele a nacte jej do promene
function read_user() { 
	global $database, $_SESSION, $usrinfo;
	$usersql = "SELECT id, login, pwd, idperson, lastlogon as 'lastaction', right_power, right_text, right_org, right_aud, timeout, ip as 'currip', plan, sid
	FROM ".DB_PREFIX."user 
	WHERE deleted=0 AND suspended=0 AND ".DB_PREFIX."user.sid ='".$_SESSION['sid']."' AND user_agent='".$_SERVER['HTTP_USER_AGENT']."'";
	if ($usrinfo=mysqli_fetch_assoc (mysqli_query ($database,$usersql))) {
		$_SESSION['inactiveallowance'] = $usrinfo['timeout'];
	} else {
		unset($_SESSION['sid']);
	}
}

// info o uzivateli
if (isset($_SESSION['sid'])) {
	read_user();
}

// *** FORCED LOGOUT - timeout, unallowed pages
function logout_forced($msg) {
	global $database,$_SESSION;
	if (isset($_SESSION['sid'])) {
		Debugger::log("FORCED LOGOUT: ".$msg." - ".$_SESSION['sid']);
		if (isset($_SESSION['sid'])) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."user set sid=null WHERE sid=".$_SESSION['sid']);
		}
    }
    session_regenerate_id();
    session_destroy();
	session_start();
	if (isset($msg)) { 
		$_SESSION['message'] .= $msg;
	}
	$_SESSION['timestamp'] = time();
	Header ('location: login.php');
	exit();
}

$config['page_free'] = array_map(function($val) { global $config; return $config['page_prefix'].$val; }, $config['page_free']);   
$page_current = explode("/",$_SERVER['REQUEST_URI']);
if (
	(isset($_SESSION['inactiveallowance']) AND 
	(time() > ($_SESSION['timestamp'] +
	 $_SESSION['inactiveallowance'])))
	and isset($_SESSION['sid']) and !isset($_REQUEST['logmein'])) { //timeout
		logout_forced("Z bezpečnostních důvodů jste byl odhlášen!");
} elseif (!isset($_SESSION['sid']) AND in_array(end($page_current),$config['page_free']) == false) {
		logout_forced(null);
}
$_SESSION['timestamp'] = time();
?>