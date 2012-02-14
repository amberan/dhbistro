<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  MySQL_Query ("UPDATE ".DB_PREFIX."groups SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  Header ('Location: groups.php');
	}
	if (isset($_POST['insertgroup']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret'])) {
	  pageStart ('Přidána skupina');
		mainMenu (3);
		sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./newgroup.php">nová skupina</a> &raquo; <strong>přidána skupina</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."')");
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
	  } else {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."groups VALUES('','".mysql_real_escape_string(safeInput($_POST['title']))."','".mysql_real_escape_string($_POST['contents'])."','".Time()."','".$usrinfo['id']."','0','".$_POST['secret']."')");
			echo '<div id="obsah"><p>Skupina vytvořena.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertgroup'])) {
		  pageStart ('Přidána skupina');
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./newgroup.php">nová skupina</a> &raquo; <strong>přidána skupina</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['groupid']) && isset($_POST['editgroup']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/i^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret'])) {
	  pageStart ('Uložení změn');
		mainMenu (3);
		sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn</strong>');
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."') AND id<>".$_POST['groupid']);
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
	  } else {
			MySQL_Query ("UPDATE ".DB_PREFIX."groups SET title='".mysql_real_escape_string(safeInput($_POST['title']))."', contents='".mysql_real_escape_string($_POST['contents'])."', secret='".$_POST['secret']."' WHERE id=".$_POST['groupid']);
			echo '<div id="obsah"><p>Skupina upravena.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['editgroup'])) {
		  pageStart ('Uložení změn');
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn</strong>');
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
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','2','".$_POST['groupid']."','".$_POST['secret']."')";
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
		Header ('Location: editgroup.php?rid='.$_GET['groupid']);
	}
	if (isset($_POST['setnote'])) {
		if (!preg_match ('/^[[:blank:]]*$/i',$_POST['note']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && is_numeric($_POST['secret'])) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."notes VALUES('','".mysql_real_escape_string($_POST['note'])."','".mysql_real_escape_string($_POST['title'])."','".Time()."','".$usrinfo['id']."','2','".$_POST['groupid']."','".$_POST['secret']."')");
		}
		Header ('Location: '.$_POST['backurl']);
	}
	if (isset($_GET['deletenote'])) {
		MySQl_Query("DELETE FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.id=".$_GET['deletenote']);
		Header ('Location: '.URLDecode($_GET['backurl']));
	}
?>