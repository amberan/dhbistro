<?php
  $mtime = microtime();
  $mtime = explode(" ",$mtime);
  $mtime = $mtime[1] + $mtime[0];
  $starttime = $mtime;

	// verze
	$mazzarino_version='1.4.6';
  
	// sessions
	session_start();
	$_SESSION['once']=0;
	
	global $point;

	// databaze
  switch ($_SERVER["SERVER_NAME"]) {
  	case '127.0.0.1':
  		$dbusr='dhbistrocz';
  		$verze=0;
  		$point='zlobod';
  		$barva='local';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
  		break;
  	case 'www.dhbistro.cz':
  		$dbusr='dhbistrocz';
  		$verze=1;
  		$point='zlobod';
  		$barva='dh';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
  		break;
  	case 'nh.dhbistro.cz':
  		$dbusr='nhbistro';
  		$verze=2;
  		$point='bludišťák';
  		$barva='nh';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
  		break;
  	case 'test.dhbistro.cz':
  		$dbusr='testbistro';
  		$verze=3;
  		$point='zlobod';
  		$barva='test';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
  		break;
  	case 'org.dhbistro.cz':
		$dbusr='orgbistro';
		$verze=4;
		$point='zlobod';
		$barva='org';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
		break;
        case 'enigma.dhbistro.cz':
		$dbusr='enigmabistro';
		$verze=5;
		$point='zlobod';
		$barva='enigma';
                $hlaseniV='Zakázka';
                $hlaseniM='zakázka';
		break;
        case 'nhtest.dhbistro.cz':
  		$dbusr='nhtestbistro';
  		$verze=2;
  		$point='bludišťák';
  		$barva='test';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
  }
  
// vyzvedni heslo k databazi
$file = "inc/important.php";
$lines = file($file,FILE_IGNORE_NEW_LINES) or die("fail pwd");;
$password = $lines[2];

// kontrola pripojeni
  if (!@mysql_connect ('localhost',$dbusr,$password)) {
  	echo 'fail ';
    exit;
  }
	MySQL_Select_DB ($dbusr);
//	echo $dbusr;
//	echo $password;
//	echo $file;
//	echo $lines;

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
												".DB_PREFIX."users.right_aud AS 'right_aud',
												".DB_PREFIX."users.timeout AS 'timeout',
												".DB_PREFIX."users.ip AS 'ip',
												".DB_PREFIX."users.plan AS 'plan',
												".DB_PREFIX."loggedin.sid AS 'sid',
												".DB_PREFIX."loggedin.time AS 'lastaction',
												".DB_PREFIX."loggedin.ip AS 'currip'
												FROM ".DB_PREFIX."users, ".DB_PREFIX."loggedin WHERE agent='".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."' AND ".DB_PREFIX."loggedin.sid ='".mysql_real_escape_string($_SESSION['sid'])."' AND deleted=0 AND ".DB_PREFIX."loggedin.iduser=".DB_PREFIX."users.id";
		$ures=MySQL_Query ($sql);

		if ($usrinfo=MySQL_Fetch_Assoc($ures)) {
		  $loggedin=true;
		  
		  // natazeni tabulky neprectenych zaznamu do promenne
		  $sql_r="SELECT * FROM ".DB_PREFIX."unread WHERE iduser=".$usrinfo['id'];
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
  $free_pages = array ($page_prefix.'login.php');
  //if ($verze > 0) {
    $cropedUrlAll = explode("/", $_SERVER['PHP_SELF']);
    $cropedUrlLast = end($cropedUrlAll);
    if (!$loggedin && !in_array($cropedUrlLast,$free_pages)) {
            Header ('location: login.php');
    }
  //}

// vyhledani tabulky v neprectenych zaznamech
function searchTable ($tablenum) { 
	global $unread;
	foreach ($unread as $record) {
            if ($record['idtable'] == $tablenum) {
            return true;
        }
    }
    	return false;
}

// vyhledani zaznamu v neprectenych zaznamech
function searchRecord ($tablenum, $recordnum) {
	global $unread;
	foreach ($unread as $record) {
            if ($record['idtable'] == $tablenum && $record['idrecord'] == $recordnum) {
            return true;
        }
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
				$srsql="INSERT INTO ".DB_PREFIX."unread (idtable, idrecord, iduser) VALUES('".$tablenum."', '".$rid."', '".$rec_ur['id']."')";
				MySQL_Query ($srsql);
			}
		} else if ($secret == 0 && $rec_ur['deleted'] <> 1) {
			if ($rec_ur['id'] <> $usrinfo['id']) {
				$srsql="INSERT INTO ".DB_PREFIX."unread (idtable, idrecord, iduser) VALUES('".$tablenum."', '".$rid."', '".$rec_ur['id']."')";
				MySQL_Query ($srsql);
			}
		}
	}
}

// vymaz z tabulek neprectenych pri precteni
function deleteUnread ($tablenum,$rid) {
	global $usrinfo;
	if ($rid<>'none') {
		$sql_ur="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND idrecord=".$rid." AND iduser=".$usrinfo['id'];
	} else {
		$sql_ur="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND iduser=".$usrinfo['id'];
	}
	MySQL_Query ($sql_ur);
}

// vymaz z tabulek neprectenych pri smazani zaznamu
function deleteAllUnread ($tablenum,$rid) {
	$sql_ur="SELECT ".DB_PREFIX."users.id as 'id', ".DB_PREFIX."users.right_power as 'right_power' FROM ".DB_PREFIX."users";
	$res_ur=MySQL_Query ($sql_ur);
	while ($rec_ur=MySQL_Fetch_Assoc($res_ur)) {
		$srsql="DELETE FROM ".DB_PREFIX."unread WHERE idtable=".$tablenum." AND idrecord=".$rid." AND iduser=".$rec_ur['id'];
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

//auditni stopa
function auditTrail ($record_type,$operation_type,$idrecord) {
	global $usrinfo;
	$sql_check="SELECT * FROM ".DB_PREFIX."audit_trail WHERE iduser='".$usrinfo['id']."' AND time='".time()."'";
	$res_check=MySQL_Query ($sql_check);
	if (MySQL_Num_Rows($res_check)) {
	} else {
		if (!$usrinfo['currip']) {
			$currip=$_SERVER['REMOTE_ADDR'];
		} else {
			$currip=$usrinfo['currip'];
		}
		$sql_au="INSERT INTO ".DB_PREFIX."audit_trail VALUES('','".$usrinfo['id']."','".time()."','".$operation_type."','".$record_type."','".$idrecord."','".$currip."','".$usrinfo['right_org']."')";
		MySql_Query($sql_au);
	}
}

//pokus o pristup k tajnemu, soukromemu nebo smazanemu zaznamu
function unauthorizedAccess ($record_type,$secret,$deleted,$idrecord) {
	global $usrinfo;
        switch ($record_type) {
            case 1:
                $link='<a href="./persons.php">osoby</a>';
                break;
            case 2:
                $link='<a href="./groups.php">skupiny</a>';
                break;
            case 3:
                $link='<a href="./cases.php">případy</a>';
                break;
            case 4:
                $link='<a href="./reports.php">hlášení</a>';
                break;
            case 8:
                $link='A ven!';
                break;
        }
        if ($deleted==1) {
            auditTrail($record_type, 13, $idrecord);
        } else {
            auditTrail($record_type, 12, $idrecord);
        }
        pageStart ('Neautorizovaný přístup');
        mainMenu (5);
        sparklets ($link.' &raquo; <strong>neautorizovaný přístup</strong>');
        echo '<div id="obsah"><p>Tady nemáš co dělat.</p></div>';
        exit;
}

//vytvoreni zalohy
function backupDB () {
	global $dbusr;
	function  zalohuj($db,$soubor=""){
	 global $dbusr;
	 
		function  keys($prefix,$array){
			if (empty($array)) { $pocet=0; } else {	$pocet = count ($array); }
			if (!isset($radky)) { $radky=''; }
			if ($pocet == 0)
				return ;
			for ($i = 0; $i<$pocet; $i++)
				$radky .= "`".$array[$i]."`".($i != $pocet-1 ? ",":"");
				return  ",\n".$prefix."(".$radky.")";
		}

		$sql = mysql_query ("SHOW table status  FROM ".$db);


		while ($data = mysql_fetch_row ($sql)){

		if (!isset($text)) { $text = '';}
		$text .= (empty ($text)?"":"\n\n")."--\n-- Struktura tabulky ".$data[0]."\n--\n\n\n";
    	$text .= "CREATE TABLE `".$data[0]."`(\n";
	    $sqll = mysql_query ("SHOW columns  FROM ".$data[0]);
    			$e = true;

		while ($dataa = mysql_fetch_row ($sqll)){
		if ($e) $e = false;
			else  $text .= ",\n";

			$null = ($dataa[2] == "NO")? "NOT NULL":"NULL";
			$default = !empty ($dataa[4])? " DEFAULT '".$dataa[4]."'":"";

			if ($default == " DEFAULT 'CURRENT_TIMESTAMP'") $default = " DEFAULT CURRENT_TIMESTAMP";
	      if ($dataa[3] == "PRI") $PRI[] = $dataa[0];
    	  		if ($dataa[3] == "UNI") $UNI[] = $dataa[0];
      			if ($dataa[3] == "MUL") $MUL[] = $dataa[0];
      			$extra = !empty ($dataa[5])? " ".$dataa[5]:"";
      			$text .= "`$dataa[0]` $dataa[1] $null$default$extra";
		}
		if (!isset($UNI)) $UNI='';
		if (!isset($PRI)) $PRI='';
		if (!isset($MUL)) $MUL='';
		$primary = keys("PRIMARY KEY",$PRI);
		$unique = keys("UNIQUE KEY",$UNI);
    	$mul = keys("INDEX",$MUL);
    	$text .= $primary.$unique.$mul."\n) ENGINE=".$data[1]." COLLATE=".$data[14].";\n\n";
    	unset ($PRI,$UNI,$MUL);

	    $text .= "--\n-- Data tabulky ".$data[0]."\n--\n\n";
		$query = mysql_query ("SELECT  * FROM ".$data[0]."");
		while ($fetch = mysql_fetch_row ($query)){
		$pocet_sloupcu = count ($fetch);

		for ($i = 0;$i < $pocet_sloupcu;$i++)
			@$values .= "'".mysql_escape_string ($fetch[$i])."'".($i < $pocet_sloupcu-1?",":"");
			$text .= "\nINSERT INTO `".$data[0]."` VALUES(".$values.");";
			unset ($values);
		}
		}

		if (!empty ($soubor)){
		$fp = @fopen ($soubor,"w+");
		$fw = @fwrite ($fp,$text);
		@fclose ($fp);
		}

		return  $text;
		}
	
		$sql_check="SELECT time FROM ".DB_PREFIX."backups ORDER BY time DESC LIMIT 1";
		$fetch_check=MySQL_Fetch_Assoc(MySql_Query ($sql_check));
		$last_backup=$fetch_check['time'];
		if (round($last_backup,-5)<round(time(),-5)) {
			mysql_query ("SET NAMES  'utf8'");
			$xsoubor="backup".time().".sql";
			$fsoubor="files/backups/".$xsoubor;
			$sql_bck="INSERT INTO ".DB_PREFIX."backups VALUES('','".Time()."','".$xsoubor."')";
			MySql_Query ($sql_bck);
			zalohuj($dbusr,$fsoubor);
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
    <meta http-equiv="Content-language" content="cs" />
    <meta http-equiv="Cache-control" content="no-cache" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="robots" content="index, follow" />
    <meta name="Author" content="David Ambeřan Maleček, Jakub Ethan Kraft" />
    <meta name="Copyright" content="2006 - 2014" />
    
    <title><?php echo (($loggedin)?$usrinfo['login'].' @ ':'')?>BIStro <?php echo $mazzarino_version;?> | <?php echo $title;?></title>
    <meta name="description" content="city larp management system" />
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    
    <!--[if lt IE 7]><style type="text/css">body {behavior: url('./inc/csshover.htc');}</style><![endif]-->
    <link media="all" rel="stylesheet" type="text/css" href="./inc/styly.css" />
    <link media="print" rel="stylesheet" type="text/css" href="./css/print.css" />
	
	<script type="text/javascript" src="./js/jquery-min.js"></script>
        <script type="text/javascript" src="./js/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
        tinymce.init({
            selector: "textarea",
            theme: "modern",
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality template paste textcolor"
            ],
            toolbar: "undo redo | styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor table removeformat",
            menubar: false,
            toolbar_items_size: 'small',
        });
        </script>
        
	<script type="text/javascript" src="./js/mrFixit.js"></script>
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
	  global $usrinfo, $verze, $barva, $hlaseniV;
	  $currentfile = $_SERVER["PHP_SELF"];
	  $dlink=MySQL_Fetch_Assoc(MySQL_Query ("SELECT link FROM ".DB_PREFIX."doodle ORDER BY id desc LIMIT 0,1"));
	  echo '<div id="menu">
	<ul class="'.$barva.'">
		<li '.((searchTable(5))?' class="unread"':((searchTable(6))?' class="unread"':'')).'><a href="index.php">Aktuality</a></li>
		<li '.((searchTable(4))?' class="unread"':'').'><a href="reports.php">'.$hlaseniV.'</a></li>	
		<li '.((searchTable(1))?' class="unread"':((searchTable(7))?' class="unread"':'')).'><a href="persons.php">Osoby</a></li>
		<li '.((searchTable(3))?' class="unread"':'').'><a href="cases.php">Případy</a></li>
		<li '.((searchTable(2))?' class="unread"':'').'><a href="groups.php">Skupiny</a></li>
		'.(($usrinfo['right_power'])?'<li><a href="mapagents.php">Mapa agentů</a></li>':'').'
		'.(($usrinfo['right_power'])?'<li><a href="doodle.php">Časová dostupnost</a></li>':'<li><a href="'.$dlink['link'].'" target="_new">Časová dostupnost</a></li>').'
		'.(($verze==2)?'<li><a href="http://www.prazskahlidka.cz/forums/index.php" target="_new">Fórum</a></li>':'<li><a href="http://www.prazskahlidka.cz/forums/index.php" target="_new">Fórum</a></li>').'
		'.(($verze==2)?'<li><a href="evilpoints.php">Bludišťáky</a></li>':'<li><a href="evilpoints.php">Zlobody</a></li>').'
		<li><a href="settings.php">Nastavení</a></li>
                <li><a href="search.php">Vyhledávání</a></li>
		'.(($usrinfo['right_power'])?'<li><a href="users.php">Uživatelé</a></li>':'').'
                '.((!$usrinfo['right_power'] && $usrinfo['right_text'])?'<li><a href="tasks.php">Úkoly</a></li>':'').'
		'.(($usrinfo['right_aud'])?'<li><a href="audit.php">Audit</a></li>':'').'
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