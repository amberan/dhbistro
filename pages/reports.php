<?php


if ($URL[1] == 'reports' && isset($URL[2],$URL[3]) && $URL[2] == 'delete' && is_numeric($URL[3])) {
    if ($user['aclReport'] < 2) {
        unauthorizedAccess(4, 11, $URL[3]);
    } else {
        authorizedAccess(4, 11, $URL[3]);
        $deleteReportSql = 'UPDATE '.DB_PREFIX.'report SET reportDeleted=now(), reportModifiedBy='.$user['userId'].' WHERE reportId='.$URL[3];
        mysqli_query($database, $deleteReportSql);
        deleteAllUnread(1, $URL[3]);
    }
}

if ($URL[1] == 'reports' && isset($URL[2],$URL[3]) && $URL[2] == 'restore' && is_numeric($URL[3])) {
    if ($user['aclRoot'] < 1) {
        unauthorizedAccess(4, 17, $URL[3]);
    } else {
        authorizedAccess(4, 17, $URL[3]);
        $deleteReportSql = 'UPDATE '.DB_PREFIX.'report SET reportDeleted=0, reportModifiedBy='.$user['userId'].' WHERE reportId='.$URL[3];
        mysqli_query($database, $deleteReportSql);
        deleteAllUnread(1, $URL[3]);
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

if ($user['aclRoot'] < 1) {
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


//create new bool column for deletion
$reportsSql = "SELECT
    concat(ownerPerson.name,' ',ownerPerson.surname) as reportOwnerName,
    ownerUser.userName as reportOwnerUserName,
    concat(createdPerson.name,' ',createdPerson.surname) as reportCreatedByName,
    createdUser.userName as reportCreatedByUserName,
    concat(modifiedPerson.name,' ',modifiedPerson.surname) as reportModifiedByName,
    modifiedUser.userName as reportModifiedByUserName,
    ".DB_PREFIX."report.*,
    ".DB_PREFIX."unread.id AS 'unread',
    CASE WHEN ( reportDeleted < from_unixtime(1) OR reportDeleted IS NULL) THEN 'False' ELSE 'True' END AS reportDeletedBool
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
    $latteParameters['reportType'] = reportType();
    $latteParameters['reportStatus'] = reportStatus();
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('reports_body');
