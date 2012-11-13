<?php
  $mtime = microtime();
  $mtime = explode(" ",$mtime);
  $mtime = $mtime[1] + $mtime[0];
  $starttime = $mtime;
	
	// verze
	$mazzarino_version='1.3.3';
  
	// sessions
	session_start();

	// databaze
  switch ($_SERVER["SERVER_NAME"]) {
  	case '127.0.0.1':
  		$dbusr='dhbistrocz';
  		$verze=0;
  		$point='zlobod';
  		break;
  	case 'www.dhbistro.cz':
  		$dbusr='dhbistrocz';
  		$verze=1;
  		$point='zlobod';
  		break;
  	case 'nh.dhbistro.cz':
  		$dbusr='nhbistro';
  		$verze=2;
  		$point='bludišťák';
  		break;
  	case 'test.dhbistro.cz':
  		$dbusr='testbistro';
  		$verze=3;
  		$point='zlobod';
  		break;
  	case 'org.dhbistro.cz':
		$dbusr='orgbistro';
		$verze=4;
		$point='zlobod';
		break;
  }

  if (!@mysql_connect ('localhost',$dbusr,'eqgsCv3t')) {
  	echo 'fail ';
    Exit;
  }
	MySQL_Select_DB ($dbusr);

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
		$sql = "SELECT
												".DB_PREFIX."users.id AS 'id',
												".DB_PREFIX."users.login AS 'login',
												".DB_PREFIX."users.pwd AS 'pwd',
												".DB_PREFIX."users.idperson AS 'idperson',
												".DB_PREFIX."users.lastlogon AS 'lastlogon',
												".DB_PREFIX."users.right_power AS 'right_power',
												".DB_PREFIX."users.right_text AS 'right_text',
												".DB_PREFIX."users.right_org AS 'right_org',
												".DB_PREFIX."users.timeout AS 'timeout',
												".DB_PREFIX."users.ip AS 'ip',
												".DB_PREFIX."users.plan AS 'plan',
												".DB_PREFIX."loggedin.sid AS 'sid',
												".DB_PREFIX."loggedin.time AS 'lastaction'
												FROM ".DB_PREFIX."users, ".DB_PREFIX."loggedin WHERE agent='".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."' AND ".DB_PREFIX."loggedin.sid ='".mysql_real_escape_string($_SESSION['sid'])."' AND deleted=0 AND ".DB_PREFIX."loggedin.iduser=".DB_PREFIX."users.id";
		$ures=MySQL_Query ($sql);

		if ($usrinfo=MySQL_Fetch_Assoc($ures)) {
		  $loggedin=true;
		  
		  // natazeni tabulky neprectenych zaznamu do promenne
		  $sql_r="SELECT * FROM ".DB_PREFIX."unread_".$usrinfo['id'];
		  $res_r=MySQL_Query($sql_r);
		  while ($unread[]=mysql_fetch_array($res_r));
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
		$inactive = 600;
	}
	
	if(isset($_SESSION['timeout']) ) {
		$session_life = time() - $_SESSION['timeout'];
		if($session_life > $inactive) {
			session_destroy(); header("Location: login.php");
		}
	}
	
	$_SESSION['timeout'] = time();
	
  // ta parametrizaci na verzi je tam proto, ze na lokale to kdoviproc nefunguje	
  // overeni prihlaseni, nutno zmenit jmeno souboru na ostre verzi
  $free_pages = array ($page_prefix.'/login.php');
  if ($verze > 0) {
	if (!$loggedin && !in_array($_SERVER['PHP_SELF'],$free_pages)) {
		Header ('location: login.php');
	}
  }

// vyhledani tabulky v neprectenych zaznamech
function searchTable ($tablenum) { 
	global $unread;
	foreach ($unread as $record) {
   		if ($record['idtable'] == $tablenum)
       		return true;
		}
    	return false;
}

// vyhledani zaznamu v neprectenych zaznamech
function searchRecord ($tablenum, $recordnum) {
	global $unread;
	foreach ($unread as $record) {
		if ($record['idtable'] == $tablenum && $record['idrecord'] == $recordnum)
			return true;
	}
	return false;
}

// zaznam do tabulek neprectenych
function unreadRecords ($tablenum,$rid) {
	global $usrinfo, $_POST;
	$secret=0;
	if (isset($_POST['secret'])) {
		$secret=$_POST['secret'];
	}
	if (isset($_POST['nsecret'])) {
		$secret=$_POST['nsecret'];
	}
	$sql_ur="SELECT ".DB_PREFIX."users.id as 'id', ".DB_PREFIX."users.right_power as 'right_power', ".DB_PREFIX."users.deleted as 'deleted' FROM ".DB_PREFIX."users";
	$res_ur=MySQL_Query ($sql_ur);
	while ($rec_ur=MySQL_Fetch_Assoc($res_ur)) {
		if ($secret == 1 && $rec_ur['deleted'] <> 1) {
			if ($rec_ur['id'] <> $usrinfo['id'] && $rec_ur['right_power'] == 1) {
				$srsql="INSERT INTO ".DB_PREFIX."unread_".$rec_ur['id']." (idtable, idrecord) VALUES('".$tablenum."', '".$rid."')";
				MySQL_Query ($srsql);
			}
		} else if ($secret == 0 && $rec_ur['deleted'] <> 1) {
			if ($rec_ur['id'] <> $usrinfo['id']) {
				$srsql="INSERT INTO ".DB_PREFIX."unread_".$rec_ur['id']." (idtable, idrecord) VALUES('".$tablenum."', '".$rid."')";
				MySQL_Query ($srsql);
			}
		}
	}
}

// vymaz z tabulek neprectenych pri precteni
function deleteUnread ($tablenum,$rid) {
	global $usrinfo;
	$sql_ur="DELETE FROM ".DB_PREFIX."unread_".$usrinfo['id']." WHERE idtable=".$tablenum." AND idrecord=".$rid;
	MySQL_Query ($sql_ur);
}

// vymaz z tabulek neprectenych pri smazani zaznamu
function deleteAllUnread ($tablenum,$rid) {
	$sql_ur="SELECT ".DB_PREFIX."users.id as 'id', ".DB_PREFIX."users.right_power as 'right_power' FROM ".DB_PREFIX."users";
	$res_ur=MySQL_Query ($sql_ur);
	while ($rec_ur=MySQL_Fetch_Assoc($res_ur)) {
		$srsql="DELETE FROM ".DB_PREFIX."unread_".$rec_ur['id']." WHERE idtable=".$tablenum." AND idrecord=".$rid;
		MySQL_Query ($srsql);
	}
}

// ziskani autora zaznamu
function getAuthor ($recid,$trn) {
	if ($trn==1) {
		$sql_ga="SELECT ".DB_PREFIX."persons.name as 'name', ".DB_PREFIX."persons.surname as 'surname', ".DB_PREFIX."users.login as 'nick' FROM ".DB_PREFIX."persons, ".DB_PREFIX."users WHERE ".DB_PREFIX."users.id=".$recid." AND ".DB_PREFIX."persons.id=".DB_PREFIX."users.idperson";
		$res_ga=MySQL_Query ($sql_ga);
		if (MySQL_Num_Rows($res_ga)) {
			while ($rec_ga=MySQL_Fetch_Assoc($res_ga)) {
				$name=StripSlashes ($rec_ga['surname']).', '.StripSlashes ($rec_ga['name']);
				return $name;
			}
		} else {
			$name='Uživatel není přiřazen.';
			return $name;
		}
	} else {
		$sql_ga="SELECT ".DB_PREFIX."users.login as 'nick' FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.id=".$recid;
		$res_ga=MySQL_Query ($sql_ga);
		if (MySQL_Num_Rows($res_ga)) {
			while ($rec_ga=MySQL_Fetch_Assoc($res_ga)) {
				$name=StripSlashes ($rec_ga['nick']);
				return $name;
			}
		} else {
			$name='Neznámo.';
			return $name;
		}
	}
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
    <title><?php echo (($loggedin)?$usrinfo['login'].' @ ':'')?>BIStro <?php echo $mazzarino_version;?> | <?php echo $title;?></title>
    <meta name="Author" content="Jakub Ethan Kraft, David Ambeřan Maleček" />
    <meta name="Copyright" content="2006 - 2012" />
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
<div id="wrapper">
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
</div>
<!-- end of #wrapper -->
</body>
</html>
<?php
	}
	
	function mainMenu ($index) {
	  global $usrinfo, $verze;
	  $currentfile = $_SERVER["PHP_SELF"];
	  $dlink=MySQL_Fetch_Assoc(MySQL_Query ("SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
	  echo '<div id="menu">
	<ul>
		<li '.((searchTable(5))?' class="unread"':((searchTable(6))?' class="unread"':'')).'><a href="index.php">Aktuality</a></li>
		<li '.((searchTable(4))?' class="unread"':'').'><a href="reports.php">Hlášení</a></li>	
		<li '.((searchTable(1))?' class="unread"':'').'><a href="persons.php">Osoby</a></li>
		<li '.((searchTable(3))?' class="unread"':'').'><a href="cases.php">Případy</a></li>
		<li '.((searchTable(2))?' class="unread"':'').'><a href="groups.php">Skupiny</a></li>
		'.(($usrinfo['right_power'])?'<li><a href="mapagents.php">Mapa agentů</a></li>':'').'
		'.(($usrinfo['right_power'])?'<li><a href="doodle.php">Časová dostupnost</a></li>':'<li><a href="'.$dlink['link'].'" target="_new">Časová dostupnost</a></li>').'
		'.(($verze==2)?'<li><a href="http://www.prazskahlidka.cz/forum2/index.php" target="_new">Fórum</a></li>':'<li><a href="http://www.prazskahlidka.cz/forum/index.php" target="_new">Fórum</a></li>').'
		'.(($verze==2)?'<li><a href="evilpoints.php">Bludišťáky</a></li>':'<li><a href="evilpoints.php">Zlobody</a></li>').'
		<li><a href="settings.php">Nastavení</a></li>
		'.(($usrinfo['right_power'])?'<li><a href="users.php">Uživatelé</a></li>':'').'
		<li class="float-right"><a href="logout.php">Odhlásit</a></li>
		<li class="float-right"><a href="procother.php?delallnew='.$currentfile.'" onclick="'."return confirm('Opravdu označit vše jako přečtené?');".'">Přečíst vše</a></li>
	</ul>
	<!-- form id="search_menu">
		<input type="text" name="query" />
		<input type="submit" value="Hledat" />
	</form -->
</div>
<!-- end of #menu -->';
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