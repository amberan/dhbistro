<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");
       
        // Přidání symbolu
	if (isset($_POST['insertsymbol'])) {
	    $latteParameters['title'] = 'Přidán symbol';
	    mainMenu ();
	    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
	        $sfile = Time().MD5(uniqid(Time().Rand()));
	        move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
	        $sdst = imageResize ('./files/'.$sfile.'tmp',100,100);
	        imagejpeg($sdst,'./files/symbols/'.$sfile);
	        unlink('./files/'.$sfile.'tmp');
	    } else {
	        $sfile = '';
	    }
	    $time = time();
	    $sql_p = "INSERT INTO ".DB_PREFIX."symbol (symbol, `desc`, deleted, created, created_by, modified, modified_by, archiv, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret)  VALUES( '".$sfile."', '".$_POST['contents']."', '0', '".$time."', '".$user['userId']."', '".$time."', '".$user['userId']."', '0', '0', '".$_POST['liner']."', '".$_POST['curver']."', '".$_POST['pointer']."', '".$_POST['geometrical']."', '".$_POST['alphabeter']."', '".$_POST['specialchar']."', 0)";
	    mysqli_query ($database,$sql_p);
	    $sql_f = "SELECT id FROM ".DB_PREFIX."symbol WHERE created='".$time."' AND created_by='".$user['userId']."' AND modified='".$time."' AND modified_by='".$user['userId']."'";
	    $pidarray = mysqli_fetch_assoc (mysqli_query ($database,$sql_f));
	    $pid = $pidarray['id'];
	    auditTrail(7, 3, $pid);
	    if (!isset($_POST['notnew'])) {
	        unreadRecords (7,$pid);
	    }
	    sparklets ('<a href="persons.php">osoby</a> &raquo; <a href="symbols.php">nepřiřazené symboly</a> &raquo; <strong>nový symbol</strong>','<a href="./editsymbol.php?rid='.$pid.'">úprava symbolu</a>');
	    echo '<div id="obsah"><p>Symbol vložen.</p></div>';
	    latteDrawTemplate("footer");
	} else {
	    if (isset($_POST['insertperson'])) {
	        $latteParameters['title'] = 'Nepřidán symbol';
	        mainMenu ();
	        sparklets ('<a href="persons.php">osoby</a> &raquo; <a href="symbols.php">nepřiřazené symboly</a> &raquo; <strong>neúspěšné vložení symbolu</strong>');
	        echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	        latteDrawTemplate("footer");
	    }
	}
        // Vymazani symbolu
	if (isset($_REQUEST['sdelete']) && is_numeric($_REQUEST['sdelete']) && $usrinfo['right_text']) {
	    auditTrail(7, 11, $_REQUEST['sdelete']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."symbol SET deleted=1 WHERE id=".$_REQUEST['sdelete']);
	    deleteAllUnread (7,$_REQUEST['sdelete']);
	    Header ('Location: symbols.php');
	}
        
        // Uprava symbolu
	if (isset($_POST['symbolid'], $_POST['editsymbol']) && $usrinfo['right_text'] ) {
	    auditTrail(7, 2, $_POST['symbolid']);
	    $latteParameters['title'] = 'Uložení změn';
	    mainMenu ();
	    if (!isset($_POST['notnew'])) {
	        unreadRecords (7,$_POST['symbolid']);
	    }
	    sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>','<a href="./readsymbol.php?rid='.$_POST['symbolid'].'">zobrazit upravené</a>');
	    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
	        $sps = mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."symbol WHERE id=".$_POST['symbolid']);
	        if ($spc = mysqli_fetch_assoc ($sps)) {
	            unlink('./files/symbols/'.$spc['symbol']);
	        }
	        $sfile = Time().MD5(uniqid(Time().Rand()));
	        move_uploaded_file ($_FILES['symbol']['tmp_name'],'./files/'.$sfile.'tmp');
	        $sdst = imageResize ('./files/'.$sfile.'tmp',100,100);
	        imagejpeg($sdst,'./files/symbols/'.$sfile);
	        unlink('./files/'.$sfile.'tmp');
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."symbol SET symbol='".$sfile."' WHERE id=".$_POST['symbolid']);
	    }
	    if ($user['aclGamemaster'] == 1) {
	        $sql = "UPDATE ".DB_PREFIX."symbol SET `desc`='".$_POST['desc']."', archiv='".(isset($_POST['archiv']) ? '1' : '0')."', search_lines='".$_POST['liner']."', search_curves='".$_POST['curver']."', search_points='".$_POST['pointer']."', search_geometricals='".$_POST['geometrical']."', search_alphabets='".$_POST['alphabeter']."', search_specialchars='".$_POST['specialchar']."' WHERE id=".$_POST['symbolid'];
	        mysqli_query ($database,$sql);
	    } else {
	        $sql = "UPDATE ".DB_PREFIX."symbol SET `desc`='".$_POST['desc']."', modified='".Time()."', modified_by='".$user['userId']."', archiv='".(isset($_POST['archiv']) ? '1' : '0')."', search_lines='".$_POST['liner']."', search_curves='".$_POST['curver']."', search_points='".$_POST['pointer']."', search_geometricals='".$_POST['geometrical']."', search_alphabets='".$_POST['alphabeter']."', search_specialchars='".$_POST['specialchar']."' WHERE id=".$_POST['symbolid'];
	        mysqli_query ($database,$sql);
	    }
	    echo '<div id="obsah"><p>Symbol upraven.</p></div>';
	    latteDrawTemplate("footer");
	} else {
	    if (isset($_POST['editsymbol'])) {
	        $latteParameters['title'] = 'Uložení změn';
	        mainMenu ();
	        sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn neúspešné</strong>');
	        echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
	        latteDrawTemplate("footer");
	    }
	}
        
        // Ukoly
	if (isset($_REQUEST['acctask']) && is_numeric($_REQUEST['acctask']) && $usrinfo['right_text']) {
	    auditTrail(10, 2, $_REQUEST['acctask']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."task SET status=2, modified='".Time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['acctask']);
	    //		deleteAllUnread (1,$_REQUEST['delete']);
	    Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (isset($_REQUEST['rtrntask']) && is_numeric($_REQUEST['rtrntask']) && $usrinfo['right_text']) {
	    auditTrail(10, 2, $_REQUEST['rtrntask']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."task SET status=0, modified='".Time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['rtrntask']);
	    //		deleteAllUnread (1,$_REQUEST['delete']);
	    Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (isset($_REQUEST['fnshtask']) && is_numeric($_REQUEST['fnshtask'])) {
	    auditTrail(10, 2, $_REQUEST['fnshtask']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."task SET status=1, modified='".Time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['fnshtask']);
	    //		deleteAllUnread (1,$_REQUEST['delete']);
	    Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
	if (isset($_REQUEST['cncltask']) && is_numeric($_REQUEST['cncltask']) && $usrinfo['right_text']) {
	    auditTrail(10, 2, $_REQUEST['cncltask']);
	    mysqli_query ($database,"UPDATE ".DB_PREFIX."task SET status=3, modified='".Time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['cncltask']);
	    //		deleteAllUnread (1,$_REQUEST['delete']);
	    Header ('Location: '.$_SERVER['HTTP_REFERER']);
	}
?>
