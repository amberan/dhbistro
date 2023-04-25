<?php


// archive
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'archive' && $user['aclReport'] > 1) {
    authorizedAccess('report', 'edit', $URL['2']);
    $sqlArchive = 'UPDATE ' . DB_PREFIX . 'report SET reportArchived=CURRENT_TIMESTAMP WHERE reportId=' . $URL[2];
    mysqli_query($database, $sqlArchive);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationArchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotArchived'];
    }
}
// unarchive
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'unarchive' && $user['aclReport'] > 1) {
    authorizedAccess('report', 'edit', $URL[2]);
    $sqlUnarchive = 'UPDATE ' . DB_PREFIX . 'report SET reportArchived=null WHERE reportId=' . $URL[2];
    mysqli_query($database, $sqlUnarchive);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationUnarchived'];
    } else {
        $_SESSION['message'] = $text['notificationNotUnarchived'];
    }
}
// delete
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'delete' && $user['aclReport'] > 1) {
    authorizedAccess('report', 'delete', $URL[2]);
    $sqlDelete = 'UPDATE '.DB_PREFIX.'report SET reportDeleted=now(), reportModifiedBy='.$user['userId'].' WHERE reportId='.$URL[2];
    mysqli_query($database, $sqlDelete);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationDeleted'];
        deleteAllUnread('person', $URL[2]);
    } else {
        $_SESSION['message'] = $text['notificationNotDeleted'];
    }
}
//restore
if (isset($URL[3]) && is_numeric($URL[2]) && $URL[3] == 'restore' && $user['aclRoot'] >= 1) {
    authorizedAccess('report', 'restore', $URL[2]);
    $sqlRestore = 'UPDATE '.DB_PREFIX.'report SET reportDeleted=0, reportModifiedBy='.$user['userId'].' WHERE reportId='.$URL[2];
    mysqli_query($database, $sqlRestore);
    if (mysqli_affected_rows($database) > 0) {
        $_SESSION['message'] = $text['notificationRestored'];
    } else {
        $_SESSION['message'] = $text['notificationNotRestored'];
    }
}



//FILTER
if (isset($_GET['sort'])) {
    sortingSet('report', $_GET['sort'], 'report');
}

if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    filterSet('report', @$_POST['filter']);
}
$filter = filterGet('report');
$sqlFilter = DB_PREFIX.'report.reportSecret<='.$user['aclSecret'];

if ($user['aclRoot'] < 1 || ($user['aclRoot'] && !isset($filter['deleted']))) {
    $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
}
if (!isset($filter['archived'])) {
    $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportArchived is null OR '.DB_PREFIX.'report.reportArchived  < from_unixtime(1))  ';
}
if (!isset($filter['secret'])) {
    $sqlFilter .= ' AND '.DB_PREFIX.'report.reportSecret = 0 ';
}
if (isset($filter['mine'])) {
    $sqlFilter .= ' AND '.DB_PREFIX.'report.reportOwner = '.$user['userId'];
}
if (isset($filter['new'])) {
    $sqlFilter .= ' AND '.DB_PREFIX.'unread.id is not null ';
}
if (@$filter['reportType']) {
    $sqlFilter .= ' AND '.DB_PREFIX.'report.reportType = '.($filter['reportType']);
}
if (isset($filter['reportStatus']) && @$filter['reportStatus'] != 'all') {
    $sqlFilter .= ' AND '.DB_PREFIX.'report.reportStatus = 0'.($filter['reportStatus']);
}
$latteParameters['filter'] = $filter;



$reportsSql = "SELECT
    concat(ownerPerson.name,' ',ownerPerson.surname) as reportOwnerName,
    ownerUser.userName as reportOwnerUserName,
    concat(createdPerson.name,' ',createdPerson.surname) as reportCreatedByName,
    createdUser.userName as reportCreatedByUserName,
    concat(modifiedPerson.name,' ',modifiedPerson.surname) as reportModifiedByName,
    modifiedUser.userName as reportModifiedByUserName,
    ".DB_PREFIX."report.*,
    ".DB_PREFIX."unread.id AS 'unread',
    CASE WHEN ( reportDeleted < from_unixtime(1) OR reportDeleted IS NULL) THEN 'False' ELSE 'True' END AS reportDeletedBool,
    CASE WHEN ( reportArchived < from_unixtime(1) OR reportArchived IS NULL) THEN 'False' ELSE 'True' END AS reportArchivedBool
    FROM ".DB_PREFIX."report
    LEFT JOIN ".DB_PREFIX."unread on  ".DB_PREFIX."report.reportId =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 4 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    LEFT JOIN ".DB_PREFIX."user as ownerUser on ".DB_PREFIX."report.reportOwner = ownerUser.userId
    LEFT JOIN ".DB_PREFIX."person as ownerPerson on ownerUser.personId = ownerPerson.id AND ownerPerson.deleted = 0 AND ownerPerson.secret <= ".$user['aclSecret']."
    LEFT JOIN ".DB_PREFIX."user as createdUser on ".DB_PREFIX."report.reportCreatedBy = createdUser.userId
    LEFT JOIN ".DB_PREFIX."person as createdPerson on createdUser.personId = createdPerson.id AND createdPerson.deleted = 0 AND createdPerson.secret <= ".$user['aclSecret']."
    LEFT JOIN ".DB_PREFIX."user as modifiedUser on ".DB_PREFIX."report.reportModifiedBy = modifiedUser.userId
    LEFT JOIN ".DB_PREFIX."person as modifiedPerson on modifiedUser.personId = modifiedPerson.id and modifiedPerson.deleted = 0 AND modifiedPerson.secret <= ".$user['aclSecret']."
    WHERE $sqlFilter ".
    sortingGet('report'); //."GROUP BY ".DB_PREFIX."report.id";
$reportList = mysqli_query($database, $reportsSql);
$reportCount = mysqli_num_rows($reportList);


if ($reportCount > 0) {
    while ($report = mysqli_fetch_assoc($reportList)) {
        if ($cases = reportCases($report['reportId'])) {
            $report['cases'] = $cases;
            unset($cases);
        }
        $reportIdList[] = $report['reportId'];
        // reportId 1:N nw_file.iditem (&& nw_file.idtable = 4 ) OPTIONAL (get nw_file.*)

        // reportId 1:N nw_symbols2all.idrecord (&& nw_symbols2all.table =4) OPTIONAL
        //     nw_symbols2all.idsymbol 1:1 nw_symbol.id (&& nw_symbol.deleted=0) MANDATORY (get nw_symbol.*)
        $reports[$report['reportId']] = $report;
        $reports[$report['reportId']]['reportName'] = stripslashes($report['reportName'].'');
    }
    $reportParticipants = reportsParticipants($reportIdList);
    foreach ($reportParticipants as $participant) {
        $reports[$participant['reportId']]['participant'][] = [
            'participantRole' => $participant['participantRole'],
            'participantName' => stripslashes($participant['participantName']),
        ];
    }
    $latteParameters['reportsRecord'] = $reports;
    $latteParameters['reportCount'] = $reportCount;
} else {
    $latteParameters['warningReport'] = $text['notificationListEmpty'];
}
$latteParameters['reportType'] = reportType();
$latteParameters['reportStatus'] = reportStatus();
latteDrawTemplate('sparklet');
latteDrawTemplate('reports_body');
