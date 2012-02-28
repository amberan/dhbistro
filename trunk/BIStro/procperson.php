<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete']) && $usrinfo['right_text']) {
	  MySQL_Query ("UPDATE ".DB_PREFIX."persons SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  Header ('Location: persons.php');
	}
	if (isset($_POST['insertperson']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['name']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
	  pageStart ('Přidána osoba');
		mainMenu (5);
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./newperson.php">nová osoba</a> &raquo; <strong>přidána osoba</strong>');
		if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
      $file=Time().MD5(uniqid(Time().Rand()));
      move_uploaded_file ($_FILES['portrait']['tmp_name'],'./files/'.$file.'tmp');
			$dst=resize_Image ('./files/'.$file.'tmp',100,130);
			imagejpeg($dst,'./files/portraits/'.$file);
			unlink('./files/'.$file.'tmp');
		} else {
		  $file='';
		}
		MySQL_Query ("INSERT INTO ".DB_PREFIX."persons VALUES('','".mysql_real_escape_string(safeInput($_POST['name']))."','".mysql_real_escape_string(safeInput($_POST['surname']))."','".mysql_real_escape_string(safeInput($_POST['phone']))."','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string($_POST['contents'])."','".$_POST['secret']."','0','".$file."', '".$_POST['side']."', '".$_POST['power']."', '".$_POST['spec']."')");
		echo '<div id="obsah"><p>Osoba vytvořena.</p></div>';
		pageEnd ();
	} else {
	  if (isset($_POST['insertperson'])) {
		  pageStart ('Přidána osoba');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./newperson.php">nová osoba</a> &raquo; <strong>přidána osoba</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['personid']) && isset($_POST['editperson']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['name']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
	  pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>');
		if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
		  $ps=MySQL_Query ("SELECT portrait FROM ".DB_PREFIX."persons WHERE id=".$_POST['personid']);
		  if ($pc=MySQL_Fetch_Assoc($ps)) {
		    unlink('./files/portraits/'.$pc['portrait']);
		  }
      $file=Time().MD5(uniqid(Time().Rand()));
      move_uploaded_file ($_FILES['portrait']['tmp_name'],'./files/'.$file.'tmp');
			$dst=resize_Image ('./files/'.$file.'tmp',100,130);
			imagejpeg($dst,'./files/portraits/'.$file);
			unlink('./files/'.$file.'tmp');
			MySQL_Query ("UPDATE ".DB_PREFIX."persons SET portrait='".$file."' WHERE id=".$_POST['personid']);
		}
		MySQL_Query ("UPDATE ".DB_PREFIX."persons SET name='".mysql_real_escape_string(safeInput($_POST['name']))."', surname='".mysql_real_escape_string(safeInput($_POST['surname']))."', phone='".mysql_real_escape_string($_POST['phone'])."', datum='".Time()."', iduser='".$usrinfo['id']."', contents='".mysql_real_escape_string($_POST['contents'])."', secret='".$_POST['secret']."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."' WHERE id=".$_POST['personid']);
		echo '<div id="obsah"><p>Osoba upravena.</p></div>';
		pageEnd ();
	} else {
	  if (isset($_POST['editperson'])) {
		  pageStart ('Uložení změn');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['setgroups'])) {
		MySQL_Query ("DELETE FROM ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".$_POST['personid']);
		$group=$_POST['group'];
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Skupiny pro uživatele uloženy.</p></div>';
		for ($i=0;$i<Count($group);$i++) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."g2p VALUES('".$_POST['personid']."','".$group[$i]."','".$usrinfo['id']."')");
		}
		pageEnd ();
	}
	if (isset($_POST['uploadfile'])) {
		if (is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['personid']) && is_numeric($_POST['secret'])) {
			$newname=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
			$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','1','".$_POST['personid']."','".$_POST['secret']."')";
			MySQL_Query ($sql);
		}
		Header ('Location: '.$_POST['backurl']);
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		if ($usrinfo['right_text']) {
			$fres=MySQL_Query ("SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=MySQL_Fetch_Assoc($fres);
			UnLink ('./files/'.$frec['uniquename']);
			MySQL_Query ("DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editperson.php?rid='.$_GET['personid']);
	}
?>