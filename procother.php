<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');

        
        // Úprava nástěnky
	if (isset($_POST['editdashboard'])) {
		auditTrail(6, 2, 0);
		pageStart ('Upravena nástěnka');
		mainMenu (5);
		sparklets ('<a href="dashboard.php">nástěnka</a> &raquo; <strong>nástěnka upravena</strong>');
		$sql="INSERT INTO ".DB_PREFIX."dashboard VALUES('','".Time()."','".$usrinfo['id']."','".mysqli_real_escape_string ($database,safeInput($_POST['contents']))."')";
		mysqli_query ($database,$sql);
		unreadRecords (6,0);
		echo '<div id="obsah"><p>Nástěnka upravena.</p></div>';
		pageEnd ();
	}
        
        // Přidání symbolu
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
		$sql_p="INSERT INTO ".DB_PREFIX."symbols VALUES('', '".$sfile."', '".mysqli_real_escape_string ($database,$_POST['contents'])."', '0', '".$time."', '".$usrinfo['id']."', '".$time."', '".$usrinfo['id']."', '0', '0', '".mysqli_real_escape_string ($database,$_POST['liner'])."', '".mysqli_real_escape_string ($database,$_POST['curver'])."', '".mysqli_real_escape_string ($database,$_POST['pointer'])."', '".mysqli_real_escape_string ($database,$_POST['geometrical'])."', '".mysqli_real_escape_string ($database,$_POST['alphabeter'])."', '".mysqli_real_escape_string ($database,$_POST['specialchar'])."', 0)";
                mysqli_query ($database,$sql_p);
		$sql_f="SELECT id FROM ".DB_PREFIX."symbols WHERE created='".$time."' AND created_by='".$usrinfo['id']."' AND modified='".$time."' AND modified_by='".$usrinfo['id']."'";
		$pidarray=mysqli_fetch_assoc (mysqli_query ($database,$sql_f));
		$pid=$pidarray['id'];
		auditTrail(7, 3, $pid);
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
        // Vymazani symbolu
	if (isset($_REQUEST['sdelete']) && is_numeric($_REQUEST['sdelete']) && $usrinfo['right_text']) {
		auditTrail(7, 11, $_REQUEST['sdelete']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."symbols SET deleted=1 WHERE id=".$_REQUEST['sdelete']);
		deleteAllUnread (7,$_REQUEST['sdelete']);
		Header ('Location: symbols.php');
	}
        
        // Uprava symbolu
	if (isset($_POST['symbolid']) && isset($_POST['editsymbol']) && $usrinfo['right_text'] ) {
		auditTrail(7, 2, $_POST['symbolid']);
		pageStart ('Uložení změn');
		mainMenu (5);
		if (!isset($_POST['notnew'])) {
			unreadRecords (7,$_POST['symbolid']);
		}
		sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>','<a href="./readsymbol.php?rid='.$_POST['symbolid'].'">zobrazit upravené</a>');
		if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
			$sps=mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."symbols WHERE id=".$_POST['symbolid']);
			if ($spc=mysqli_fetch_assoc ($sps)) {
				unlink('./files/symbols/'.$spc['symbol']);
			}
			$sfile=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
			$sdst=resize_Image ('./files/'.$sfile.'tmp',100,100);
			imagejpeg($sdst,'./files/symbols/'.$sfile);
			unlink('./files/'.$sfile.'tmp');
			mysqli_query ($database,"UPDATE ".DB_PREFIX."symbols SET symbol='".$sfile."' WHERE id=".$_POST['symbolid']);
		}
		if ($usrinfo['right_org']==1) {
			$sql="UPDATE ".DB_PREFIX."symbols SET `desc`='".mysqli_real_escape_string ($database,$_POST['desc'])."', archiv='".(isset($_POST['archiv'])?'1':'0')."', search_lines='".$_POST['liner']."', search_curves='".$_POST['curver']."', search_points='".$_POST['pointer']."', search_geometricals='".$_POST['geometrical']."', search_alphabets='".$_POST['alphabeter']."', search_specialchars='".$_POST['specialchar']."' WHERE id=".$_POST['symbolid'];
                        mysqli_query ($database,$sql);
		} else {
			$sql="UPDATE ".DB_PREFIX."symbols SET `desc`='".mysqli_real_escape_string ($database,$_POST['desc'])."', modified='".Time()."', modified_by='".$usrinfo['id']."', archiv='".(isset($_POST['archiv'])?'1':'0')."', search_lines='".$_POST['liner']."', search_curves='".$_POST['curver']."', search_points='".$_POST['pointer']."', search_geometricals='".$_POST['geometrical']."', search_alphabets='".$_POST['alphabeter']."', search_specialchars='".$_POST['specialchar']."' WHERE id=".$_POST['symbolid'];
			mysqli_query ($database,$sql);
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
        
        // Ukoly
	if (isset($_REQUEST['acctask']) && is_numeric($_REQUEST['acctask']) && $usrinfo['right_text']) {
		auditTrail(10, 2, $_REQUEST['acctask']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."tasks SET status=2, modified='".Time()."', modified_by='".$usrinfo['id']."' WHERE id=".$_REQUEST['acctask']);
//		deleteAllUnread (1,$_REQUEST['delete']);
		Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (isset($_REQUEST['rtrntask']) && is_numeric($_REQUEST['rtrntask']) && $usrinfo['right_text']) {
		auditTrail(10, 2, $_REQUEST['rtrntask']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."tasks SET status=0, modified='".Time()."', modified_by='".$usrinfo['id']."' WHERE id=".$_REQUEST['rtrntask']);
		//		deleteAllUnread (1,$_REQUEST['delete']);
		Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (isset($_REQUEST['fnshtask']) && is_numeric($_REQUEST['fnshtask'])) {
		auditTrail(10, 2, $_REQUEST['fnshtask']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."tasks SET status=1, modified='".Time()."', modified_by='".$usrinfo['id']."' WHERE id=".$_REQUEST['fnshtask']);
		//		deleteAllUnread (1,$_REQUEST['delete']);
		Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (isset($_REQUEST['cncltask']) && is_numeric($_REQUEST['cncltask']) && $usrinfo['right_text']) {
		auditTrail(10, 2, $_REQUEST['cncltask']);
		mysqli_query ($database,"UPDATE ".DB_PREFIX."tasks SET status=3, modified='".Time()."', modified_by='".$usrinfo['id']."' WHERE id=".$_REQUEST['cncltask']);
		//		deleteAllUnread (1,$_REQUEST['delete']);
		Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}	
?>