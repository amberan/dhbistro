<?php


// *** FORCED LOGOUT
function logout_forced($msg) {
	global $database,$_SESSION;
	mysqli_query ($database,"UPDATE ".DB_PREFIX."users set sid=null WHERE sid=".$sid);
	mysqli_query ($database,"DELETE FROM ".DB_PREFIX."loggedin WHERE iduser=".$logrec['id']); //odstranit v 1.5.4
	session_destroy(); 
	session_start();
	if ($msg != null) { $_SESSION['message'] = $msg; }
	$_SESSION['timestamp'] = time();
	Header ('location: login.php');
}
if (!isset($_SESSION['inactiveallowance'])) { $_SESSION['inactiveallowance'] = $config['timeout'];}
if (((isset($_SESSION['user_agent']) and $_SESSION['user_agent'] != mysqli_real_escape_string($database,$_SERVER['HTTP_USER_AGENT']))) //zmena agenta
	or (!isset($_REQUEST['logmein']) and ((time() - $_SESSION['timestamp']) > $_SESSION['inactiveallowance']))) { //timeout
		logout_forced("Z bezpečnostních důvodů jste byl odhlášen!");
}
$_SESSION['timestamp'] = time();


// prihlaseni
if (isset($_REQUEST['logmein'])) { 
	$logres=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."users WHERE login='".$_REQUEST['loginname']."' AND pwd='".$_REQUEST['loginpwd']."'");
	if ($logrec=mysqli_fetch_array ($logres)) {
		if ($logrec['sid']) { //new login
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET sid='$sid', lastlogon=".Time().", ip='".$_SERVER['REMOTE_ADDR']."', user_agent='".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."' WHERE id=".$logrec['id']);
			$_SESSION['sid'] = $sid;
		} else { //fallback loggedin odstranit v 1.5.4
			mysqli_query ($database,"DELETE FROM ".DB_PREFIX."loggedin WHERE iduser=".$logrec['id']);
			$sid=md5(uniqid(rand()));
				mysqli_query ($database,"INSERT INTO ".DB_PREFIX."loggedin VALUES('".$logrec['id']."','".Time()."','".$sid."','".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."','".$_SERVER['REMOTE_ADDR']."')");
				mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET lastlogon=".Time().", ip='' WHERE id=".$logrec['id']);
			$_SESSION['sid']=$sid;
		}
    } 
}
// info o uzivateli
if (isset($_SESSION['sid'])) {
	if ($logrec['sid']) { //new login
		$sql = "SELECT
		".DB_PREFIX."users.id AS 'id',
		".DB_PREFIX."users.login AS 'login',
		".DB_PREFIX."users.pwd AS 'pwd',
		".DB_PREFIX."users.idperson AS 'idperson',
		".DB_PREFIX."users.lastlogon AS 'lastlogon',
		".DB_PREFIX."users.right_power AS 'right_power',
		".DB_PREFIX."users.right_text AS 'right_text',
		".DB_PREFIX."users.right_org AS 'right_org',
		".DB_PREFIX."users.right_aud AS 'right_aud',
		".DB_PREFIX."users.timeout AS 'timeout',
		".DB_PREFIX."users.ip AS 'ip',
		".DB_PREFIX."users.plan AS 'plan',
		".DB_PREFIX."users.sid AS 'sid',
		".DB_PREFIX."users.ip AS 'currip'
		FROM ".DB_PREFIX."users, WHERE user_agent='".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."' AND ".DB_PREFIX."users.sid ='".$_SESSION['sid']."' AND deleted=0";
} else { // //fallback loggedin odstranit v 1.5.4
	$sql = "SELECT
		".DB_PREFIX."users.id AS 'id',
		".DB_PREFIX."users.login AS 'login',
		".DB_PREFIX."users.pwd AS 'pwd',
		".DB_PREFIX."users.idperson AS 'idperson',
		".DB_PREFIX."users.lastlogon AS 'lastlogon',
		".DB_PREFIX."users.right_power AS 'right_power',
		".DB_PREFIX."users.right_text AS 'right_text',
		".DB_PREFIX."users.right_org AS 'right_org',
		".DB_PREFIX."users.right_aud AS 'right_aud',
		".DB_PREFIX."users.timeout AS 'timeout',
		".DB_PREFIX."users.ip AS 'ip',
		".DB_PREFIX."users.plan AS 'plan',
		".DB_PREFIX."loggedin.sid AS 'sid',
		".DB_PREFIX."loggedin.time AS 'lastaction',
		".DB_PREFIX."loggedin.ip AS 'currip'
		FROM ".DB_PREFIX."users, ".DB_PREFIX."loggedin WHERE agent='".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."' AND ".DB_PREFIX."loggedin.sid ='".mysqli_real_escape_string ($database,$_SESSION['sid'])."' AND deleted=0 AND ".DB_PREFIX."loggedin.iduser=".DB_PREFIX."users.id";
}
	if ($usrinfo=mysqli_fetch_assoc (mysqli_query ($database,$sql))) {
		$_SESSION['inactiveallowance'] = $usrinfo['timeout'];
	} else {
		unset($_SESSION['sid']);
	}
} else {
	unset($_SESSION['sid']);
}


// *** ALLOWED PAGES
$config['page_free'] = array_map(function($val) { global $config; return $config['page_prefix'].$val; }, $config['page_free']);   
if (!isset($_SESSION['sid']) AND in_array(end(explode("/", $_SERVER['REQUEST_URI'])),$config['page_free']) == false) {
	logout_forced();
}
?>