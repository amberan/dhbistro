<?php
// overeni verze logovacich tabulek
$check_sql=mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."users' and column_name='sid'");

$sid = session_id();

// zpracovani login formulare
if (isset($_REQUEST['logmein'])) { 
	$logres=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."users WHERE login='".$_REQUEST['loginname']."' AND pwd=md5('".$_POST['loginpwd']."')");
	if(mysqli_num_rows($check_sql)== 0 and $logrec=mysqli_fetch_array ($logres)) { // old login 1.5.3>
		mysqli_query ($database,"DELETE FROM ".DB_PREFIX."loggedin WHERE iduser=".$logrec['id']);
		mysqli_query ($database,"INSERT INTO ".DB_PREFIX."loggedin VALUES('".$logrec['id']."','".Time()."','".session_id()."','".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."','".$_SERVER['REMOTE_ADDR']."')");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET lastlogon=".Time().", ip='' WHERE id=".$logrec['id']);
		$_SESSION['sid']=session_id(); 
	} elseif ($logrec=mysqli_fetch_array ($logres)) { // new login 1.5.3<
		mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET sid='".session_id()."', lastlogon=".Time().", ip='".$_SERVER['REMOTE_ADDR']."', user_agent='".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."' WHERE id=".$logrec['id']);
		$_SESSION['sid']=session_id(); 
	} 
}
// info o uzivateli
if (isset($_SESSION['sid'])) {
	if (mysqli_num_rows($check_sql)== 0) { // old login 1.5.3>
		$usersql = "SELECT
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
			FROM ".DB_PREFIX."users, ".DB_PREFIX."loggedin WHERE agent='".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."' AND ".DB_PREFIX."loggedin.sid ='".$_SESSION['sid']."' AND deleted=0 AND ".DB_PREFIX."loggedin.iduser=".DB_PREFIX."users.id";
	} elseif (isset($_SESSION['sid'])) { //new login 1.5.3<
		$usersql = "SELECT
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
		FROM ".DB_PREFIX."users WHERE user_agent='".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."' AND ".DB_PREFIX."users.sid ='".$_SESSION['sid']."' AND deleted=0";
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
	mysqli_query ($database,"UPDATE ".DB_PREFIX."users set sid=null WHERE sid=".$_SESSION['sid']);
	session_destroy(); 
	session_start();
	if (isset($msg)) { $_SESSION['message'] .= $msg;}
	$_SESSION['timestamp'] = time();
	Header ('location: login.php');
}
$config['page_free'] = array_map(function($val) { global $config; return $config['page_prefix'].$val; }, $config['page_free']);   
if (!isset($_SESSION['inactiveallowance'])) { $_SESSION['inactiveallowance'] = $config['timeout'];}
$page_current = explode("/",$_SERVER['REQUEST_URI']);
if (((time() - $_SESSION['timestamp']) > $_SESSION['inactiveallowance']) and isset($_SESSION['sid']) and !isset($_REQUEST['logmein'])) { //timeout
		logout_forced("Z bezpečnostních důvodů jste byl odhlášen!");
} elseif (!isset($_SESSION['sid']) AND in_array(end($page_current),$config['page_free']) == false) {
		logout_forced(null);
}
$_SESSION['timestamp'] = time();
?>