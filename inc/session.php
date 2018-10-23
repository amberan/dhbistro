<?php
	// sessions
	session_start();
    $_SESSION['once']=0;
    
    // prihlaseni
  if (isset($_REQUEST['logmein'])) { 
	$logres=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."users WHERE login='".mysqli_real_escape_string ($database,$_REQUEST['loginname'])."' AND pwd='".mysqli_real_escape_string ($database,$_REQUEST['loginpwd'])."'");
	if ($logrec=mysqli_fetch_array ($logres)) {
      mysqli_query ($database,"DELETE FROM ".DB_PREFIX."loggedin WHERE iduser=".$logrec['id']);
      $sid=md5(uniqid(rand()));
			mysqli_query ($database,"INSERT INTO ".DB_PREFIX."loggedin VALUES('".$logrec['id']."','".Time()."','".$sid."','".mysqli_real_escape_string ($database,$_SERVER['HTTP_USER_AGENT'])."','".$_SERVER['REMOTE_ADDR']."')");
			//mysqli_query ($database,"INSERT INTO ".DB_PREFIX."loggedin VALUES('".$logrec['id']."','".Time()."','".$sid."','','')");
			mysqli_query ($database,"UPDATE ".DB_PREFIX."users SET lastlogon=".Time().", ip='' WHERE id=".$logrec['id']);
      $_SESSION['sid']=$sid;
    }
  }
	// info o uzivateli
	if (isset($_SESSION['sid'])) {
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
		$ures=mysqli_query ($database,$sql);

		if ($usrinfo=mysqli_fetch_assoc ($ures)) {
		  $loggedin=true;
		  
		  // natazeni tabulky neprectenych zaznamu do promenne
		  $sql_r="SELECT * FROM ".DB_PREFIX."unread WHERE iduser=".$usrinfo['id'];
		  $res_r=mysqli_query ($database,$sql_r);
		  while ($unread[]=mysqli_fetch_array ($res_r));
		} else {
		  $loggedin=false;
		}
	} else {
	  $loggedin=false;
	}

	// doba timeoutu ve vterinach
	if (isset($usrinfo['timeout'])) {
		$inactive = $usrinfo['timeout'];
	} else {
		$inactive = $config['timeout'];
	}
	
 	if(isset($_SESSION['timeout']) ) {
		$session_life = time() - $_SESSION['timeout'];
		if($session_life > $inactive) {
			session_destroy(); header("Location: login.php");
		}
	} 
	
    $_SESSION['timeout'] = time();
    
  $free_pages = array ($config['page_prefix'].'login.php');
  //if ($verze > 0) {
    $cropedUrlAll = explode("/", $_SERVER['PHP_SELF']);
    $cropedUrlLast = end($cropedUrlAll);
    if (!$loggedin && !in_array($cropedUrlLast,$free_pages)) {
            Header ('location: login.php');
    //}
  }

?>