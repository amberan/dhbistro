<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  MySQL_Query ("UPDATE ".DB_PREFIX."cases SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  Header ('Location: cases.php');
	}
	if (isset($_POST['insertcase']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  pageStart ('Přidán případ');
		mainMenu (4);
		sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./newcase.php">nový případ</a> &raquo; <strong>přidán případ</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."cases WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."')");
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Případ již existuje, změňte jeho jméno.</p></div>';
	  } else {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."cases VALUES('','".mysql_real_escape_string(safeInput($_POST['title']))."','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string($_POST['contents'])."','".$_POST['secret']."','0','".$_POST['status']."')");
			echo '<div id="obsah"><p>Případ vytvořen.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertcase'])) {
		  pageStart ('Přidán případ');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./newcase.php">nový případ</a> &raquo; <strong>přidán případ</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['caseid']) && isset($_POST['editcase']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  pageStart ('Uložení změn');
		mainMenu (4);
		sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."cases WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."') AND id<>".$_POST['caseid']);
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Případ již existuje, změňte jeho jméno.</p></div>';
	  } else {
			MySQL_Query ("UPDATE ".DB_PREFIX."cases SET title='".mysql_real_escape_string(safeInput($_POST['title']))."', contents='".mysql_real_escape_string($_POST['contents'])."', secret='".$_POST['secret']."', status='".$_POST['status']."' WHERE id=".$_POST['caseid']);
			echo '<div id="obsah"><p>Případ upraven.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['editcase'])) {
		  pageStart ('Uložení změn');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['caseid']) && is_numeric($_POST['secret'])) {
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','3','".$_POST['caseid']."','".$_POST['secret']."')";
		MySQL_Query ($sql);
		Header ('Location: '.$_POST['backurl']);
	} else {
	  if (isset($_POST['uploadfile'])) {
		  pageStart ('Uložení změn');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>přiložení souboru</strong>');
			echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		if ($usrinfo['right_text']) {
			$fres=MySQL_Query ("SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=MySQL_Fetch_Assoc($fres);
			UnLink ('./files/'.$frec['uniquename']);
			MySQL_Query ("DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editcase.php?rid='.$_GET['caseid']);
	}
?>