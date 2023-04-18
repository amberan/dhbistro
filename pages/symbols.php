<?php

$latteParameters['title'] = $text['menuSymbols'];

// Přidání symbolu
if (isset($_POST['insertsymbol'])) {
    if (is_uploaded_file($_FILES['symbol']['tmp_name'])) {
        $sfile = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['symbol']['tmp_name'], './files/'.$sfile.'tmp');
        $sdst = imageResize('./files/'.$sfile.'tmp', 100, 100);
        imagejpeg($sdst, './files/symbols/'.$sfile);
        unlink('./files/'.$sfile.'tmp');
    } else {
        $sfile = '';
    }
    $time = time();
    $sql_p = "INSERT INTO ".DB_PREFIX."symbol (symbol, `desc`, deleted, created, created_by, modified, modified_by, archived, assigned, search_lines, search_curves, search_points, search_geometricals, search_alphabets, search_specialchars, secret)
    VALUES( '".$sfile."', '".$_POST['contents']."', '0', '".$time."', '".$user['userId']."', '".$time."', '".$user['userId']."', 0, '0', '".$_POST['liner']."', '".$_POST['curver']."', '".$_POST['pointer']."', '".$_POST['geometrical']."', '".$_POST['alphabeter']."', '".$_POST['specialchar']."', 0)";
    mysqli_query($database, $sql_p);
    $sql_f = "SELECT id FROM ".DB_PREFIX."symbol WHERE created='".$time."' AND created_by='".$user['userId']."' AND modified='".$time."' AND modified_by='".$user['userId']."'";
    $pidarray = mysqli_fetch_assoc(mysqli_query($database, $sql_f));
    $pid = $pidarray['id'];
    authorizedAccess('symbol', 'new', $pid);
    if (!isset($_POST['notnew'])) {
        unreadRecords(7, $pid);
    }
    $_SESSION['message'] = 'Symbol vložen.';
} elseif (isset($_POST['insertperson'])) {
    $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
}
// Vymazani symbolu
if (isset($_REQUEST['sdelete']) && is_numeric($_REQUEST['sdelete']) && $user['aclSymbol'] > 1) {
    authorizedAccess('symbol', 'delete', $_REQUEST['sdelete']);
    $sqlDelete = "UPDATE ".DB_PREFIX."symbol SET deleted=1 WHERE id=".$_REQUEST['sdelete'];
    mysqli_query($database, $sqlDelete);
    deleteAllUnread('symbol', $_REQUEST['sdelete']);
    $_SESSION['message'] = 'Symbol smazan.';
}
// Obnoveni symbolu
if (isset($_REQUEST['undelete']) && is_numeric($_REQUEST['undelete']) && $user['aclRoot']) {
    authorizedAccess('symbol', 'restore', $_REQUEST['undelete']);
    mysqli_query($database, "UPDATE ".DB_PREFIX."symbol SET deleted=0 WHERE id=".$_REQUEST['undelete']);
    $_SESSION['message'] = 'Symbol obnoven.';
}

// archivace symbolu
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'archive' && $user['aclSymbol'] > 1) {
    authorizedAccess('symbol', 'edit', $URL['2']);
    mysqli_query($database, 'UPDATE '.DB_PREFIX.'symbol SET archived=CURRENT_TIMESTAMP WHERE id='.$URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationArchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotArchived'];
    }
}
// odarchivace symbolu
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'unarchive' && $user['aclSymbol'] > 1) {
    authorizedAccess('symbol', 'edit', $URL[2]);
    mysqli_query($database, 'UPDATE '.DB_PREFIX.'symbol SET archived=0 WHERE id='.$URL[2]);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationUnarchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotUnarchived'];
    }
}



if (isset($_GET['sort'])) {
    sortingSet('symbol', $_GET['sort'], 'symbol');
}
if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    filterSet('symbol', @$_POST['filter']);
}
$filter = filterGet('symbol');

$sqlFilter = "symbol.secret<=" . $user['aclSecret'];
if ($user['aclRoot'] < 1 || ($user['aclRoot'] && !isset($filter['deleted']))) {
    $sqlFilter .= ' AND symbol.deleted = 0 ';
}

switch (@$filter['archived']) {
    case 'on':;
        break;
    default: $sqlFilter .= ' AND (symbol.archived is null OR symbol.archived  < from_unixtime(1))  ';
}
switch (@$filter['secret']) {
    case 'on': $sqlFilter .= ' AND symbol.secret<='.$user['aclSecret'];
        break;
    default: $sqlFilter .= ' AND symbol.secret=0 ';
}
$latteParameters['filter'] = $filter;


$symbolSql = "SELECT    symbol.id as symbolId,
                        symbol.desc as symbolDescription,
                        symbol.deleted as symbolDeleted,
                        symbol.archived as symbolArchived,
                        symbol.created as symbolCreated,
                        symbol.created_by as symbolCreatedBy,
                        symbol.modified as symbolModified,
                        symbol.modified_by as symbolModifiedBy,
                        symbol.assigned as symbolAssignedTo,
                        symbol.secret as symbolSecret,
                        CASE WHEN ( symbol.archived < from_unixtime(1) OR symbol.archived IS NULL) THEN 'False' ELSE 'True' END AS symbolArchivedBool,
                        concat(createdPerson.name,' ',createdPerson.surname) as symbolCreatedByName,
                        createdUser.userName as symbolCreatedByUserName,
                        concat(modifiedPerson.name,' ',modifiedPerson.surname) as symbolModifiedByName,
                        modifiedUser.userName as symbolModifiedByUserName
                FROM ".DB_PREFIX."symbol as symbol
                LEFT JOIN ".DB_PREFIX."user as createdUser on symbol.created_by = createdUser.userId
                LEFT JOIN ".DB_PREFIX."person as createdPerson on createdUser.personId = createdPerson.id AND createdPerson.deleted = 0 AND createdPerson.secret <= ".$user['aclSecret']."
                LEFT JOIN ".DB_PREFIX."user as modifiedUser on symbol.modified_by = modifiedUser.userId
                LEFT JOIN ".DB_PREFIX."person as modifiedPerson on modifiedUser.personId = modifiedPerson.id and modifiedPerson.deleted = 0 AND modifiedPerson.secret <= ".$user['aclSecret']."
                WHERE ".$sqlFilter." AND symbol.secret<=".$user['aclSecret']." AND symbol.assigned=0".
               sortingGet('symbol');
$symbolList = mysqli_query($database, $symbolSql);
$symbolCount = mysqli_num_rows($symbolList);



if ($symbolCount > 0) {
    while ($symbol = mysqli_fetch_assoc($symbolList)) {
        if ($notes = symbolNotes($symbol['symbolId'])) {
            $symbol['notes'] = $notes;
            unset($notes);
        }
        if ($cases = symbolCases($symbol['symbolId'])) {
            $symbol['cases'] = $cases;
            unset($cases);
        }
        if ($reports = symbolReports($symbol['symbolId'])) {
            $symbol['reports'] = $reports;
            unset($reports);
        }
        $symbols[] = $symbol;
    }
    $latteParameters['symbolsRecord'] = $symbols;
    $latteParameters['symbolCount'] = $symbolCount;
    $latteParameters['noteType'] = noteType();
} else {
    $latteParameters['warning'] = $text['notificationListEmpty'];
}




latteDrawTemplate('sparklet');
latteDrawTemplate('symbols_body');
