<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);




	if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete']) && $usrinfo['right_text']) {
	    auditTrail(1, 11, $_REQUEST['delete']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET deleted=1 WHERE id=".$_REQUEST['delete']);
	    deleteAllUnread (1,$_REQUEST['delete']);
	    Header ('Location: persons.php');
	}
	if (isset($_POST['insertperson']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['name']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
	    $latteParameters['title'] = 'Přidána osoba';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	    mainMenu (5);
	    if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
	        $file = Time().MD5(uniqid(Time().Rand()));
	        move_uploaded_file ($_FILES['portrait']['tmp_name'],'./files/'.$file.'tmp');
	        $sdst = resize_Image ('./files/'.$file.'tmp',100,130);
	        imagejpeg($sdst,'./files/portraits/'.$file);
	        unlink('./files/'.$file.'tmp');
	    } else {
	        $file = '';
	    }
	    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
	        $sfile = Time().MD5(uniqid(Time().Rand()));
	        move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
	        $sdst = resize_Image ('./files/'.$sfile.'tmp',100,130);
	        imagejpeg($sdst,'./files/symbols/'.$sfile);
	        unlink('./files/'.$sfile.'tmp');
	        $sql_sy = "INSERT INTO ".DB_PREFIX."symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archiv, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '".$sfile."', '', 0, '".Time()."', '".$usrinfo['id']."', '".Time()."', '".$usrinfo['id']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
	        mysqli_query ($database,$sql_sy);
	        $syidarray = mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."symbol WHERE symbol = '".$sfile."'"));
	        $syid = $syidarray['id'];
	    } else {
	        $sfile = '';
	        $syid = '';
	    }
	    $sql_p = "INSERT INTO ".DB_PREFIX."person (name, surname, phone, datum, iduser, contents, secret, deleted, portrait, side, power, spec, symbol, dead, archiv, regdate, regid) VALUES('".$_POST['name']."','".$_POST['surname']."','".$_POST['phone']."','".Time()."','".$usrinfo['id']."','".$_POST['contents']."','".$_POST['secret']."','0','".$file."', '".$_POST['side']."', '".$_POST['power']."', '".$_POST['spec']."', '".$syid."','0','0','".Time()."','".$usrinfo['id']."')";
	    mysqli_query ($database,$sql_p);
	    $pidarray = mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."person WHERE UCASE(surname)=UCASE('".$_POST['surname']."') AND UCASE(name)=UCASE('".$_POST['name']."') AND side='".$_POST['side']."'"));
	    $pid = $pidarray['id'];
	    if (!isset($_POST['notnew'])) {
	        unreadRecords (1,$pid);
	    }
	    auditTrail(1, 3, $pid);
	    sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./newperson.php">nová osoba</a> &raquo; <strong>přidána osoba</strong>','<a href="./readperson.php?rid='.$pid.'">zobrazit vytvořené</a> &raquo; <a href="./editperson.php?rid='.$pid.'">úprava osoby</a>');
	    echo '<div id="obsah"><p>Osoba vytvořena.</p></div>';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	} else {
	    if (isset($_POST['insertperson'])) {
	        $latteParameters['title'] = 'Přidána osoba';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	        mainMenu (5);
	        sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./newperson.php">nová osoba</a> &raquo; <strong>neúspěšné přidání osoby</strong>');
	        echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	    }
	}
	if (isset($_POST['personid'], $_POST['editperson']) && $usrinfo['right_text'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['name']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
	    auditTrail(1, 2, $_POST['personid']);

	    $latteParameters['title'] = 'Uložení změn';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

	    mainMenu (5);
	    if (!isset($_POST['notnew'])) {
	        unreadRecords (1,$_POST['personid']);
	    }
	    sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>','<a href="./readperson.php?rid='.$_POST['personid'].'">zobrazit upravené</a>');
	    if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
	        $ps = mysqli_query ($database,"SELECT portrait FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
	        if ($pc = mysqli_fetch_assoc ($ps)) {
	            unlink('./files/portraits/'.$pc['portrait']);
	        }
	        $file = Time().MD5(uniqid(Time().Rand()));
	        move_uploaded_file ($_FILES['portrait']['tmp_name'],'./files/'.$file.'tmp');
	        $dst = resize_Image ('./files/'.$file.'tmp',100,130);
	        imagejpeg($dst,'./files/portraits/'.$file);
	        unlink('./files/'.$file.'tmp');
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET portrait='".$file."' WHERE id=".$_POST['personid']);
	    }
	    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
	        $sps = mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
	        if ($spc = mysqli_fetch_assoc ($sps)) {
	            $prsn_res = mysqli_query ($database,"SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
	            $prsn_rec = mysqli_fetch_assoc ($prsn_res);
	            $sdate = "<p>".Date("j/m/Y H:i:s", Time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
	            mysqli_query ($database,"UPDATE ".DB_PREFIX."symbol SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
	        }
	        $sfile = Time().MD5(uniqid(Time().Rand()));
	        move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
	        $sdst = resize_Image ('./files/'.$sfile.'tmp',100,100);
	        imagejpeg($sdst,'./files/symbols/'.$sfile);
	        unlink('./files/'.$sfile.'tmp');
	        $sql_sy = "INSERT INTO ".DB_PREFIX."symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archiv, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '".$sfile."', '', 0, '".Time()."', '".$usrinfo['id']."', '".Time()."', '".$usrinfo['id']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
	        mysqli_query ($database,$sql_sy);
	        $syidarray = mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."symbol WHERE symbol = '".$sfile."'"));
	        $syid = $syidarray['id'];
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET symbol='".$syid."' WHERE id=".$_POST['personid']);
	    }
	    if ($usrinfo['right_org'] == 1) {
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET name='".$_POST['name']."', surname='".$_POST['surname']."', phone='".$_POST['phone']."', contents='".$_POST['contents']."', secret='".$_POST['secret']."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."', dead='".(isset($_POST['dead']) ? '1' : '0')."', archiv='".(isset($_POST['archiv']) ? '1' : '0')."' WHERE id=".$_POST['personid']);
	    } else {
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET name='".$_POST['name']."', surname='".$_POST['surname']."', phone='".$_POST['phone']."', datum='".Time()."', iduser='".$usrinfo['id']."', contents='".$_POST['contents']."', secret='".$_POST['secret']."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."', dead='".(isset($_POST['dead']) ? '1' : '0')."', archiv='".(isset($_POST['archiv']) ? '1' : '0')."' WHERE id=".$_POST['personid']);
	    }
	    echo '<div id="obsah"><p>Osoba upravena.</p></div>';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	} else {
	    if (isset($_POST['editperson'])) {
	        $latteParameters['title'] = 'Uložení změn';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
  
	        mainMenu (5);
	        sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn neúspešné</strong>');
	        echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	    }
	}
	if (isset($_POST['personid'], $_POST['orgperson']) && is_numeric($_POST['rdatumday']) && is_numeric($_POST['regusr'])) {
	    auditTrail(1, 10, $_POST['personid']);

	    $latteParameters['title'] = 'Organizační uložení změn';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
  
	    mainMenu (5);
	    sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>','<a href="./readperson.php?rid='.$_POST['personid'].'">zobrazit upravené</a>');
	    $rdatum = mktime(0,0,0,$_POST['rdatummonth'],$_POST['rdatumday'],$_POST['rdatumyear']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET regdate='".$rdatum."', regid='".$_POST['regusr']."' WHERE id=".$_POST['personid']);
	    echo '<div id="obsah"><p>Osoba upravena.</p></div>';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	} else {
	    if (isset($_POST['orgperson'])) {
	        $latteParameters['title'] = 'Uložení změn';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	  
	        mainMenu (5);
	        sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn neúspešné</strong>');
	        echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	    }
	}
	if (isset($_POST['setgroups'])) {
	    auditTrail(1, 6, $_POST['personid']);
	    mysqli_query ($database,"DELETE FROM ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".$_POST['personid']);
	    $group = $_POST['group'];
	    $latteParameters['title'] = 'Uložení změn';
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
  
	    mainMenu (5);
	    sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>uložení změn</strong>','<a href="./readperson.php?rid='.$_POST['personid'].'">zobrazit upravené</a>');
	    echo '<div id="obsah"><p>Skupiny pro uživatele uloženy.</p></div>';
	    for ($i = 0;$i < Count($group);$i++) {
	        mysqli_query ($database,"INSERT INTO ".DB_PREFIX."g2p VALUES('".$_POST['personid']."','".$group[$i]."','".$usrinfo['id']."')");
	    }
	    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	}
	if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['personid']) && is_numeric($_POST['secret'])) {
	    auditTrail(1, 4, $_POST['personid']);
	    $newname = Time().MD5(uniqid(Time().Rand()));
	    move_uploaded_file ($_FILES['attachment']['tmp_name'],'./files/'.$newname);
	    $sql = "INSERT INTO ".DB_PREFIX."file VALUES('','".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".Time()."','".$usrinfo['id']."','1','".$_POST['personid']."','".$_POST['secret']."')";
	    mysqli_query ($database,$sql);
	    if (!isset($_POST['fnotnew'])) {
	        unreadRecords (1,$_POST['personid']);
	    }
	    Header ('Location: '.$_POST['backurl']);
	} else {
	    if (isset($_POST['uploadfile'])) {
	        $latteParameters['title'] = 'Přiložení souboru';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	        mainMenu (5);
	        sparklets ('<a href="./persons.php">osoby</a> &raquo; <a href="./editperson.php?rid='.$_POST['personid'].'">úprava osoby</a> &raquo; <strong>přiložení souboru neúspěšné</strong>');
	        echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
	        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	    }
	}
	if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
	    auditTrail(1, 5, $_POST['personid']);
	    if ($usrinfo['right_text']) {
	        $fres = mysqli_query ($database,"SELECT uniquename FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
	        $frec = mysqli_fetch_assoc ($fres);
	        UnLink ('./files/'.$frec['uniquename']);
	        mysqli_query ($database,"DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
	    }
	    Header ('Location: editperson.php?rid='.$_GET['personid']);
	}
	if (isset($_GET['deletesymbol'])) {
	    auditTrail(1, 2, $_GET['personid']);
	    if ($usrinfo['right_text']) {
	        $sps = mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."person WHERE id=".$_GET['personid']);
	        $spc = mysqli_fetch_assoc ($sps);
	        $prsn_res = mysqli_query ($database,"SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_GET['personid']);
	        $prsn_rec = mysqli_fetch_assoc ($prsn_res);
	        $sdate = "<p>".Date("j/m/Y H:i:s", Time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."symbol SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."person SET symbol='' WHERE id=".$_GET['personid']);
	    }
	    Header ('Location: editperson.php?rid='.$_GET['personid']);
	}
?>