<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  MySQL_Query ("UPDATE ".DB_PREFIX."reports SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  Header ('Location: reports.php');
	}
	if (isset($_POST['insertrep']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['label']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['task']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['summary']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['impact']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['details']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  pageStart ('Hlášení uloženo');
		mainMenu (4);
		sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./newactrep.php">nové hlášení z výjezdu</a> &raquo; <strong>hlášení nepřidáno</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysql_real_escape_string(safeInput($_POST['label']))."')");
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Toto označení hlášení již, změňte ho.</p></div>';
	  } else {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."reports VALUES('','".mysql_real_escape_string(safeInput($_POST['label']))."','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string($_POST['task'])."','".mysql_real_escape_string($_POST['summary'])."','".mysql_real_escape_string($_POST['impact'])."','".mysql_real_escape_string($_POST['details'])."','".$_POST['secret']."','0','".$_POST['status']."','1')");
			echo '<div id="obsah"><p>Hlášení uloženo.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertrep'])) {
		  pageStart ('Hlášení uloženo');
			mainMenu (4);
			sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./newactrep.php">nové hlášení z výjezdu</a> &raquo; <strong>hlášení nepřidáno</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['reportid']) && isset($_POST['editactrep']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['label']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['task']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['summary']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['impacts']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['details']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  pageStart ('Uložení změn');
		mainMenu (4);
		sparklets ('<a href="./hlášein.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysql_real_escape_string(safeInput($_POST['label']))."') AND id<>".$_POST['reportid']);
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Toto označení již existuje, změňte ho.</p></div>';
	  } else {
			MySQL_Query ("UPDATE ".DB_PREFIX."reports SET label='".mysql_real_escape_string(safeInput($_POST['label']))."', task='".mysql_real_escape_string(safeInput($_POST['task']))."', summary='".mysql_real_escape_string($_POST['summary'])."', impacts='".mysql_real_escape_string(safeInput($_POST['impacts']))."', details='".mysql_real_escape_string(safeInput($_POST['details']))."', secret='".$_POST['secret']."', status='".$_POST['status']."' WHERE id=".$_POST['reportid']);
			echo '<div id="obsah"><p>Hlášení upraveno.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['editactrep'])) {
		  pageStart ('Uložení změn');
			mainMenu (4);
			sparklets ('<a href="./cases.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['reportid']) && is_numeric($_POST['secret'])) {
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','3','".$_POST['reportid']."','".$_POST['secret']."')";
		MySQL_Query ($sql);
		Header ('Location: '.$_POST['backurl']);
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		if ($usrinfo['right_text']) {
			$fres=MySQL_Query ("SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=MySQL_Fetch_Assoc($fres);
			UnLink ('./files/'.$frec['uniquename']);
			MySQL_Query ("DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editactrep.php?rid='.$_GET['reportid']);
	}
	if (isset($_POST['setnote'])) {
		if (!preg_match ('/^[[:blank:]]*$/i',$_POST['note']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && is_numeric($_POST['secret'])) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."notes VALUES('','".mysql_real_escape_string($_POST['note'])."','".mysql_real_escape_string($_POST['title'])."','".Time()."','".$usrinfo['id']."','3','".$_POST['caseid']."','".$_POST['secret']."')");
		}
		Header ('Location: '.$_POST['backurl']);
	}
	if (isset($_GET['deletenote'])) {
		MySQl_Query("DELETE FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.id=".$_GET['deletenote']);
		Header ('Location: '.URLDecode($_GET['backurl']));
	}
?>