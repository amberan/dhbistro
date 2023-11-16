<?php

// DELETE
if (isset($URL[2], $URL[3]) && $URL[2] == 'delete' && is_numeric($URL[3]) && $user['aclPerson'] > 1) {
    personDelete($URL[3]);
}
// RESTORE
if (isset($URL[2], $URL[3]) && $URL[2] == 'restore' && is_numeric($URL[3]) && $user['aclRoot'] > 0) {
    personRestore($URL[3]);
}
// archivace
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'archive' && $user['aclPerson'] > 1) {
    authorizedAccess('person', 'edit', $URL['2']);
    mysqli_query($database, 'UPDATE ' . DB_PREFIX . 'person SET archived=CURRENT_TIMESTAMP WHERE id=' . $URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationArchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotArchived'];
    }
}
// odarchivace
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'unarchive' && $user['aclPerson'] > 1) {
    authorizedAccess('person', 'edit', $URL[2]);
    mysqli_query($database, 'UPDATE ' . DB_PREFIX . 'person SET archived=null WHERE id=' . $URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationUnarchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotUnarchived'];
    }
}
// NEW
if (isset($_POST['insertperson']) && !preg_match('/^[[:blank:]]*$/i', $_POST['name']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['side']) && is_numeric($_POST['power']) && is_numeric($_POST['spec'])) {
    if (is_uploaded_file($_FILES['portrait']['tmp_name'])) {
        $file = time() . md5(uniqid(time() . random_int(0, getrandmax())));
        move_uploaded_file($_FILES['portrait']['tmp_name'], './files/' . $file . 'tmp');
        $sdst = imageResize('./files/' . $file . 'tmp', 100, 130);
        imagejpeg($sdst, './files/portraits/' . $file);
        unlink('./files/' . $file . 'tmp');
    } else {
        $file = '';
    }
    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
        $sfile = time() . md5(uniqid(time() . random_int(0, getrandmax())));
        move_uploaded_file($_FILES['symbol']['tmp_name'], './files/' . $sfile . 'tmp');
        $sdst = imageResize('./files/' . $sfile . 'tmp', 100, 130);
        imagejpeg($sdst, './files/symbols/' . $sfile);
        unlink('./files/' . $sfile . 'tmp');
        $sql_sy = "INSERT INTO " . DB_PREFIX . "symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret) VALUES( '" . $sfile . "', '', 0, '" . time() . "', '" . $user['userId'] . "', '" . time() . "', '" . $user['userId'] . "', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
        mysqli_query($database, $sql_sy);
        $syidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "symbol WHERE symbol = '" . $sfile . "'"));
        $syid = $syidarray['id'];
    } else {
        $sfile = '';
        $syid = '';
    }
    $updateDate = $rdatum = time();
    if ($user['aclGamemaster'] == 1) { //ANTEDATING
        $rdatum = mktime(0, 0, 0, $_POST['rdatummonth'], $_POST['rdatumday'], $_POST['rdatumyear']);
        $updateDate = rand($rdatum, time());
    }

    $sql_p = "INSERT INTO " . DB_PREFIX . "person ( name, surname, phone, datum, iduser, contents, secret, deleted, portrait, side, power, spec, symbol, regdate, regid)
                VALUES('" . $_POST['name'] . "','" . $_POST['surname'] . "','" . $_POST['phone'] . "','" . $updateDate . "','" . $user['userId'] . "','" . $_POST['contents'] . "',
                '" . $_POST['secret'] . "','0','" . $file . "', '" . $_POST['side'] . "', '" . $_POST['power'] . "', '" . $_POST['spec'] . "', '" . $syid . "','" . $rdatum . "','" . $user['userId'] . "')";
    mysqli_query($database, $sql_p);
    $pid = mysqli_insert_id($database);
    personCheckboxUpdate($pid, 'archived', @$_POST['archiv']);
    personCheckboxUpdate($pid, 'roof', @$_POST['personRoof']);
    personCheckboxUpdate($pid, 'dead', @$_POST['dead']);

    if (!isset($_POST['notnew'])) {
        unreadRecords(1, $pid);
    }
    authorizedAccess('person', 'new', $pid);
    $_SESSION['message'] = 'Osoba vytvořena.';
} else {
    if (isset($_POST['insertperson'])) {
        $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
//EDIT
if (isset($_POST['personid'], $_POST['editperson']) && $user['aclPerson'] && !preg_match('/^[[:blank:]]*$/i', $_POST['name'])) {
    authorizedAccess('person', 'edit', $_POST['personid']);
    if (!isset($_POST['notnew'])) {
        unreadRecords(1, $_POST['personid']);
    }
    if (is_uploaded_file($_FILES['portrait']['tmp_name'])) { //UPLOAD portait
        $pc = mysqli_fetch_assoc(mysqli_query($database, "SELECT portrait FROM ".DB_PREFIX."person WHERE id=".$_POST['personid']));
        if (isset($pc['portrait'])) {
            unlink('./files/portraits/'.$pc['portrait']);
        }
        $file = time() . md5(uniqid(time() . random_int(0, getrandmax())));
        move_uploaded_file($_FILES['portrait']['tmp_name'], './files/' . $file . 'tmp');
        $dst = imageResize('./files/' . $file . 'tmp', 100, 130);
        imagejpeg($dst, './files/portraits/' . $file);
        unlink('./files/' . $file . 'tmp');
        mysqli_query($database, "UPDATE " . DB_PREFIX . "person SET portrait='" . $file . "' WHERE id=" . $_POST['personid']);
    }
    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) { //UPLOAD SYMBOL
        $sps = mysqli_query($database, "SELECT symbol, name, surname FROM " . DB_PREFIX . "person WHERE symbol>0 AND id=" . $_POST['personid']);
        if ($spc = mysqli_fetch_assoc($sps)) {
            $sdate = "<p>" . date("j/m/Y H:i:s", time()) . " Odpojeno od " . $spc['name'] . " " . $spc['surname'] . "</p>";
            $sqlUploadSymbol = "UPDATE " . DB_PREFIX . "symbol SET `desc` = concat('" . $sdate . "', `desc`), assigned=0 WHERE id=" . $spc['symbol'];
            mysqli_query($database, $sqlUploadSymbol);
        }
        $sfile = time() . md5(uniqid(time() . random_int(0, getrandmax())));
        move_uploaded_file($_FILES['symbol']['tmp_name'], './files/' . $sfile . 'tmp');
        $sdst = imageResize('./files/' . $sfile . 'tmp', 100, 100);
        imagejpeg($sdst, './files/symbols/' . $sfile);
        unlink('./files/' . $sfile . 'tmp');
        $sql_sy = "INSERT INTO " . DB_PREFIX . "symbol  ( symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines,
            search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret)
            VALUES( '" . $sfile . "', '', 0, '" . time() . "', '" . $user['userId'] . "', '" . time() . "', '" . $user['userId'] . "', 0, 1, 0, 0, 0, 0, 0, 0, 0)";
        mysqli_query($database, $sql_sy);
        $syidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "symbol WHERE symbol = '" . $sfile . "'"));
        $syid = $syidarray['id'];
        mysqli_query($database, "UPDATE " . DB_PREFIX . "person SET symbol='" . $syid . "' WHERE id=" . $_POST['personid']);
    }
    personCheckboxUpdate($_POST['personid'], 'archived', @$_POST['archiv']);
    personCheckboxUpdate($_POST['personid'], 'roof', @$_POST['personRoof']);
    personCheckboxUpdate($_POST['personid'], 'dead', @$_POST['dead']);
    $sqlPlayer = '';
    if ($user['aclGamemaster'] != 1) {
        $sqlPlayer = "datum='" . time() . "', iduser='" . $user['userId'] . "',";
    }
    $update = "UPDATE " . DB_PREFIX . "person SET name='" . $_POST['name'] . "', surname='" . $_POST['surname'] . "', phone='" . $_POST['phone'] . "', " . $sqlPlayer . "
        contents='" . $_POST['contents'] . "', secret='" . $_POST['secret'] . "', side='" . $_POST['side'] . "', power='" . $_POST['power'] . "', spec='" . $_POST['spec'] . "'
         WHERE id=" . $_POST['personid'];
    mysqli_query($database, $update);
    $_SESSION['message'] = 'Osoba upravena.';
} else {
    if (isset($_POST['editperson'])) {
        $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
//ANTIDATING registration
if (isset($_POST['personid']) && $user['aclGamemaster'] == 1 && is_numeric($_POST['rdatumday']) && is_numeric($_POST['regusr'])) {
    authorizedAccess('person', 'GMedit', $_POST['personid']);
    $rdatum = mktime(0, 0, 0, $_POST['rdatummonth'], $_POST['rdatumday'], $_POST['rdatumyear']);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "person SET regdate='" . $rdatum . "', regid='" . $_POST['regusr'] . "' WHERE id=" . $_POST['personid']);
    $_SESSION['message'] = 'Osoba upravena.';
} else {
    if (isset($_POST['orgperson'])) {
        $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
if (isset($_POST['setgroups'])) {
    authorizedAccess('person', 'link', $_POST['personid']);
    mysqli_query($database, "DELETE FROM " . DB_PREFIX . "g2p WHERE " . DB_PREFIX . "g2p.idperson=" . $_POST['personid']);
    $group = $_POST['group'];
    $_SESSION['message'] = 'Skupiny pro uživatele uloženy.';
    for ($i = 0; $i < count($group); $i++) {
        mysqli_query($database, "INSERT INTO " . DB_PREFIX . "g2p VALUES('" . $_POST['personid'] . "','" . $group[$i] . "','" . $user['userId'] . "')");
    }
}
if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['personid']) && is_numeric($_POST['secret'])) {
    authorizedAccess('person', 'fileAdd', $_POST['personid']);
    $newname = time().md5(uniqid(time().random_int(0, getrandmax())));
    move_uploaded_file($_FILES['attachment']['tmp_name'], './files/'.$newname);
    $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret)
    VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".time()."',
        '".$user['userId']."','1','".$_POST['personid']."','".$_POST['secret']."')";
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
    authorizedAccess('person', 'fileDelete', $_GET['personid']);
    if ($user['aclPerson']) {
        $fres = mysqli_query($database, "SELECT uniquename FROM " . DB_PREFIX . "file WHERE " . DB_PREFIX . "file.id=" . $_GET['deletefile']);
        $frec = mysqli_fetch_assoc($fres);
        unlink('./files/' . $frec['uniquename']);
        mysqli_query($database, "DELETE FROM " . DB_PREFIX . "file WHERE " . DB_PREFIX . "file.id=" . $_GET['deletefile']);
    }
}
if (isset($_GET['deletesymbol'])) {
    authorizedAccess('person', 'edit', $_GET['personid']);
    if ($user['aclPerson']) {
        $sps = mysqli_query($database, "SELECT symbol FROM " . DB_PREFIX . "person WHERE id=" . $_GET['personid']);
        $spc = mysqli_fetch_assoc($sps);
        $prsn_res = mysqli_query($database, "SELECT name, surname FROM " . DB_PREFIX . "person WHERE id=" . $_GET['personid']);
        $prsn_rec = mysqli_fetch_assoc($prsn_res);
        $sdate = "<p>" . date("j/m/Y H:i:s", time()) . " Odpojeno od " . $prsn_rec['name'] . " " . $prsn_rec['surname'] . "</p>";
        mysqli_query($database, "UPDATE " . DB_PREFIX . "symbol SET `desc` = concat('" . $sdate . "', `desc`), assigned=0 WHERE id=" . $spc['symbol']);
        mysqli_query($database, "UPDATE " . DB_PREFIX . "person SET symbol='' WHERE id=" . $_GET['personid']);
    }
}

//FILTER
if (isset($_GET['sort'])) {
    sortingSet('person', $_GET['sort'], 'person');
}
if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    filterSet('person', @$_POST['filter']);
}
$filter = filterGet('person');

$sqlFilter = DB_PREFIX . "person.secret<=" . $user['aclSecret'];
if ($user['aclRoot'] < 1 || ($user['aclRoot'] && !isset($filter['deleted']))) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'person.deleted = 0 ';
}
if (!isset($filter['archived'])) {
    $sqlFilter .= ' AND (' . DB_PREFIX . 'person.archived is null OR ' . DB_PREFIX . 'person.archived  < from_unixtime(1))  ';
}
if (!isset($filter['dead'])) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'person.dead = 0 ';
}
if (!isset($filter['secret']) || $user['aclSecret'] < 1) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'person.secret = 0 ';
}
if (isset($filter['new'])) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'unread.id is not null ';
}
if (@$filter['classSelect']) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'person.spec = ' . ($filter['classSelect'] - 1);
}
if (@$filter['categorySelect']) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'person.power = ' . ($filter['categorySelect'] - 1);
}
if (@$filter['sideSelect']) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'person.side = ' . ($filter['sideSelect'] - 1);
}
$filter['side'] = filterSide();
$filter['category'] = filterCategory();
$filter['class'] = filterClass();
$latteParameters['filter'] = $filter;

//TODO UNESCAPE
$sql = "SELECT " . DB_PREFIX . "person.deleted,
    " . DB_PREFIX . "person.spec,
    " . DB_PREFIX . "person.power,
    " . DB_PREFIX . "person.side,
    " . DB_PREFIX . "unread.id as unread,
    " . DB_PREFIX . "person.regdate as date_created,
    " . DB_PREFIX . "person.datum as date_changed,
    " . DB_PREFIX . "person.phone,
    " . DB_PREFIX . "person.archived,
    " . DB_PREFIX . "person.dead ,
    " . DB_PREFIX . "person.secret ,
    " . DB_PREFIX . "person.name ,
    CASE WHEN ( LENGTH(" . DB_PREFIX . "person.surname) < 1 ) THEN " . DB_PREFIX . "person.name ELSE concat(" . DB_PREFIX . "person.surname,', '," . DB_PREFIX . "person.name) END as personFullname,
    CASE WHEN ( LENGTH(" . DB_PREFIX . "person.surname) < 1 ) THEN ' ' ELSE " . DB_PREFIX . "person.surname END AS surname,
    CASE WHEN ( archived < from_unixtime(1) OR archived IS NULL) THEN 'False' ELSE 'True' END AS personArchivedBool,
    " . DB_PREFIX . "person.id AS 'id',
    " . DB_PREFIX . "person.symbol
FROM " . DB_PREFIX . "person
LEFT JOIN  " . DB_PREFIX . "unread on  " . DB_PREFIX . "person.id =  " . DB_PREFIX . "unread.idrecord AND  " . DB_PREFIX . "unread.idtable = 1 and  " . DB_PREFIX . "unread.iduser=" . $user['userId'] . "
WHERE " . $sqlFilter . "
GROUP BY " . DB_PREFIX . "person.id " . sortingGet('person');

$personList = mysqli_query($database, $sql);
$personCount = mysqli_num_rows($personList);

if ($personCount > 0) {
    $latteParameters['person_record'] = $personList;
    $latteParameters['personCount'] = $personCount;
} else {
    $latteParameters['warning'] = $text['notificationListEmpty'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('persons_body');
