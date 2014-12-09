<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  auditTrail(2, 11, $_REQUEST['delete']);
	  MySQL_Query ("UPDATE ".DB_PREFIX."groups SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  deleteAllUnread (2,$_REQUEST['delete']);
	  Header ('Location: groups.php');
	}
	if (isset($_POST['insertgroup']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret'])) {
	  pageStart ('Přidána skupina');
	  mainMenu (3);
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."')");
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
	  } else {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."groups VALUES('','".mysql_real_escape_string(safeInput($_POST['title']))."','".mysql_real_escape_string($_POST['contents'])."','".Time()."','".$usrinfo['id']."','0','".$_POST['secret']."')");
			$gidarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."')"));
			$gid=$gidarray['id'];
			auditTrail(2, 3, $gid);
			if (!isset($_POST['notnew'])) {
				unreadRecords (2,$gid);
			}
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./newgroup.php">nová skupina</a> &raquo; <strong>přidána skupina</strong>','<a href="./readgroup.php?rid='.$gid.'">zobrazit vytvořené</a> &raquo; <a href="./editgroup.php?rid='.$gid.'">úprava skupiny</a>');
			echo '<div id="obsah"><p>Skupina vytvořena.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertgroup'])) {
		  pageStart ('Přidána skupina');
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./newgroup.php">nová skupina</a> &raquo; <strong>přidání skupiny neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['groupid']) && isset($_POST['editgroup']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/i^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret'])) {
	  auditTrail(2, 2, $_POST['groupid']);
	  pageStart ('Uložení změn');
		mainMenu (3);
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."') AND id<>".$_POST['groupid']);
	  if (MySQL_Num_Rows ($ures)) {
	  	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn neúspěšné</strong>');
	    echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
	  } else {
			MySQL_Query ("UPDATE ".DB_PREFIX."groups SET title='".mysql_real_escape_string(safeInput($_POST['title']))."', contents='".mysql_real_escape_string($_POST['contents'])."', secret='".$_POST['secret']."' WHERE id=".$_POST['groupid']);
			if (!isset($_POST['notnew'])) {
				unreadRecords (2,$_POST['groupid']);
			}
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn</strong>','<a href="./readgroup.php?rid='.$_POST['groupid'].'">zobrazit upravené</a>');
			echo '<div id="obsah"><p>Skupina upravena.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['editgroup'])) {
		  pageStart ('Uložení změn');
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
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
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','2','".$_POST['groupid']."','".$_POST['secret']."')";
		MySQL_Query ($sql);
		if (!isset($_POST['fnotnew'])) {
			unreadRecords (2,$_POST['groupid']);
		}
		Header ('Location: '.$_POST['backurl']);
	} else {
	  if (isset($_POST['uploadfile'])) {
		  pageStart ('Přiložení souboru');
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>přiložení souboru neúspěšné</strong>');
			echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		auditTrail(2, 5, $_GET['groupid']);
		if ($usrinfo['right_text']) {
			$fres=MySQL_Query ("SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=MySQL_Fetch_Assoc($fres);
			UnLink ('./files/'.$frec['uniquename']);
			MySQL_Query ("DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editgroup.php?rid='.$_GET['groupid']);
	}
?>