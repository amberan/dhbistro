<?php
	require_once ('./inc/func_main.php');
	if (isset($_POST['reportid'])) {
		$autharray=MySQL_Fetch_Assoc(MySQL_Query("SELECT iduser FROM ".DB_PREFIX."reports WHERE id=".$_POST['reportid']));
		$author=$autharray['iduser'];
	}
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
	  MySQL_Query ("UPDATE ".DB_PREFIX."reports SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  Header ('Location: reports.php');
	}
	if (isset($_POST['insertrep']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['label']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['task']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['summary']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['impact']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['details']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['start']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['end']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['energy']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['inputs']) && is_numeric($_POST['secret']) && is_numeric($_POST['status']) && is_numeric($_POST['type'])) {
	  //pageStart ('Hlášení uloženo');
	 // mainMenu (4);
	// sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
	  $adatum = mktime(0,0,0,$_POST['adatummonth'],$_POST['adatumday'],$_POST['adatumyear']);
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysql_real_escape_string(safeInput($_POST['label']))."')");
	  if (MySQL_Num_Rows ($ures)) {
	  	pageStart ('Hlášení nepřidáno');
	  	mainMenu (4);
	  	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení nepřidáno</strong>');
	    echo '<div id="obsah"><p>Toto označení hlášení již existuje, změňte ho.</p></div>';
	  } else {
		  	pageStart ('Hlášení uloženo');
		  	mainMenu (4);
		  	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení uloženo</strong>');
			MySQL_Query ("INSERT INTO ".DB_PREFIX."reports VALUES('','".mysql_real_escape_string(safeInput($_POST['label']))."','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string($_POST['task'])."','".mysql_real_escape_string($_POST['summary'])."','".mysql_real_escape_string($_POST['impact'])."','".mysql_real_escape_string($_POST['details'])."','".$_POST['secret']."','0','".$_POST['status']."','".$_POST['type']."','".$adatum."','".mysql_real_escape_string(safeInput($_POST['start']))."','".mysql_real_escape_string(safeInput($_POST['end']))."','".mysql_real_escape_string($_POST['energy'])."','".mysql_real_escape_string($_POST['inputs'])."')");
			$ridarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysql_real_escape_string(safeInput($_POST['label']))."')"));
			$rid=$ridarray['id'];
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
	  pageStart ('Uložení změn');
	  mainMenu (4);
	  sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>');
	  $adatum = mktime(0,0,0,$_POST['adatummonth'],$_POST['adatumday'],$_POST['adatumyear']);
	  $ures=MySQL_Query ("SELECT id FROM ".DB_PREFIX."reports WHERE UCASE(label)=UCASE('".mysql_real_escape_string(safeInput($_POST['label']))."') AND id<>".$_POST['reportid']);
	  if (MySQL_Num_Rows ($ures)) {
	    echo '<div id="obsah"><p>Toto označení již existuje, změňte ho.</p></div>';
	  } else {
			MySQL_Query ("UPDATE ".DB_PREFIX."reports SET label='".mysql_real_escape_string(safeInput($_POST['label']))."', task='".mysql_real_escape_string(safeInput($_POST['task']))."', summary='".mysql_real_escape_string($_POST['summary'])."', impacts='".mysql_real_escape_string(safeInput($_POST['impacts']))."', details='".mysql_real_escape_string(safeInput($_POST['details']))."', secret='".$_POST['secret']."', status='".$_POST['status']."', adatum='".$adatum."', start='".mysql_real_escape_string(safeInput($_POST['start']))."', end='".mysql_real_escape_string(safeInput($_POST['end']))."', energy='".mysql_real_escape_string($_POST['energy'])."', inputs='".mysql_real_escape_string($_POST['inputs'])."' WHERE id=".$_POST['reportid']);
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
		$newname=Time().MD5(uniqid(Time().Rand()));
		move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
		$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','4','".$_POST['reportid']."','".$_POST['secret']."')";
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
?>