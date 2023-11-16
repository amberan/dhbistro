<?php

// archivace
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'archive' && $user['aclCase'] > 1) {
    authorizedAccess('case', 'edit', $URL['2']);
    mysqli_query($database, 'UPDATE ' . DB_PREFIX . 'case SET caseArchived=CURRENT_TIMESTAMP WHERE id=' . $URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationArchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotArchived'];
    }
}
// odarchivace
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'unarchive' && $user['aclCase'] > 1) {
    authorizedAccess('case', 'edit', $URL[2]);
    mysqli_query($database, 'UPDATE ' . DB_PREFIX . 'case SET caseArchived=null WHERE id=' . $URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationUnarchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotUnarchived'];
    }
}

if (isset($URL[2], $URL[3]) && $URL[2] == 'delete' && is_numeric($URL[3]) && $user['aclCase'] > 0) { //delete case
    authorizedAccess('cases', 'delete', $URL[3]);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "case SET deleted=1 WHERE id=" . $URL['3']);
    deleteAllUnread('case', $URL[3]);
    $_SESSION['message'] = 'Případ smazán.';
}
if (isset($URL[2], $URL[3]) && $URL[2] == 'restore' && is_numeric($URL[3]) && $user['aclRoot'] > 0) { //delete case
    authorizedAccess('cases', 'delete', $URL[3]);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "case SET deleted=0 WHERE id=" . $URL[3]);
    deleteAllUnread('case', $URL[3]);
    $_SESSION['message'] = 'Případ obnoven.';
}
if (isset($_POST['insertcase']) && !preg_match('/^[[:blank:]]*$/i', $_POST['title']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
    $ures = mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "case WHERE UCASE(title)=UCASE('" . $_POST['title'] . "')");
    if (mysqli_num_rows($ures)) {
        $_SESSION['message'] = 'Případ již existuje, změňte jeho jméno.';
    } else {
        mysqli_query($database, "INSERT INTO " . DB_PREFIX . "case  (caseCreated, title, datum, iduser, contents, secret, deleted, status) VALUES(CURRENT_TIMESTAMP, '" . $_POST['title'] . "','" . time() . "','" . $user['userId'] . "','" . $_POST['contents'] . "','" . $_POST['secret'] . "','0','" . $_POST['status'] . "')");
        $cidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "case WHERE UCASE(title)=UCASE('" . $_POST['title'] . "')"));
        $cid = $cidarray['id'];
        authorizedAccess('cases', 'new', $cid);
        if (!isset($_POST['notnew'])) {
            unreadRecords('case', $cid);
        }
        $_SESSION['message'] = 'Případ vytvořen.';
    }
} else {
    if (isset($_POST['insertcase'])) {
        $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
if (isset($_POST['caseid'], $_POST['editcase']) && $user['aclCase'] && !preg_match('/^[[:blank:]]*$/i', $_POST['title']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
    authorizedAccess('cases', 'edit', $_POST['caseid']);
    if (!isset($_POST['notnew'])) {
        unreadRecords(3, $_POST['caseid']);
    }
    $ures = mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "case WHERE UCASE(title)=UCASE('" . $_POST['title'] . "') AND id<>" . $_POST['caseid']);
    if (mysqli_num_rows($ures)) {
        $_SESSION['message'] = 'Případ již existuje, změňte jeho jméno.';
    } else {
        if ($user['aclGamemaster'] == 1) {
            $sqlCaseUpdate = "UPDATE " . DB_PREFIX . "case SET title='" . $_POST['title'] . "', contents='" . $_POST['contents'] . "', secret='" . $_POST['secret'] . "', status='" . $_POST['status'] . "' WHERE id=" . $_POST['caseid'];
            mysqli_query($database, $sqlCaseUpdate);
        } else {
            $sqlCaseUpdate = "UPDATE " . DB_PREFIX . "case SET title='" . $_POST['title'] . "', datum='" . time() . "', iduser='" . $user['userId'] . "', contents='" . $_POST['contents'] . "', secret='" . $_POST['secret'] . "', status='" . $_POST['status'] . "' WHERE id=" . $_POST['caseid'];
            mysqli_query($database, $sqlCaseUpdate);
        }
        $_SESSION['message'] = 'Případ upraven.';
    }
} else {
    if (isset($_POST['editcase'])) {
        $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
    }
}
if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['caseid']) && is_numeric($_POST['secret'])) {
    authorizedAccess('cases', 'fileAdd', $_POST['caseid']);
    $newname = time() . md5(uniqid(time() . random_int(0, getrandmax())));
    move_uploaded_file($_FILES['attachment']['tmp_name'], './files/' . $newname);
    $sql = "INSERT INTO " . DB_PREFIX . "file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('" . $newname . "','" . $_FILES['attachment']['name'] . "','" . $_FILES['attachment']['type'] . "','" . $_FILES['attachment']['size'] . "','" . time() . "','" . $user['userId'] . "','3','" . $_POST['caseid'] . "','" . $_POST['secret'] . "')";
    mysqli_query($database, $sql);
    if (!isset($_POST['fnotnew'])) {
        unreadRecords(3, $_POST['caseid']);
    }
} else {
    if (isset($_POST['uploadfile'])) {
        $_SESSION['message'] = 'Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.';
    }
}
if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
    authorizedAccess('cases', 'fileDelete', $_GET['caseid']);
    if ($user['aclCase']) {
        $fres = mysqli_query($database, "SELECT uniquename FROM " . DB_PREFIX . "file WHERE " . DB_PREFIX . "file.id=" . $_GET['deletefile']);
        $frec = mysqli_fetch_assoc($fres);
        unlink('./files/' . $frec['uniquename']);
        mysqli_query($database, "DELETE FROM " . DB_PREFIX . "file WHERE " . DB_PREFIX . "file.id=" . $_GET['deletefile']);
    }
    header('Location: editcase.php?rid=' . $_GET['caseid']);
}

//FILTER
if (isset($_GET['sort'])) {
    sortingSet('case', $_GET['sort'], 'case');
}
if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    filterSet('case', @$_POST['filter']);
}
$filter = filterGet('case');

$sqlFilter = DB_PREFIX . "case.secret<=" . $user['aclSecret'];
if ($user['aclRoot'] < 1 || ($user['aclRoot'] && !isset($filter['deleted']))) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'case.deleted = 0 ';
}
switch (@$filter['stat']) {
    case 'on': $sqlFilter .= ' AND ' . DB_PREFIX . 'case.status in (0,1)';
        break; //solved
    default: $sqlFilter .= ' AND ' . DB_PREFIX . 'case.status=0 ';
}
if (!isset($filter['archived'])) {
    $sqlFilter .= ' AND (' . DB_PREFIX . 'case.caseArchived is null OR ' . DB_PREFIX . 'case.caseArchived  < from_unixtime(1))  ';
}
if (@$filter['secret'] != 'on') {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'case.secret = 0 ';
}
if (@$filter['new']) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'unread.id is not null ';
}
$latteParameters['filter'] = $filter;

//CASE LIST
$sql = "SELECT " . DB_PREFIX . "case.datum as date_changed,
        " . DB_PREFIX . "case.status,
        " . DB_PREFIX . "case.secret,
        " . DB_PREFIX . "case.title,
        " . DB_PREFIX . "case.id,
        " . DB_PREFIX . "case.caseArchived,
        " . DB_PREFIX . "case.deleted, " . DB_PREFIX . "case.caseCreated,  " . DB_PREFIX . "unread.id as unread,
        CASE WHEN ( caseArchived < from_unixtime(1) OR caseArchived IS NULL) THEN 'False' ELSE 'True' END AS caseArchivedBool
FROM " . DB_PREFIX . "case
LEFT JOIN  " . DB_PREFIX . "unread on  " . DB_PREFIX . "case.id =  " . DB_PREFIX . "unread.idrecord AND  " . DB_PREFIX . "unread.idtable = 3 and  " . DB_PREFIX . "unread.iduser=" . $user['userId'] . "
WHERE " . $sqlFilter . " GROUP BY " . DB_PREFIX . "case.id " . sortingGet('case');

$caseList = mysqli_query($database, $sql);
$caseCount = mysqli_num_rows($caseList);
if ($caseCount > 0) {
    $latteParameters['case_record'] = $caseList;
    $latteParameters['caseCount'] = $caseCount;
} else {
    $latteParameters['warning'] = $text['notificationListEmpty'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('cases_body');
