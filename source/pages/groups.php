<?php

if (isset($URL[3]) && is_numeric($URL[3]) && $URL[2] == 'delete' && $user['aclGroup'] > 0) {
    authorizedAccess('group', 'delete', $URL[3]);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "group SET deleted=1 WHERE id=" . $URL[3]);
    deleteAllUnread('group', $URL[3]);
}
if (isset($URL[3]) && is_numeric($URL[3]) && $URL[2] == 'restore' && $user['aclGroup'] > 0) {
    authorizedAccess('group', 'restore', $URL[3]);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "group SET deleted=0 WHERE id=" . $URL[3]);
}
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'archive' && $user['aclGroup'] > 0) {
    authorizedAccess('group', 'edit', $URL[2]);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "group SET archived=1 WHERE id=" . $URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationArchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotArchived'];
    }
}
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'unarchive' && $user['aclGroup'] > 0) {
    authorizedAccess('group', 'edit', $URL[2]);
    mysqli_query($database, "UPDATE " . DB_PREFIX . "group SET archived=0 WHERE id=" . $URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationUnarchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotUnarchived'];
    }
}

if (isset($_POST['insertgroup']) && !preg_match('/^[[:blank:]]*$/i', $_POST['title']) && !preg_match('/^[[:blank:]]*$/i', $_POST['contents']) && is_numeric($_POST['secret'])) {
    $ures = mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "group WHERE UCASE(title)=UCASE('" . $_POST['title'] . "')");
    if (mysqli_num_rows($ures)) {
        echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
    } else {
        mysqli_query($database, "INSERT INTO " . DB_PREFIX . "group ( title, contents, datum, iduser, deleted, secret, archived, groupCreated) VALUES('" . $_POST['title'] . "','" . $_POST['contents'] . "','" . time() . "','" . $user['userId'] . "','0','" . $_POST['secret'] . "',0,CURRENT_TIMESTAMP)");
        $gidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "group WHERE UCASE(title)=UCASE('" . $_POST['title'] . "')"));
        $gid = $gidarray['id'];
        authorizedAccess('group', 'new', $gid);
        if (!isset($_POST['notnew'])) {
            unreadRecords(2, $gid);
        }
        echo '<div id="obsah"><p>Skupina vytvořena.</p></div>';
    }
} else {
    if (isset($_POST['insertgroup'])) {
        echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
    }
}
if (isset($_POST['groupid'], $_POST['editgroup']) && $user['aclGroup'] && !preg_match('/^[[:blank:]]*$/i', $_POST['title']) && !preg_match('/i^[[:blank:]]*$/i', $_POST['contents'])) {
    authorizedAccess('group', 'edit', $_POST['groupid']);
    $ures = mysqli_query($database, "SELECT id FROM " . DB_PREFIX . "group WHERE UCASE(title)=UCASE('" . $_POST['title'] . "') AND id<>" . $_POST['groupid']);
    if (mysqli_num_rows($ures)) {
        echo '<div id="obsah"><p>Skupina již existuje, změňte její jméno.</p></div>';
    } else {
        $sqlGroupUpdate = "UPDATE " . DB_PREFIX . "group SET datum='" . time() . "', title='" . $_POST['title'] . "', contents='" . $_POST['contents'] . "', archived='" . (isset($_POST['archived']) ? '1' : '0') . "', secret='" . (isset($_POST['secret']) ? '1' : '0') . "' WHERE id=" . $_POST['groupid'];
        mysqli_query($database, $sqlGroupUpdate);
        if (!isset($_POST['notnew'])) {
            unreadRecords(2, $_POST['groupid']);
        }
        $_SESSION['message'] = 'Skupina upravena.';
    }
} else {
    if (isset($_POST['editgroup'])) {
        $latteParameters['title'] = 'Uložení změn';
        echo '<div id="obsah"><p>Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
    }
}
if (isset($_POST['setperson'])) {
    header('Location: editgroup.php?rid=' . $_POST['groupid']);
}
if (isset($_GET['delperson']) && is_numeric($_GET['delperson'])) {
    header('Location: editgroup.php?rid=' . $_GET['groupid']);
}
if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['groupid']) && is_numeric($_POST['secret'])) {
    authorizedAccess('group', 'fileAdd', $_POST['groupid']);
    $newname = time() . md5(uniqid(time() . random_int(0, getrandmax())));
    move_uploaded_file($_FILES['attachment']['tmp_name'], './files/' . $newname);
    $sql = "INSERT INTO " . DB_PREFIX . "file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('" . $newname . "','" . $_FILES['attachment']['name'] . "','" . $_FILES['attachment']['type'] . "','" . $_FILES['attachment']['size'] . "','" . time() . "','" . $user['userId'] . "','2','" . $_POST['groupid'] . "','" . $_POST['secret'] . "')";
    mysqli_query($database, $sql);
    if (!isset($_POST['fnotnew'])) {
        unreadRecords(2, $_POST['groupid']);
    }
    header('Location: ' . $_POST['backurl']);
} else {
    if (isset($_POST['uploadfile'])) {
        echo '<div id="obsah"><p>Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.</p></div>';
        header('Location: ' . $_POST['backurl']);
    }
}
if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
    authorizedAccess('group', 'fileDelete', $_GET['groupid']);
    if ($user['aclGroup']) {
        $fres = mysqli_query($database, "SELECT uniquename FROM " . DB_PREFIX . "file WHERE " . DB_PREFIX . "file.id=" . $_GET['deletefile']);
        $frec = mysqli_fetch_assoc($fres);
        unlink('./files/' . $frec['uniquename']);
        mysqli_query($database, "DELETE FROM " . DB_PREFIX . "file WHERE " . DB_PREFIX . "file.id=" . $_GET['deletefile']);
    }
    header('Location: editgroup.php?rid=' . $_GET['groupid']);
}

//FILTER
if (isset($_GET['sort'])) {
    sortingSet('group', $_GET['sort'], 'group');
}
if (isset($_POST['filter'])) {
    filterSet('group', @$_POST['filter']);
}
$filter = filterGet('group');

$sqlFilter = DB_PREFIX . "group.secret<=" . $user['aclSecret'];
if ($user['aclRoot'] < 1 || ($user['aclRoot'] && !isset($filter['deleted']))) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'group.deleted = 0 ';
}
switch (@$filter['archived']) {
    case 'on': $sqlFilter .= ' AND ' . DB_PREFIX . 'group.archived in (0,1)';
        break;
    default: $sqlFilter .= ' AND ' . DB_PREFIX . 'group.archived=0 ';
}
if (@$filter['new']) {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'unread.id is not null ';
}
if (@$filter['secret'] != 'on') {
    $sqlFilter .= ' AND ' . DB_PREFIX . 'group.secret = 0 ';
}
$latteParameters['filter'] = $filter;

//GROUP LIST
$sql = "SELECT " . DB_PREFIX . "group.secret, " . DB_PREFIX . "group.title , " . DB_PREFIX . "group.id , " . DB_PREFIX . "group.archived ,
    " . DB_PREFIX . "group.datum as date_changed, " . DB_PREFIX . "group.groupCreated, " . DB_PREFIX . "group.deleted,  " . DB_PREFIX . "unread.id as unread,
    CASE WHEN ( archived = 0 OR archived IS NULL) THEN 'False' ELSE 'True' END AS groupArchivedBool
    FROM " . DB_PREFIX . "group
    LEFT JOIN  " . DB_PREFIX . "unread on  " . DB_PREFIX . "group.id =  " . DB_PREFIX . "unread.idrecord AND  " . DB_PREFIX . "unread.idtable = 2 and  " . DB_PREFIX . "unread.iduser=" . $user['userId'] . "
    WHERE " . $sqlFilter . " GROUP BY " . DB_PREFIX . "group.id " . sortingGet('group');
$groupList = mysqli_query($database, $sql);
$groupCount = mysqli_num_rows($groupList);
if ($groupCount > 0) {
    $latteParameters['groupCount'] = $groupCount;
    $latteParameters['group_record'] = $groupList;
} else {
    $latteParameters['warning'] = $text['notificationListEmpty'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('groups_body');
