<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delallnew'])) {
	  MySQL_Query ("TRUNCATE ".DB_PREFIX."unread_".$usrinfo['id']);
	  Header ('Location: '.$_REQUEST['delallnew']);
	}
	if (isset($_POST['editdashboard'])) {
		pageStart ('Upravena nástěnka');
		mainMenu (5);
		sparklets ('<a href="dashboard.php">nástěnka</a> &raquo; <strong>nástěnka upravena</strong>');
		$sql="INSERT INTO ".DB_PREFIX."dashboard VALUES('','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string(safeInput($_POST['contents']))."')";
		MySQL_Query ($sql);
		unreadRecords (6,0);
		echo '<div id="obsah"><p>Nástěnka upravena.</p></div>';
		pageEnd ();
	}
	if (isset($_POST['insertsymbol'])) {
		pageStart ('Přidán symbol');
		mainMenu (5);
		if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
			$sfile=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
			$sdst=resize_Image ('./files/'.$sfile.'tmp',100,100);
			imagejpeg($sdst,'./files/symbols/'.$sfile);
			unlink('./files/'.$sfile.'tmp');
		} else {
			$sfile='';
		}
		$time=time();
		$sql_p="INSERT INTO ".DB_PREFIX."symbols VALUES('', '".$sfile."', '".mysql_real_escape_string($_POST['contents'])."', '0', '".$time."', '".$usrinfo['id']."', '".$time."', '".$usrinfo['id']."', '0')";
		MySQL_Query ($sql_p);
		$sql_f="SELECT id FROM ".DB_PREFIX."symbols WHERE created='".$time."' AND created_by='".$usrinfo['id']."' AND modified='".$time."' AND modified_by='".$usrinfo['id']."'";
		$pidarray=MySQL_Fetch_Assoc(MySQL_Query($sql_f));
		$pid=$pidarray['id'];
		if (!isset($_POST['notnew'])) {
			unreadRecords (7,$pid);
		}
		sparklets ('<a href="persons.php">osoby</a> &raquo; <a href="symbols.php">nepřiřazené symboly</a> &raquo; <strong>nový symbol</strong>','<a href="./editsymbol.php?rid='.$pid.'">úprava symbolu</a>');
		echo '<div id="obsah"><p>Symbol vložen.</p></div>';
		pageEnd ();
	} else {
		if (isset($_POST['insertperson'])) {
			pageStart ('Nepřidán symbol');
			mainMenu (5);
			sparklets ('<a href="persons.php">osoby</a> &raquo; <a href="symbols.php">nepřiřazené symboly</a> &raquo; <strong>neúspěšné vložení symbolu</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_REQUEST['sdelete']) && is_numeric($_REQUEST['sdelete']) && $usrinfo['right_text']) {
		MySQL_Query ("UPDATE ".DB_PREFIX."symbols SET deleted=1 WHERE id=".$_REQUEST['sdelete']);
		deleteAllUnread (7,$_REQUEST['sdelete']);
		Header ('Location: symbols.php');
	}
	if (isset($_POST['symbolid']) && isset($_POST['editsymbol']) && $usrinfo['right_text'] ) {
		pageStart ('Uložení změn');
		mainMenu (5);
		if (!isset($_POST['notnew'])) {
			unreadRecords (7,$_POST['symbolid']);
		}
		sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
		if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
			$sps=MySQL_Query ("SELECT symbol FROM ".DB_PREFIX."symbols WHERE id=".$_POST['symbolid']);
			if ($spc=MySQL_Fetch_Assoc($sps)) {
				unlink('./files/symbols/'.$spc['symbol']);
			}
			$sfile=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
			$sdst=resize_Image ('./files/'.$sfile.'tmp',100,100);
			imagejpeg($sdst,'./files/symbols/'.$sfile);
			unlink('./files/'.$sfile.'tmp');
			MySQL_Query ("UPDATE ".DB_PREFIX."symbols SET symbol='".$sfile."' WHERE id=".$_POST['symbolid']);
		}
		if ($usrinfo['right_org']==1) {
			$sql="UPDATE ".DB_PREFIX."symbols SET `desc`='".mysql_real_escape_string($_POST['desc'])."', archiv='".(isset($_POST['archiv'])?'1':'0')."' WHERE id=".$_POST['symbolid'];
			MySQL_Query ($sql);
		} else {
			$sql="UPDATE ".DB_PREFIX."symbols SET `desc`='".mysql_real_escape_string($_POST['desc'])."', modified='".Time()."', modified_by='".$usrinfo['id']."', archiv='".(isset($_POST['archiv'])?'1':'0')."' WHERE id=".$_POST['symbolid'];
			MySQL_Query ();
		}
		echo '<div id="obsah"><p>Symbol upraven.</p></div>';
		pageEnd ();
	} else {
		if (isset($_POST['editsymbol'])) {
			pageStart ('Uložení změn');
			mainMenu (5);
			sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn neúspešné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
?>