<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete']) && $usrinfo['right_text']) {
	  auditTrail(1, 11, $_REQUEST['delete']);
	  MySQL_Query ("UPDATE ".DB_PREFIX."persons SET deleted=1 WHERE id=".$_REQUEST['delete']);
	  deleteAllUnread (1,$_REQUEST['delete']);
	  Header ('Location: persons.php');
	}
	if (isset($_POST['insertperson']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['name']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
	  pageStart ('Přidána osoba');
		mainMenu (5);
		if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
      		$file=Time().MD5(uniqid(Time().Rand()));
     		move_uploaded_file ($_FILES['portrait']['tmp_name'],'./files/'.$file.'tmp');
			$sdst=resize_Image ('./files/'.$file.'tmp',100,130);
			imagejpeg($sdst,'./files/portraits/'.$file);
			unlink('./files/'.$file.'tmp');
		} else {
		  $file='';
		}
		if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
			$sfile=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
			$sdst=resize_Image ('./files/'.$sfile.'tmp',100,130);
			imagejpeg($sdst,'./files/symbols/'.$sfile);
			unlink('./files/'.$sfile.'tmp');
                        $sql_sy="INSERT INTO ".DB_PREFIX."symbols VALUES('', '".$sfile."', '', 0, '".Time()."', '".$usrinfo['id']."', '".Time()."', '".$usrinfo['id']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
                        MySQL_Query ($sql_sy);
                        $syidarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."symbols WHERE symbol = '".$sfile."'"));
                        $syid=$syidarray['id'];
		} else {
			$sfile='';
                        $syid='';
		}
		$sql_p="INSERT INTO ".DB_PREFIX."persons VALUES('','".mysql_real_escape_string(safeInput($_POST['name']))."','".mysql_real_escape_string(safeInput($_POST['surname']))."','".mysql_real_escape_string(safeInput($_POST['phone']))."','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string($_POST['contents'])."','".$_POST['secret']."','0','".$file."', '".$_POST['side']."', '".$_POST['power']."', '".$_POST['spec']."', '".$syid."','0','0','".Time()."','".$usrinfo['id']."')";
		MySQL_Query ($sql_p);
		$pidarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."persons WHERE UCASE(surname)=UCASE('".mysql_real_escape_string(safeInput($_POST['surname']))."') AND UCASE(name)=UCASE('".mysql_real_escape_string(safeInput($_POST['name']))."') AND side='".$_POST['side']."'"));
		$pid=$pidarray['id'];
		if (!isset($_POST['notnew'])) {
			unreadRecords (1,$pid);
		}
		auditTrail(1, 3, $pid);
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./newperson.php">nová osoba</a> &raquo; <strong>přidána osoba</strong>','<a href="./readperson.php?rid='.$pid.'">zobrazit vytvořené</a> &raquo; <a href="./editperson.php?rid='.$pid.'">úprava osoby</a>');
		echo '<div id="obsah"><p>Osoba vytvořena.</p></div>';
		pageEnd ();
	} else {
	  if (isset($_POST['insertperson'])) {
		  pageStart ('Přidána osoba');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./newperson.php">nová osoba</a> &raquo; <strong>neúspěšné přidání osoby</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['personid']) && isset($_POST['editperson']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['name']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
	  auditTrail(1, 2, $_POST['personid']);
	  pageStart ('Uložení změn');
		mainMenu (5);
		if (!isset($_POST['notnew'])) {
			unreadRecords (1,$_POST['personid']);
		}
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>','<a href="./readperson.php?rid='.$_POST['personid'].'">zobrazit upravené</a>');
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
		if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
			$sps=MySQL_Query ("SELECT symbol FROM ".DB_PREFIX."persons WHERE id=".$_POST['personid']);
			if ($spc=MySQL_Fetch_Assoc($sps)) {
                                $prsn_res=MySQL_Query ("SELECT name, surname FROM ".DB_PREFIX."persons WHERE id=".$_POST['personid']);
                                $prsn_rec=MySQL_Fetch_Assoc($prsn_res);
                                $sdate = "<p>".Date("j/m/Y H:i:s", Time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
                                MySQL_Query ("UPDATE ".DB_PREFIX."symbols SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
			}
			$sfile=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
			$sdst=resize_Image ('./files/'.$sfile.'tmp',100,100);
			imagejpeg($sdst,'./files/symbols/'.$sfile);
			unlink('./files/'.$sfile.'tmp');
                        $sql_sy="INSERT INTO ".DB_PREFIX."symbols VALUES('', '".$sfile."', '', 0, '".Time()."', '".$usrinfo['id']."', '".Time()."', '".$usrinfo['id']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
                        MySQL_Query ($sql_sy);
                        $syidarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."symbols WHERE symbol = '".$sfile."'"));
                        $syid=$syidarray['id'];
                        MySQL_Query ("UPDATE ".DB_PREFIX."persons SET symbol='".$syid."' WHERE id=".$_POST['personid']);
		}
		if ($usrinfo['right_org']==1) {
			MySQL_Query ("UPDATE ".DB_PREFIX."persons SET name='".mysql_real_escape_string(safeInput($_POST['name']))."', surname='".mysql_real_escape_string(safeInput($_POST['surname']))."', phone='".mysql_real_escape_string($_POST['phone'])."', contents='".mysql_real_escape_string($_POST['contents'])."', secret='".(isset($_POST['secret'])?'1':'0')."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."', dead='".(isset($_POST['dead'])?'1':'0')."', archiv='".(isset($_POST['archiv'])?'1':'0')."' WHERE id=".$_POST['personid']);
		} else {
			MySQL_Query ("UPDATE ".DB_PREFIX."persons SET name='".mysql_real_escape_string(safeInput($_POST['name']))."', surname='".mysql_real_escape_string(safeInput($_POST['surname']))."', phone='".mysql_real_escape_string($_POST['phone'])."', datum='".Time()."', iduser='".$usrinfo['id']."', contents='".mysql_real_escape_string($_POST['contents'])."', secret='".$_POST['secret']."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."', dead='".(isset($_POST['dead'])?'1':'0')."', archiv='".(isset($_POST['archiv'])?'1':'0')."' WHERE id=".$_POST['personid']);
		}
		echo '<div id="obsah"><p>Osoba upravena.</p></div>';
		pageEnd ();
	} else {
	  if (isset($_POST['editperson'])) {
		  pageStart ('Uložení změn');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn neúspešné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['personid']) && isset($_POST['orgperson']) && is_numeric($_POST['rdatumday']) && is_numeric($_POST['regusr'])) {
		auditTrail(1, 10, $_POST['personid']);
		pageStart ('Organizační uložení změn');
		mainMenu (5);
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>','<a href="./readperson.php?rid='.$_POST['personid'].'">zobrazit upravené</a>');
		$rdatum = mktime(0,0,0,$_POST['rdatummonth'],$_POST['rdatumday'],$_POST['rdatumyear']);
		MySQL_Query ("UPDATE ".DB_PREFIX."persons SET regdate='".$rdatum."', regid='".$_POST['regusr']."' WHERE id=".$_POST['personid']);
		echo '<div id="obsah"><p>Osoba upravena.</p></div>';
		pageEnd ();
	} else {
		if (isset($_POST['orgperson'])) {
			pageStart ('Uložení změn');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn neúspešné</strong>');
			echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_POST['setgroups'])) {
		auditTrail(1, 6, $_POST['personid']);
		MySQL_Query ("DELETE FROM ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".$_POST['personid']);
		$group=$_POST['group'];
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>','<a href="./readperson.php?rid='.$_POST['personid'].'">zobrazit upravené</a>');
		echo '<div id="obsah"><p>Skupiny pro uživatele uloženy.</p></div>';
		for ($i=0;$i<Count($group);$i++) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."g2p VALUES('".$_POST['personid']."','".$group[$i]."','".$usrinfo['id']."')");
		}
		pageEnd ();
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['personid']) && is_numeric($_POST['secret'])) {
			auditTrail(1, 4, $_POST['personid']);
			$newname=Time().MD5(uniqid(Time().Rand()));
			move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
			$sql="INSERT INTO ".DB_PREFIX."data VALUES('','".$newname."','".mysql_real_escape_string($_FILES['attachment']['name'])."','".mysql_real_escape_string($_FILES['attachment']['type'])."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','1','".$_POST['personid']."','".$_POST['secret']."')";
			MySQL_Query ($sql);
			if (!isset($_POST['fnotnew'])) {
				unreadRecords (1,$_POST['personid']);
			}
			Header ('Location: '.$_POST['backurl']);
	} else {
	  if (isset($_POST['uploadfile'])) {
		  pageStart ('Přiložení souboru');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>přiložení souboru neúspěšné</strong>');
			echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
			pageEnd ();
		}
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
		auditTrail(1, 5, $_POST['personid']);
		if ($usrinfo['right_text']) {
			$fres=MySQL_Query ("SELECT uniquename FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
			$frec=MySQL_Fetch_Assoc($fres);
			UnLink ('./files/'.$frec['uniquename']);
			MySQL_Query ("DELETE FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.id=".$_GET['deletefile']);
		}
		Header ('Location: editperson.php?rid='.$_GET['personid']);
	}
	if (isset($_GET['deletesymbol'])) {
		auditTrail(1, 2, $_GET['personid']);
		if ($usrinfo['right_text']) {
                        $sps=MySQL_Query ("SELECT symbol FROM ".DB_PREFIX."persons WHERE id=".$_GET['personid']);
			$spc=MySQL_Fetch_Assoc($sps);
                        $prsn_res=MySQL_Query ("SELECT name, surname FROM ".DB_PREFIX."persons WHERE id=".$_GET['personid']);
                        $prsn_rec=MySQL_Fetch_Assoc($prsn_res);
                        $sdate = "<p>".Date("j/m/Y H:i:s", Time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
                        MySQL_Query ("UPDATE ".DB_PREFIX."symbols SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
                        MySQL_Query ("UPDATE ".DB_PREFIX."persons SET symbol='' WHERE id=".$_GET['personid']);
		}
		Header ('Location: editperson.php?rid='.$_GET['personid']);
	}
?>