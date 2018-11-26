<?php
// overeni verze logovacich tabulek
$check_sql=mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."users' and column_name='suspended'");

// zpracovani login formulare
if (isset($_REQUEST['logmein'])) { 
	if (mysqli_num_rows($check_sql)== 0) { // without suspended 1.5.5>
		$logres=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."users WHERE login='".$_REQUEST['loginname']."' AND pwd=md5('".$_POST['loginpwd']."') and deleted=0 ");
	} else { //with suspended 1.5.5<
		$logres=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."users WHERE login='".$_REQUEST['loginname']."' AND pwd=md5('".$_POST['loginpwd']."') and deleted=0 and suspended=0 "); 
	}
	if($logrec=mysqli_fetch_array ($logres)) {
		$_SESSION['sid']=session_id(); 
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users set sid='' where sid='".$_SESSION['sid']."'");
		error_log("LOGIN SUCCESS: ".$_REQUEST['loginname']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET sid='".$_SESSION['sid']."', lastlogon=".Time().", ip='".$_SERVER['REMOTE_ADDR']."', user_agent='".$_SERVER['HTTP_USER_AGENT']."' WHERE id=".$logrec['id']);
	} else {
		error_log("LOGIN FAILED: ".$_REQUEST['loginname']);
	}
}
// info o uzivateli
if (isset($_SESSION['sid'])) {
	if (mysqli_num_rows($check_sql)== 0) { // without suspended 1.5.5>
		$usersql = "SELECT id, login, pwd, idperson, lastlogon as 'lastaction', right_power, right_text, right_org, right_aud, timeout, ip as 'currip', plan, sid
		FROM ".DB_PREFIX."users 
		WHERE deleted=0 AND ".DB_PREFIX."users.sid ='".$_SESSION['sid']."' AND user_agent='".$_SERVER['HTTP_USER_AGENT']."'";
	} else { //with suspended 1.5.5<
		$usersql = "SELECT id, login, pwd, idperson, lastlogon as 'lastaction', right_power, right_text, right_org, right_aud, timeout, ip as 'currip', plan, sid
		FROM ".DB_PREFIX."users 
		WHERE deleted=0 AND suspended=0 AND ".DB_PREFIX."users.sid ='".$_SESSION['sid']."' AND user_agent='".$_SERVER['HTTP_USER_AGENT']."'";
	} 
	if ($usrinfo=mysqli_fetch_assoc (mysqli_query ($database,$usersql))) {
		$_SESSION['inactiveallowance'] = $usrinfo['timeout'];
	} else {
		unset($_SESSION['sid']);
	}
}

// *** FORCED LOGOUT - timeout, unallowed pages
function logout_forced($msg) {
	global $database,$_SESSION;
	error_log("FORCED LOGOUT: ".$msg." - ".$_SESSION['sid']);
	if (isset($_SESSION['sid'])) {
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users set sid=null WHERE sid=".$_SESSION['sid']);
	}
	session_unset();
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