<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);

$latteParameters['title'] = 'Zobrazení symbolu';



	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  auditTrail(2, 11, $_REQUEST['delete']);
	  mysqli_query ($database,"UPDATE ".DB_PREFIX."groups SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  deleteAllUnread (2,$_REQUEST['delete']);
	  Header ('Location: groups.php');
	}
        if (isset($_REQUEST['archive']) && is_numeric($_REQUEST['archive'])) {
	  auditTrail(2, 2, $_REQUEST['archive']);
	  mysqli_query ($database,"UPDATE ".DB_PREFIX."groups SET archived=1 WHERE id=".$_REQUEST['archive']);
	  Header ('Location: groups.php');
	}
        if (isset($_REQUEST['dearchive']) && is_numeric($_REQUEST['dearchive'])) {
	  auditTrail(2, 2, $_REQUEST['dearchive']);
	  mysqli_query ($database,"UPDATE ".DB_PREFIX."groups SET archived=0 WHERE id=".$_REQUEST['dearchive']);
	  Header ('Location: groups.php');
	}
	if (isset($_POST['insertgroup']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret'])) {
	  $latteParameters['title'] = 'Přidána skupina';
	  $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	  mainMenu (3);
	  $ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".$_POST['title']."')");
	  if (mysqli_num_rows ($ures)) {
	    echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
	  } else {
			mysqli_query ($database,"INSERT INTO ".DB_PREFIX."groups ( title, contents, datum, iduser, deleted, secret, archived) VALUES('".$_POST['title']."','".$_POST['contents']."','".Time()."','".$usrinfo['id']."','0','".$_POST['secret']."',0)");
			$gidarray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".$_POST['title']."')"));
			$gid=$gidarray['id'];
			auditTrail(2, 3, $gid);
			if (!isset($_POST['notnew'])) {
				unreadRecords (2,$gid);
			}
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./newgroup.php">nová skupina</a> &raquo; <strong>přidána skupina</strong>','<a href="./readgroup.php?rid='.$gid.'">zobrazit vytvořené</a> &raquo; <a href="./editgroup.php?rid='.$gid.'">úprava skupiny</a>');
			echo '<div id="obsah"><p>Skupina vytvořena.</p></div>';
		}
		$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	} else {
	  if (isset($_POST['insertgroup'])) {
		  $latteParameters['title'] = 'Přidána skupina';
		  $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
		  			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./newgroup.php">nová skupina</a> &raquo; <strong>přidání skupiny neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
		}
	}
	if (isset($_POST['groupid']) && isset($_POST['editgroup']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/i^[[:blank:]]*$/i',$_POST['contents'])) {
	  auditTrail(2, 2, $_POST['groupid']);
	  $latteParameters['title'] = 'Uložení změn';
	  $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
		mainMenu (3);
	  $ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".$_POST['title']."') AND id<>".$_POST['groupid']);
	  if (mysqli_num_rows ($ures)) {
	  	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn neúspěšné</strong>');
	    echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
	  } else {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."groups SET title='".$_POST['title']."', contents='".$_POST['contents']."', archived='".(isset($_POST['archived'])?'1':'0')."', secret='".(isset($_POST['secret'])?'1':'0')."' WHERE id=".$_POST['groupid']);
			if (!isset($_POST['notnew'])) {
				unreadRecords (2,$_POST['groupid']);
			}
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn</strong>','<a href="./readgroup.php?rid='.$_POST['groupid'].'">zobrazit upravené</a>');
			echo '<div id="obsah"><p>Skupina upravena.</p></div>';
		}
		$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	} else {
	  if (isset($_POST['editgroup'])) {
		  $latteParameters['title'] = 'Uložení změn';
		  $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
		  			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
		}
	}
	if (isset($_POST['setperson'])) {
		
		Header ('Location: editgroup.php?rid='.$_POST['groupid']);
	}
	if (isset($_GET['delperson']) && is_numeric($_GET['delperson'])) {
		Header ('Location: editgroup.php?rid='.$_GET['groupid']);
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['groupid']) && is_numeric($_POST['secret'])) {
		auditTrail(2, 4, $_POST['groupid']);
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','2','".$_POST['groupid']."','".$_POST['secret']."')";
		mysqli_query ($database,$sql);
		if (!isset($_POST['fnotnew'])) {
			unreadRecords (2,$_POST['groupid']);
		}
		Header ('Location: '.$_POST['backurl']);
	} else {
	  if (isset($_POST['uploadfile'])) {
		  $latteParameters['title'] = 'Přiložení souboru';
		  $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>přiložení souboru neúspěšné</strong>');
			echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
			$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
		}
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		auditTrail(2, 5, $_GET['groupid']);
		if ($usrinfo['right_text']) {
			$fres=mysqli_query ($database,"SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=mysqli_fetch_assoc ($fres);
			UnLink ('./files/'.$frec['uniquename']);
			mysqli_query ($database,"DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editgroup.php?rid='.$_GET['groupid']);
	}
?>