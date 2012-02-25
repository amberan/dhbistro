<?php
  $mtime = microtime();
  $mtime = explode(" ",$mtime);
  $mtime = $mtime[1] + $mtime[0];
  $starttime = $mtime;
	
	// verze
	$mazzarino_version='0.9.9 - BIStro';
  
	// sessions
	session_start();
	
	// databaze
  if (!@mysql_connect ('127.0.0.1','dhbistrocz','eqgsCv3t')) {
  	echo 'fail';
    Exit;
  }
	MySQL_Select_DB ('dhbistrocz');
  $page_prefix='';

	define ('DB_PREFIX','nw_');
  MySQL_Query ("SET NAMES 'utf8'");
  
  // prihlaseni
  if (isset($_REQUEST['logmein'])) {
    $logres=MySQL_Query ("SELECT id FROM ".DB_PREFIX."users WHERE login='".mysql_real_escape_string($_REQUEST['loginname'])."' AND pwd='".mysql_real_escape_string($_REQUEST['loginpwd'])."'");
    if ($logrec=MySQL_Fetch_Array ($logres)) {
      MySQL_Query ("DELETE FROM ".DB_PREFIX."loggedin WHERE iduser=".$logrec['id']);
      $sid=md5(uniqid(rand()));
			MySQL_Query ("INSERT INTO ".DB_PREFIX."loggedin VALUES('".$logrec['id']."','".Time()."','".$sid."','".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."','".$_SERVER['REMOTE_ADDR']."')");
			//MySQL_Query ("INSERT INTO ".DB_PREFIX."loggedin VALUES('".$logrec['id']."','".Time()."','".$sid."','','')");
			MySQL_Query ("UPDATE ".DB_PREFIX."users SET lastlogon=".Time().", ip='' WHERE id=".$logrec['id']);
      $_SESSION['sid']=$sid;
    }
  }
	
	// info o uzivateli
	if (isset($_SESSION['sid'])) {
		$ures=MySQL_Query ("SELECT
												".DB_PREFIX."users.id AS 'id',
												".DB_PREFIX."users.login AS 'login',
												".DB_PREFIX."users.pwd AS 'pwd',
												".DB_PREFIX."users.idperson AS 'idperson',
												".DB_PREFIX."users.lastlogon AS 'lastlogon',
												".DB_PREFIX."users.right_power AS 'right_power',
												".DB_PREFIX."users.right_text AS 'right_text',
												".DB_PREFIX."users.ip AS 'ip',
												".DB_PREFIX."users.plan AS 'plan',
												".DB_PREFIX."loggedin.sid AS 'sid',
												".DB_PREFIX."loggedin.time AS 'lastaction'
												FROM ".DB_PREFIX."users, ".DB_PREFIX."loggedin WHERE agent='".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."' AND deleted=0 AND ".DB_PREFIX."loggedin.iduser=".DB_PREFIX."users.id");
		if ($usrinfo=MySQL_Fetch_Assoc($ures)) {
		  $loggedin=true;
		} else {
		  $loggedin=false;
		}
	} else {
	  $loggedin=false;
	}

// TOHLE NEZAPOMENOUT ODKOMENTOVAT V OSTRE VERZI	
  // overeni prihlaseni, nutno zmenit jmeno souboru na ostre verzi
  $free_pages = array ($page_prefix.'/login.php');
	if (!$loggedin && !in_array($_SERVER['PHP_SELF'],$free_pages)) {
//		Header ('location: login.php');
	}

	// vypis zacatku stranky
	function pageStart ($title,$infotext='') {
	  global $loggedin, $usrinfo, $mazzarino_version;
		echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo (($loggedin)?$usrinfo['login'].' @ ':'')?>Mazzarino <?php echo $mazzarino_version;?> | <?php echo $title;?></title>
    <meta name="Author" content="Jakub Ethan Kraft" />
    <meta name="Copyright" content="2006 - 2007" />
    <meta http-equiv="Content-language" content="cs" />
    <meta http-equiv="Cache-control" content="no-cache" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="robots" content="index, follow" />
    <meta name="description" content="city larp management system" />
    <!--[if lt IE 7]><style type="text/css">body {behavior: url('./inc/csshover.htc');}</style><![endif]-->
    <link media="all" rel="stylesheet" type="text/css" href="./inc/styly.css" />
    <link media="print" rel="stylesheet" type="text/css" href="./css/print.css" />
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<script type="text/javascript" src="./js/tiny_mce/tiny_mce_gzip.js"></script>
		<script type="text/javascript" src="./js/tiny_mce_gz.js"></script>
    <script type="text/javascript" src="./js/tiny_mce_settings.js"></script>
  </head>
  <body>
<?php
	}
	
	// vypis konce stranky
	function pageEnd () {
	  global $starttime;
	  $mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $starttime);
	  echo '		<!-- Vygenerováno za '.$totaltime.' vteřin -->';
?>
	</body>
</html>
<?php
	}
	
	function mainMenu ($index) {
	  global $usrinfo;
	  echo '<div id="menu">
	<ul>
		<li><a href="index.php">Aktuality</a></li>
		'.(($usrinfo['right_power'])?'<li><a href="users.php">Uživatelé</a></li>':'').'
		<li><a href="evilpoints.php">Zlobody</a></li>
		<li><a href="groups.php">Skupiny</a></li>
		<li><a href="cases.php">Případy</a></li>
		<li><a href="persons.php">Osoby</a></li>
		<li><a href="reports.php">Hlášení</a></li>
		<li><a href="settings.php">Nastavení</a></li>
		'.(($usrinfo['right_power'])?'<li><a href="mapagents.php">Mapa agentů</a></li>':'').'
		<li><a href="logout.php">Odhlásit</a></li>
	</ul>
	<!--form id="search_menu">
		<input type="text" name="query" />
		<input type="submit" value="Hledat" />
	</form-->
</div>';
	}
	
	function sparklets ($path,$actions='') {
	  echo '<div id="sparklets">Cesta: '.$path.(($actions!='')?' || Akce: '.$actions:'').'</div>';
	}
	
	function safeInput ($input) {
		$replaced=Array ('"');
		$replacers=Array ('&quot;');
	  $output=str_replace ($replaced,$replacers,$input);
	  return $output;
	}
	
	function resize_Image ($img,$max_width,$max_height) {
    $size=GetImageSize($img);
    $width=$size[0];
    $height=$size[1];
    $x_ratio=$max_width/$width;
    $y_ratio=$max_height/$height;
    if (($width<=$max_width) && ($height<=$max_height)) {
      $tn_width=$width;
      $tn_height=$height;
    } else if (($x_ratio * $height) < $max_height) {
      $tn_height=ceil($x_ratio * $height);
      $tn_width=$max_width;
    } else {
      $tn_width=ceil($y_ratio * $width);
      $tn_height=$max_height;
    }
    if ($size[2]==1) {
      $src=ImageCreateFromGIF($img);
    }
    if ($size[2]==2) {
      $src=ImageCreateFromJPEG($img);
    }
    if ($size[2]==3) {
      $src=ImageCreateFromPNG($img);
    }
    $dst=ImageCreateTrueColor($tn_width,$tn_height);
    ImageCopyResampled ($dst,$src,0,0,0,0,$tn_width,$tn_height,$width,$height);
    Imageinterlace($dst, 1);
    ImageDestroy($src);
    return $dst;
  }
?>