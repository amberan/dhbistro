<?php
	require_once ('./inc/func_main.php');

	// úprava poznámky 
	if (isset($_POST['noteid']) && isset($_POST['editnote']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['note']) && is_numeric($_POST['nsecret'])) {
		pageStart ('Uložení změn');
		mainMenu (5);
		switch ($_REQUEST['idtable']) {
			case 1: $sourceurl="persons.php"; $sourcename="osoby"; break;
			case 2: $sourceurl="groups.php"; $sourcename="skupiny"; break;
			case 3: $sourceurl="cases.php"; $sourcename="případy"; break;
			case 4: $sourceurl="reports.php"; $sourcename="hlášení"; break;
			default: $sourceurl=""; $sourcename=""; break;
		}
		if (!isset($_POST['nnotnew'])) {
			unreadRecords ($_REQUEST['idtable'],$_POST['itemid']);
		}
		sparklets ('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>úprava poznámky</strong> &raquo; <strong>uložení změn</strong>');
		MySQL_Query ("UPDATE ".DB_PREFIX."notes SET title='".mysql_real_escape_string(safeInput($_POST['title']))."', datum='".Time()."', note='".mysql_real_escape_string($_POST['note'])."', secret='".$_POST['nsecret']."', iduser='".$_POST['nowner']."' WHERE id=".$_POST['noteid']);
		echo '<div id="obsah"><p>Poznámka upravena.</p></div>';
		pageEnd ();
	} else {
		if (isset($_POST['editnote'])) {
			pageStart ('Uložení změn');
			mainMenu (5);
			switch ($_REQUEST['idtable']) {
			case 1: $sourceurl="persons.php"; $sourcename="osoby"; break;
			case 2: $sourceurl="groups.php"; $sourcename="skupiny"; break;
			case 3: $sourceurl="cases.php"; $sourcename="případy"; break;
			case 4: $sourceurl="reports.php"; $sourcename="hlášení"; break;
			default: $sourceurl=""; $sourcename=""; break;
		}
		sparklets ('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>úprava poznámky</strong> &raquo; <strong>uložení změn</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	
	

	// nová poznámka
	if (isset($_POST['setnote'])) {
		if (!preg_match ('/^[[:blank:]]*$/i',$_POST['note']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && is_numeric($_POST['secret'])) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."notes VALUES('','".mysql_real_escape_string($_POST['note'])."','".mysql_real_escape_string($_POST['title'])."','".Time()."','".$usrinfo['id']."','".$_POST['tableid']."','".$_POST['itemid']."','".$_POST['secret']."','0')");
	//		echo '<div id="obsah"><p>Poznámka upravena.</p></div>';
			if (!isset($_POST['nnotnew'])) {
				unreadRecords ($_POST['tableid'],$_POST['itemid']);
			}
		}
		Header ('Location: '.$_POST['backurl']);
	}
	
	// vymazání poznámky
	if (isset($_GET['deletenote'])) {
		MySQl_Query("UPDATE ".DB_PREFIX."notes SET deleted=1 WHERE ".DB_PREFIX."notes.id=".$_GET['deletenote']);
	//	echo '<div id="obsah"><p>Poznámka smazána.</p></div>';
		Header ('Location: '.URLDecode($_GET['backurl']));
	}



?>