<?php

use Tracy\Debugger;

if (isset($URL[2], $URL[3]) && $URL[2] == 'delete' && is_numeric($URL[3])) {
    personDelete($URL[3]);
}
if (isset($URL[2], $URL[3]) && $URL[2] == 'restore' && is_numeric($URL[3])) {
    personRestore($URL[3]);
}

// DELETE
if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete']) && $user['aclPerson'] > 1) {
    authorizedAccess(1, 11, $_REQUEST['delete']);
    mysqli_query($database, "UPDATE ".DB_PREFIX."person SET deleted=1 WHERE id=".$_REQUEST['delete']);
    deleteAllUnread(1, $_REQUEST['delete']);
}
// NEW
if (isset($_POST['insertperson']) && !preg_match('/^[[:blank:]]*$/i', $_POST['name']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
    if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
        $file = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['portrait']['tmp_name'], './files/'.$file.'tmp');
        $sdst = imageResize('./files/'.$file.'tmp', 100, 130);
        imagejpeg($sdst, './files/portraits/'.$file);
        unlink('./files/'.$file.'tmp');
    } else {
        $file = '';
    }
    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
        $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
        $sdst = imageResize('./files/'.$sfile.'tmp', 100, 130);
        imagejpeg($sdst, './files/symbols/'.$sfile);
        unlink('./files/'.$sfile.'tmp');
        $sql_sy = "INSERT INTO ".DB_PREFIX."symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '".$sfile."', '', 0, '".time()."', '".$user['userId']."', '".time()."', '".$user['userId']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
        mysqli_query($database, $sql_sy);
        $syidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."symbol WHERE symbol = '".$sfile."'"));
        $syid = $syidarray['id'];
    } else {
        $sfile = '';
        $syid = '';
    }
    if ($_POST['personRoof'] > null) {
        $updateRoof = 'current_timestamp';
    } else {
        $updateRoof = 'null';
    }
    $updateDate = $rdatum = time();
    if ($user['aclGamemaster'] == 1) {
        $rdatum = mktime(0, 0, 0, $_POST['rdatummonth'], $_POST['rdatumday'], $_POST['rdatumyear']);
        $updateDate = rand($rdatum, time());
    }


    $sql_p = "INSERT INTO ".DB_PREFIX."person (roof, name, surname, phone, datum, iduser, contents, secret, deleted, portrait, side, power, spec, symbol, dead, archived, regdate, regid)
            VALUES(".$updateRoof.",'".$_POST['name']."','".$_POST['surname']."','".$_POST['phone']."','".$updateDate."','".$user['userId']."','".$_POST['contents']."','".$_POST['secret']."','0','".$file."', '".$_POST['side']."', '".$_POST['power']."', '".$_POST['spec']."', '".$syid."','0',null,'".$rdatum."','".$user['userId']."')";
    mysqli_query($database, $sql_p);
    $pidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."person WHERE UCASE(surname)=UCASE('".$_POST['surname']."') AND UCASE(name)=UCASE('".$_POST['name']."') AND side='".$_POST['side']."'"));
    $pid = $pidarray['id'];
    if (!isset($_POST['notnew'])) {
        unreadRecords(1, $pid);
    }
    authorizedAccess(1, 3, $pid);
    $_SESSION['message'] = 'Osoba vytvořena.';
} else {
    if (isset($_POST['insertperson'])) {
        $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
//EDIT
if (isset($_POST['personid'], $_POST['editperson']) && $user['aclPerson'] && !preg_match('/^[[:blank:]]*$/i', $_POST['name']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
    authorizedAccess(1, 2, $_POST['personid']);
    if (!isset($_POST['notnew'])) {
        unreadRecords(1, $_POST['personid']);
    }
    if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
        $ps = mysqli_query($database, "SELECT portrait FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
        if ($pc = mysqli_fetch_assoc($ps)) {
            unlink('./files/portraits/'.$pc['portrait']);
        }
        $file = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['portrait']['tmp_name'], './files/'.$file.'tmp');
        $dst = imageResize('./files/'.$file.'tmp', 100, 130);
        imagejpeg($dst, './files/portraits/'.$file);
        unlink('./files/'.$file.'tmp');
        mysqli_query($database, "UPDATE ".DB_PREFIX."person SET portrait='".$file."' WHERE id=".$_POST['personid']);
    }
    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
        $sps = mysqli_query($database, "SELECT symbol FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
        if ($spc = mysqli_fetch_assoc($sps)) {
            $prsn_res = mysqli_query($database, "SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']);
            $prsn_rec = mysqli_fetch_assoc($prsn_res);
            $sdate = "<p>".date("j/m/Y H:i:s", time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
            mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
        }
        $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
        $sdst = imageResize('./files/'.$sfile.'tmp', 100, 100);
        imagejpeg($sdst, './files/symbols/'.$sfile);
        unlink('./files/'.$sfile.'tmp');
        $sql_sy = "INSERT INTO ".DB_PREFIX."symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '".$sfile."', '', 0, '".time()."', '".$user['userId']."', '".time()."', '".$user['userId']."', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
        mysqli_query($database, $sql_sy);
        $syidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."symbol WHERE symbol = '".$sfile."'"));
        $syid = $syidarray['id'];
        mysqli_query($database, "UPDATE ".DB_PREFIX."person SET symbol='".$syid."' WHERE id=".$_POST['personid']);
    }
    personCheckboxUpdate($_POST['personid'], 'archived', $_POST['archiv']);
    personCheckboxUpdate($_POST['personid'], 'roof', $_POST['personRoof']);
    $sqlPlayer = '';
    if ($user['aclGamemaster'] != 1) {
        $sqlPlayer = "datum='".time()."', iduser='".$user['userId']."',";
    }
    $update = "UPDATE ".DB_PREFIX."person SET name='".$_POST['name']."', surname='".$_POST['surname']."', phone='".$_POST['phone']."', ".$sqlPlayer."
        contents='".$_POST['contents']."', secret='".$_POST['secret']."', side='".$_POST['side']."', power='".$_POST['power']."', spec='".$_POST['spec']."',
        dead='".(isset($_POST['dead']) ? '1' : '0')."' WHERE id=".$_POST['personid'];

    Debugger::log('DEBUG '.$config['version'].': '.$update);

    mysqli_query($database, $update);

    $_SESSION['message'] = 'Osoba upravena.';
    header('Location: readperson.php?rid='.$_POST['personid'].'&amp;hidenotes=0');
} else {
    if (isset($_POST['editperson'])) {
        $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
//ANTIDATING registration
if ((isset($_POST['personid'])) && $user['aclGamemaster'] == 1 && is_numeric($_POST['rdatumday']) && is_numeric($_POST['regusr'])) {
    authorizedAccess(1, 10, $_POST['personid']);
    $rdatum = mktime(0, 0, 0, $_POST['rdatummonth'], $_POST['rdatumday'], $_POST['rdatumyear']);
    mysqli_query($database, "UPDATE ".DB_PREFIX."person SET regdate='".$rdatum."', regid='".$_POST['regusr']."' WHERE id=".$_POST['personid']);
    $_SESSION['message'] = 'Osoba upravena.';
    header('Location: readperson.php?rid='.$_POST['personid'].'&amp;hidenotes=0');
} else {
    if (isset($_POST['orgperson'])) {
        $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
if (isset($_POST['setgroups'])) {
    authorizedAccess(1, 6, $_POST['personid']);
    mysqli_query($database, "DELETE FROM ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".$_POST['personid']);
    $group = $_POST['group'];
    $_SESSION['message'] = 'Skupiny pro uživatele uloženy.';
    for ($i = 0; $i < count($group); $i++) {
        mysqli_query($database, "INSERT INTO ".DB_PREFIX."g2p VALUES('".$_POST['personid']."','".$group[$i]."','".$user['userId']."')");
    }
    header('Location: readperson.php?rid='.$_POST['personid'].'&amp;hidenotes=0');
}
if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['personid']) && is_numeric($_POST['secret'])) {
    authorizedAccess(1, 4, $_POST['personid']);
    $newname = time().md5(uniqid(time().random_int(0, getrandmax())));
    move_uploaded_file($_FILES['attachment']['tmp_name'], './files/'.$newname);
    $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".time()."','".$user['userId']."','1','".$_POST['personid']."','".$_POST['secret']."')";
    mysqli_query($database, $sql);
    if (!isset($_POST['fnotnew'])) {
        unreadRecords(1, $_POST['personid']);
    }
} else {
    if (isset($_POST['uploadfile'])) {
        $_SESSION['message'] = 'Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.';
    }
}
if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
    authorizedAccess(1, 5, $_POST['personid']);
    if ($user['aclPerson']) {
        $fres = mysqli_query($database, "SELECT uniquename FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
        $frec = mysqli_fetch_assoc($fres);
        unlink('./files/'.$frec['uniquename']);
        mysqli_query($database, "DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
    }
    header('Location: editperson.php?rid='.$_GET['personid']);
}
if (isset($_GET['deletesymbol'])) {
    authorizedAccess(1, 2, $_GET['personid']);
    if ($user['aclPerson']) {
        $sps = mysqli_query($database, "SELECT symbol FROM ".DB_PREFIX."person WHERE id=".$_GET['personid']);
        $spc = mysqli_fetch_assoc($sps);
        $prsn_res = mysqli_query($database, "SELECT name, surname FROM ".DB_PREFIX."person WHERE id=".$_GET['personid']);
        $prsn_rec = mysqli_fetch_assoc($prsn_res);
        $sdate = "<p>".date("j/m/Y H:i:s", time())." Odpojeno od ".$prsn_rec['name']." ".$prsn_rec['surname']."</p>";
        mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET `desc` = concat('".$sdate."', `desc`), assigned=0 WHERE id=".$spc['symbol']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."person SET symbol='' WHERE id=".$_GET['personid']);
    }
    header('Location: editperson.php?rid='.$_GET['personid']);
}

//FILTER
if (isset($_GET['sort'])) {
    sortingSet('person', $_GET['sort'], 'person');
}
if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    filterSet('person', @$_POST['filter']);
}
$filter = filterGet('person');
$sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];
if (!isset($filter['archived'])) {
    $sqlFilter .= ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  ';
}
if (!isset($filter['dead'])) {
    $sqlFilter .= ' AND '.DB_PREFIX.'person.dead = 0 ';
}
if (!isset($filter['secret'])) {
    $sqlFilter .= ' AND '.DB_PREFIX.'person.secret = 0 ';
}
if (isset($filter['new'])) {
    $sqlFilter .= ' AND '.DB_PREFIX.'unread.id is not null ';
}
if (@$filter['classSelect']) {
    $sqlFilter .= ' AND '.DB_PREFIX.'person.spec = '.($filter['classSelect'] - 1);
}
if (@$filter['categorySelect']) {
    $sqlFilter .= ' AND '.DB_PREFIX.'person.power = '.($filter['categorySelect'] - 1);
}
if (@$filter['sideSelect']) {
    $sqlFilter .= ' AND '.DB_PREFIX.'person.side = '.($filter['sideSelect'] - 1);
}
$filter['side'] = filterSide();
$filter['category'] = filterCategory();
$filter['class'] = filterClass();
$latteParameters['filter'] = $filter;


$sql = "SELECT ".DB_PREFIX."person.deleted,
    ".DB_PREFIX."person.spec,
    ".DB_PREFIX."person.power,
    ".DB_PREFIX."person.side,
    ".DB_PREFIX."unread.id as unread,
    ".DB_PREFIX."person.regdate as date_created,
    ".DB_PREFIX."person.datum as date_changed,
    ".DB_PREFIX."person.phone,
    ".DB_PREFIX."person.archived,
    ".DB_PREFIX."person.dead ,
    ".DB_PREFIX."person.secret ,
    ".DB_PREFIX."person.name ,
    CASE WHEN ( LENGTH(".DB_PREFIX."person.surname) < 2 ) THEN ' ' ELSE ".DB_PREFIX."person.surname END AS surname,
    ".DB_PREFIX."person.id AS 'id',
    ".DB_PREFIX."person.symbol
FROM ".DB_PREFIX."person
LEFT JOIN  ".DB_PREFIX."unread on  ".DB_PREFIX."person.id =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 1 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
WHERE ".$sqlFilter."
GROUP BY ".DB_PREFIX."person.id ".sortingGet('person');
//    ".DB_PREFIX."person.surname ,

$personList = mysqli_query($database, $sql);
$personCount = mysqli_num_rows($personList);

if ($personCount > 0) {
    $latteParameters['person_record'] = $personList;
    $latteParameters['person_count'] = $personCount;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('persons_body');
