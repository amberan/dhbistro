<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
		auditTrail(3, 11, $_REQUEST['delete']);
	  mysqli_query ($database,"UPDATE ".DB_PREFIX."cases SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  deleteAllUnread (3,$_REQUEST['delete']);
	  Header ('Location: cases.php');
	}
	if (isset($_POST['insertcase']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  pageStart ('Přidán případ');
		mainMenu (4);
	  $ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."cases WHERE UCASE(title)=UCASE('".$_POST['title']."')");
	  if (mysqli_num_rows ($ures)) {
	  	sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./newcase.php">nový případ</a> &raquo; <strong>duplicita jména</strong>');
	    echo '<div id="obsah"><p>Případ již existuje, změňte jeho jméno.</p></div>';
	  } else {
			  mysqli_query ($database,"INSERT INTO ".DB_PREFIX."cases  (title, datum, iduser, contents, secret, deleted, status) VALUES('".$_POST['title']."','".Time()."','".$usrinfo['id']."','".$_POST['contents']."','".$_POST['secret']."','0','".$_POST['status']."')");
			$cidarray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."cases WHERE UCASE(title)=UCASE('".$_POST['title']."')"));
	  		$cid=$cidarray['id'];
	  		auditTrail(3, 3, $cid);
	  		if (!isset($_POST['notnew'])) {
	  			unreadRecords (3,$cid);
	  		}
	  		sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./newcase.php">nový případ</a> &raquo; <strong>přidán případ</strong>','<a href="./readcase.php?rid='.$cid.'">zobrazit vytvořené</a> &raquo; <a href="./editcase.php?rid='.$cid.'">úprava případu</a>');
			echo '<div id="obsah"><p>Případ vytvořen.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertcase'])) {
		  pageStart ('Přidán případ');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./newcase.php">nový případ</a> &raquo; <strong>přidání případu neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['caseid']) && isset($_POST['editcase']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  auditTrail(3, 2, $_POST['caseid']);
	  pageStart ('Uložení změn');
		mainMenu (4);
		if (!isset($_POST['notnew'])) {
			unreadRecords (3,$_POST['caseid']);
		}
	  $ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."cases WHERE UCASE(title)=UCASE('".$_POST['title']."') AND id<>".$_POST['caseid']);
	  if (mysqli_num_rows ($ures)) {
	  	sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn neúspěšné</strong>');
	    echo '<div id="obsah"><p>Případ již existuje, změňte jeho jméno.</p></div>';
	  } else {
	  		if ($usrinfo['right_org']==1) {
	  			mysqli_query ($database,"UPDATE ".DB_PREFIX."cases SET title='".$_POST['title']."', contents='".$_POST['contents']."', secret='".$_POST['secret']."', status='".$_POST['status']."' WHERE id=".$_POST['caseid']);
	  		} else {
				mysqli_query ($database,"UPDATE ".DB_PREFIX."cases SET title='".$_POST['title']."', datum='".Time()."', iduser='".$usrinfo['id']."', contents='".$_POST['contents']."', secret='".$_POST['secret']."', status='".$_POST['status']."' WHERE id=".$_POST['caseid']);
	  		}
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>','<a href="./readcase.php?rid='.$_POST['caseid'].'">zobrazit upravené</a>');
			echo '<div id="obsah"><p>Případ upraven.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['editcase'])) {
		  pageStart ('Uložení změn');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['caseid']) && is_numeric($_POST['secret'])) {
		auditTrail(3, 4, $_POST['caseid']);
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','3','".$_POST['caseid']."','".$_POST['secret']."')";
		mysqli_query ($database,$sql);
		if (!isset($_POST['fnotnew'])) {
			unreadRecords (3,$_POST['caseid']);
		}
		Header ('Location: '.$_POST['backurl']);
	} else {
	  if (isset($_POST['uploadfile'])) {
		  pageStart ('Přiložení souboru');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>přiložení souboru neúspěšné</strong>');
			echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		auditTrail(3, 5, $_GET['caseid']);
		if ($usrinfo['right_text']) {
			$fres=mysqli_query ($database,"SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=mysqli_fetch_assoc ($fres);
			UnLink ('./files/'.$frec['uniquename']);
			mysqli_query ($database,"DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editcase.php?rid='.$_GET['caseid']);
	}
?>