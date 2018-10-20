<?php
	require_once ('./inc/func_main.php');
	if (isset($_POST['reportid'])) {
		$autharray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT iduser FROM ".DB_PREFIX."reports WHERE id=".$_POST['reportid']));
		$author=$autharray['iduser'];
	}
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  auditTrail(4, 11, $_REQUEST['delete']);
	  mysqli_query ($database,"UPDATE ".DB_PREFIX."reports SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  deleteAllUnread($_REQUEST['table'],$_REQUEST['delete']);
	  Header ('Location: reports.php');
	}
	if (isset($_POST['insertrep']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['label']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['task']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['summary']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['impact']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['details']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['start']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['end']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['energy']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['inputs']) && is_numeric($_POST['secret']) && is_numeric($_POST['status']) && is_numeric($_POST['type'])) {
	  //pageStart ('Hlášení uloženo');
	 // mainMenu (4);
	// sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
	  $adatum = mktime(0,0,0,$_POST['adatummonth'],$_POST['adatumday'],$_POST['adatumyear']);
	  $ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysqli_real_escape_string ($database,safeInput($_POST['label']))."')");
	  if (mysqli_num_rows ($ures)) {
	  	pageStart ('Hlášení nepřidáno');
	  	mainMenu (4);
	  	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
	    echo '<div id="obsah"><p>Toto označení hlášení již existuje, změňte ho.</p></div>';
	  } else {
		  	pageStart ('Hlášení uloženo');
		  	mainMenu (4);
		  	mysqli_query ($database,"INSERT INTO ".DB_PREFIX."reports VALUES('','".mysqli_real_escape_string ($database,safeInput($_POST['label']))."','".Time()."','".$usrinfo['id']."','".mysqli_real_escape_string ($database,safeInput($_POST['task']))."','".mysqli_real_escape_string ($database,$_POST['summary'])."','".mysqli_real_escape_string ($database,$_POST['impact'])."','".mysqli_real_escape_string ($database,$_POST['details'])."','".$_POST['secret']."','0','".$_POST['status']."','".$_POST['type']."','".$adatum."','".mysqli_real_escape_string ($database,safeInput($_POST['start']))."','".mysqli_real_escape_string ($database,safeInput($_POST['end']))."','".mysqli_real_escape_string ($database,$_POST['energy'])."','".mysqli_real_escape_string ($database,$_POST['inputs'])."')");
		  	$ridarray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysqli_real_escape_string ($database,safeInput($_POST['label']))."')"));
			$rid=$ridarray['id'];
			auditTrail(4, 3, $rid);
			if ($_POST['status']  <> 0) {
				unreadRecords (4,$rid);
		  	}
			sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení uloženo</strong>','<a href="readactrep.php?rid='.$rid.'&hidenotes=0&truenames=0">zobrazit uložené</a>');
			echo '<div id="obsah"><p>Hlášení uloženo.</p></div>
			<hr />
			<form action="addp2ar.php" method="post" class="otherform">
			<div>
			<input type="hidden" name="rid" value="'.$rid.'" />
			<input type="submit" value="Přidat k hlášení přítomné osoby" name="setperson" class="submitbutton" />
			</div>
			</form>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['insertrep'])) {
		  pageStart ('Hlášení nepřidáno!!!');
			mainMenu (4);
			sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva. Pamatujte, že všechna pole musí být vyplněná.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['reportid']) && isset($_POST['editactrep']) && ($usrinfo['right_text'] || $usrinfo['id']==$author) && !preg_match ('/^[[:blank:]]*$/i',$_POST['label']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['task']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['summary']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['impacts']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['details']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
	  auditTrail(4, 2, $_POST['reportid']);
	  pageStart ('Uložení změn');
	  mainMenu (4);
	  if ($_POST['status']  <> 0) {
	  	unreadRecords (4,$_POST['reportid']);
	  }
	  sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>','<a href="readactrep.php?rid='.$_POST['reportid'].'&hidenotes=0&truenames=0">zobrazit upravené</a>');
	  $adatum = mktime(0,0,0,$_POST['adatummonth'],$_POST['adatumday'],$_POST['adatumyear']);
	  $ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysqli_real_escape_string ($database,safeInput($_POST['label']))."') AND id<>".$_POST['reportid']);
	  if (mysqli_num_rows ($ures)) {
	    echo '<div id="obsah"><p>Toto označení již existuje, změňte ho.</p></div>';
	  } else {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."reports SET label='".mysqli_real_escape_string ($database,safeInput($_POST['label']))."', task='".mysqli_real_escape_string ($database,safeInput($_POST['task']))."', summary='".mysqli_real_escape_string ($database,$_POST['summary'])."', impacts='".mysqli_real_escape_string ($database,$_POST['impacts'])."', details='".mysqli_real_escape_string ($database,$_POST['details'])."', secret='".$_POST['secret']."', status='".$_POST['status']."', adatum='".$adatum."', start='".mysqli_real_escape_string ($database,safeInput($_POST['start']))."', end='".mysqli_real_escape_string ($database,safeInput($_POST['end']))."', energy='".mysqli_real_escape_string ($database,$_POST['energy'])."', inputs='".mysqli_real_escape_string ($database,$_POST['inputs'])."' WHERE id=".$_POST['reportid']);
			echo '<div id="obsah"><p>Hlášení upraveno.</p></div>';
		}
		pageEnd ();
	} else {
	  if (isset($_POST['editactrep'])) {
		  pageStart ('Uložení změn');
			mainMenu (4);
			sparklets ('<a href="./cases.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn neúspěšné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva. Pamatujte, že žádné pole nesmí být prázdné.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['reportid']) && is_numeric($_POST['secret'])) {
		auditTrail(4, 4, $_POST['reportid']);
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysqli_real_escape_string ($database,$_FILES['attachment']['name'])."','".mysqli_real_escape_string ($database,$_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','4','".$_POST['reportid']."','".$_POST['secret']."')";
		mysqli_query ($database,$sql);
		unreadRecords (4,$_POST['reportid']);
		Header ('Location: '.$_POST['backurl']);
	} else {
	  if (isset($_POST['uploadfile'])) {
		  pageStart ('Přiložení souboru');
			mainMenu (4);
			sparklets ('<a href="./cases.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>přiložení souboru</strong>');
			echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		auditTrail(4, 5, $_GET['reportid']);
		if ($usrinfo['right_text']) {
			$fres=mysqli_query ($database,"SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=mysqli_fetch_assoc ($fres);
			UnLink ('./files/'.$frec['uniquename']);
			mysqli_query ($database,"DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editactrep.php?rid='.$_GET['reportid']);
	}
?>