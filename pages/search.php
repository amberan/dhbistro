<?php

//SEARCH FILTER
$sqlFilterCase = $sqlFilterPerson = $sqlFilterCaseGroup = $sqlFilter = $sqlFilterReport = $sqlFilterGroup = $sqlFilterSymbol = '';
if (isset($_POST['filter']) && sizeof($_POST['filter']) > 0) {
    if (isset($_POST['filter']['search']) && strlen($_POST['filter']['search']) > 0) {
        filterSet('search', $_POST['filter']);
    } else {
        $tmpFilter = filterGet('search');
        $_POST['filter']['search'] = $tmpFilter['search'];
        filterSet('search', $_POST['filter']);
    }
    //TODO in case of seach_menu (no placeholder) do not reset archived, deleted, secret
}
$filterSearch = filterGet('search');
if (!isset($filterSearch['secret'])) {
    $sqlFilter .= ' AND secret = 0 AND secret <= '.$user['aclSecret'];
    $sqlFilterReport .= ' AND reportSecret = 0 AND reportSecret <= '.$user['aclSecret'];
    $sqlFilterSymbol .= ' AND symbol.secret = 0 AND symbol.secret <= '.$user['aclSecret'];
}
if ((!isset($filterSearch['deleted']) && $user['aclRoot']) || (!$user['aclRoot'])) {
    $sqlFilter .= ' AND deleted = 0 ';
    $sqlFilterReport .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1))';
    $sqlFilterSymbol .= ' AND symbol.deleted = 0 ';
}
if (!isset($filterSearch['archived'])) {
    $sqlFilterCase .= ' AND ('.DB_PREFIX.'case.caseArchived is null OR '.DB_PREFIX.'case.caseArchived  < from_unixtime(1))  ';
    $sqlFilterReport .= ' AND ('.DB_PREFIX.'report.reportArchived is null OR '.DB_PREFIX.'report.reportArchived  < from_unixtime(1)) ';
    $sqlFilterPerson .= ' AND (archived is null OR archived  < from_unixtime(1))  ';
    $sqlFilterGroup .= ' AND '.DB_PREFIX.'group.archived=0 ';
    $sqlFilterSymbol .= ' AND (symbol.archived is null OR symbol.archived  < from_unixtime(1)) ';
}

//persons
$filterSearch['side'] = filterSide();
$filterSearch['category'] = filterCategory();
$filterSearch['class'] = filterClass();

$latteParameters['filter'] = $filterSearch;

//CASE LIST
$sqlSearchCase = ' AND (title LIKE "%'.nocs($filterSearch['search']).'%" or contents LIKE  "%'.nocs($filterSearch['search']).'%")';
$sqlSortCase = ' ORDER BY 5 * MATCH(title) AGAINST ("'.nocs($filterSearch['search']).'") + MATCH(contents) AGAINST ("'.nocs($filterSearch['search']).'") DESC';
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
WHERE 1 " . $sqlFilterCase . $sqlFilter . $sqlSearchCase . " GROUP BY " . DB_PREFIX . "case.id " . $sqlSortCase;

$caseList = mysqli_query($database, $sql);
$caseCount = mysqli_num_rows($caseList);
if ($caseCount > 0) {
    $latteParameters['case_record'] = $caseList;
    $latteParameters['caseCount'] = $caseCount;
} else {
    $latteParameters['warningCase'] = $text['notificationListEmpty'];
}

//REPORT  LIST
$sqlSearchReport = ' AND (reportTask LIKE "%'.nocs($filterSearch['search']).'%" or reportDetail LIKE "%'.nocs($filterSearch['search']).'%" or reportImpact LIKE "%'.nocs($filterSearch['search']).'%" or reportInput LIKE "%'.nocs($filterSearch['search']).'%" or reportSummary LIKE  "%'.nocs($filterSearch['search']).'%")';
$sqlSortReport = 'ORDER BY 5 * MATCH(reportInput) AGAINST ("'.nocs($filterSearch['search']).'")
                + 3 * MATCH(reportSummary) AGAINST ("'.nocs($filterSearch['search']).'")
                + 2 * MATCH(reportTask) AGAINST ("'.nocs($filterSearch['search']).'")
                + 2 * MATCH(reportImpact) AGAINST ("'.nocs($filterSearch['search']).'")
                + MATCH(reportDetail) AGAINST ("'.nocs($filterSearch['search']).'") DESC';
$reportsSql = 'SELECT
    concat(ownerPerson.name," ",ownerPerson.surname) as reportOwnerName,
    ownerUser.userName as reportOwnerUserName,
    concat(createdPerson.name," ",createdPerson.surname) as reportCreatedByName,
    createdUser.userName as reportCreatedByUserName,
    concat(modifiedPerson.name," ",modifiedPerson.surname) as reportModifiedByName,
    modifiedUser.userName as reportModifiedByUserName,
    '.DB_PREFIX.'report.*,
    '.DB_PREFIX.'unread.id AS unread,
    CASE WHEN ( reportDeleted < from_unixtime(1) OR reportDeleted IS NULL) THEN "False" ELSE "True" END AS reportDeletedBool,
    CASE WHEN ( reportArchived < from_unixtime(1) OR reportArchived IS NULL) THEN "False" ELSE "True" END AS reportArchivedBool
    FROM '.DB_PREFIX.'report
    LEFT JOIN '.DB_PREFIX.'unread on  '.DB_PREFIX.'report.reportId =  '.DB_PREFIX.'unread.idrecord AND  '.DB_PREFIX.'unread.idtable = 4 and  '.DB_PREFIX.'unread.iduser='.$user['userId'].'
    LEFT JOIN '.DB_PREFIX.'user as ownerUser on '.DB_PREFIX.'report.reportOwner = ownerUser.userId
    LEFT JOIN '.DB_PREFIX.'person as ownerPerson on ownerUser.personId = ownerPerson.id AND ownerPerson.deleted = 0 AND ownerPerson.secret <= '.$user['aclSecret'].'
    LEFT JOIN '.DB_PREFIX.'user as createdUser on '.DB_PREFIX.'report.reportCreatedBy = createdUser.userId
    LEFT JOIN '.DB_PREFIX.'person as createdPerson on createdUser.personId = createdPerson.id AND createdPerson.deleted = 0 AND createdPerson.secret <= '.$user['aclSecret'].'
    LEFT JOIN '.DB_PREFIX.'user as modifiedUser on '.DB_PREFIX.'report.reportModifiedBy = modifiedUser.userId
    LEFT JOIN '.DB_PREFIX.'person as modifiedPerson on modifiedUser.personId = modifiedPerson.id and modifiedPerson.deleted = 0 AND modifiedPerson.secret <= '.$user['aclSecret'].'
    WHERE 1 '. $sqlFilterReport . $sqlSearchReport .' GROUP BY ' . DB_PREFIX . 'report.reportId '.$sqlSortReport;
sortingGet('report'); //."GROUP BY '.DB_PREFIX.'report.id';
$reportList = mysqli_query($database, $reportsSql);
$reportCount = mysqli_num_rows($reportList);

if ($reportCount > 0) {
    while ($report = mysqli_fetch_assoc($reportList)) {
        if ($cases = reportCases($report['reportId'])) {
            $report['cases'] = $cases;
            unset($cases);
        }
        $reportIdList[] = $report['reportId'];
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
    $latteParameters['warningReport'] = $text['notificationListEmpty'];
}

//PERSON LIST
$sqlSearchPerson = ' AND (surname LIKE "%'.nocs($filterSearch['search']).'%" or name LIKE  "%'.nocs($filterSearch['search']).'%" or contents LIKE  "%'.nocs($filterSearch['search']).'%")';
$sqlSortPerson = ' ORDER BY 5 * MATCH(surname)   AGAINST ("+(>'.nocs($filterSearch['search']).')" IN BOOLEAN MODE)
        + 3 * MATCH(name) AGAINST ("'.nocs($filterSearch['search']).'")
        + MATCH(contents) AGAINST ("'.nocs($filterSearch['search']).'") DESC';
$personSql = "SELECT ".DB_PREFIX."person.deleted,
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
    CASE WHEN ( LENGTH(".DB_PREFIX."person.surname) < 1 ) THEN ".DB_PREFIX."person.name ELSE concat(".DB_PREFIX."person.surname,', ',".DB_PREFIX."person.name) END as personFullname,
    CASE WHEN ( LENGTH(".DB_PREFIX."person.surname) < 1 ) THEN ' ' ELSE ".DB_PREFIX."person.surname END AS surname,
    CASE WHEN ( archived < from_unixtime(1) OR archived IS NULL) THEN 'False' ELSE 'True' END AS personArchivedBool,
    ".DB_PREFIX."person.id AS 'id',
    ".DB_PREFIX."person.symbol
FROM ".DB_PREFIX."person
LEFT JOIN  ".DB_PREFIX."unread on  ".DB_PREFIX."person.id =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 1 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
WHERE 1 ".$sqlFilterPerson. $sqlFilter. $sqlSearchPerson ."
GROUP BY ".DB_PREFIX."person.id ".$sqlSortPerson;

$personList = mysqli_query($database, $personSql);
$personCount = mysqli_num_rows($personList);

if ($personCount > 0) {
    $latteParameters['person_record'] = $personList;
    $latteParameters['personCount'] = $personCount;
} else {
    $latteParameters['warningPerson'] = $text['notificationListEmpty'];
}

//GROUP LIST
$sqlSearchGroup = ' AND (title LIKE "%'.nocs($filterSearch['search']).'%" or contents LIKE  "%'.nocs($filterSearch['search']).'%")';
$sqlSortGroup = 'ORDER BY 5 * MATCH(title) AGAINST ("'.nocs($filterSearch['search']).'")
    + MATCH(contents) AGAINST ("'.nocs($filterSearch['search']).'") DESC';
$groupsql = "SELECT ".DB_PREFIX."group.secret, ".DB_PREFIX."group.title , ".DB_PREFIX."group.id , ".DB_PREFIX."group.archived ,
    ".DB_PREFIX."group.datum as date_changed, ".DB_PREFIX."group.groupCreated, ".DB_PREFIX."group.deleted,  ".DB_PREFIX."unread.id as unread,
    CASE WHEN ( archived = 0 OR archived IS NULL) THEN 'False' ELSE 'True' END AS groupArchivedBool
    FROM ".DB_PREFIX."group
    LEFT JOIN  ".DB_PREFIX."unread on  ".DB_PREFIX."group.id =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 2 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    WHERE 1 ".$sqlFilterGroup. $sqlFilter. $sqlSearchGroup ."
    GROUP BY ".DB_PREFIX."group.id ".$sqlSortGroup;
$groupList = mysqli_query($database, $groupsql);
$groupCount = mysqli_num_rows($groupList);
if ($groupCount > 0) {
    $latteParameters['groupCount'] = $groupCount;
    $latteParameters['group_record'] = $groupList;
} else {
    $latteParameters['warningGroup'] = $text['notificationListEmpty'];
}

//SYMBOL LIST
$sqlSearchSymbol = ' AND (symbol.desc LIKE "%'.nocs($filterSearch['search']).'%")';
$sqlSortSymbol = 'ORDER BY 5 * MATCH(symbol.desc) AGAINST ("'.nocs($filterSearch['search']).'") DESC';
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
                WHERE 1 ".$sqlFilterSymbol.$sqlSearchSymbol." AND symbol.secret<=".$user['aclSecret']." AND symbol.assigned=0
                GROUP BY  symbol.id ".$sqlSortSymbol;
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
    $latteParameters['warningSymbols'] = $text['notificationListEmpty'];
}

//NOTES LISTING
$sqlSearchNotes = ' AND (title LIKE "%'.nocs($filterSearch['search']).'%" or note LIKE "%'.nocs($filterSearch['search']).'%" )';
$sqlSortNotes = 'ORDER BY 5 * MATCH(title) AGAINST ("'.nocs($filterSearch['search']).'") + MATCH(note) AGAINST ("'.nocs($filterSearch['search']).'") DESC';
$sqlNotes = "SELECT
            ".DB_PREFIX."note.datum as noteCreated,
            ".DB_PREFIX."note.title ,
            ".DB_PREFIX."note.id AS 'id',
            ".DB_PREFIX."note.idtable ,
            ".DB_PREFIX."note.iditem ,
            ".DB_PREFIX."note.secret ,
            ".DB_PREFIX."note.deleted
		FROM ".DB_PREFIX."note
		WHERE 1 ".$sqlFilter.$sqlSearchNotes.
        $sqlSortNotes;

$noteList = mysqli_query($database, $sqlNotes);
$noteCount = mysqli_num_rows($noteList);

if ($noteCount > 0) {
    while ($note = mysqli_fetch_assoc($noteList)) {
        switch ($note['idtable']) {
            case 1:
                $notePersonSql = 'SELECT '.DB_PREFIX.'person.surname AS surname, '.DB_PREFIX.'person.id AS id, '.DB_PREFIX.'person.name AS name, '.DB_PREFIX.'person.secret AS secret, deleted,
                    CASE WHEN ( archived < from_unixtime(1) OR archived IS NULL) THEN "False" ELSE "True" END AS personArchivedBool
                    FROM '.DB_PREFIX.'person
                    WHERE '.DB_PREFIX.'person.secret <= '.$user['aclSecret'].' AND id = '.$note['iditem'].' AND deleted <= '.$user['aclRoot'].' ORDER BY surname';
                $notePersonQuery = mysqli_query($database, $notePersonSql);
                while ($notePerson = mysqli_fetch_assoc($notePersonQuery)) {
                    $note['relatedTitle'] = $notePerson['surname']." ".$notePerson['name'];
                    $note['type'] = $text['person'];
                    if ($notePerson['secret']) {
                        $note['relatedSecret'] = true;
                    }
                    if ($notePerson['deleted']) {
                        $note['relatedDeleted'] = true;
                    }
                    if ($notePerson['personArchivedBool'] == 'True') {
                        $note['relatedArchived'] = true;
                    }
                    $note['relatedLink'] = "/readperson.php?rid=".$notePerson['id']."&amp;hidenotes=0";
                }
                break;
            case 2:
                $noteGroupSql = "SELECT ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.secret AS 'secret', deleted,
                    ".DB_PREFIX."group.archived
                    FROM ".DB_PREFIX."group
                    WHERE ".DB_PREFIX."group.secret <= ".$user['aclSecret']." AND id = ".$note['iditem']." AND deleted <= ".$user['aclRoot']." ORDER BY title";
                $noteGroupQuery = mysqli_query($database, $noteGroupSql);
                while ($noteGroup = mysqli_fetch_assoc($noteGroupQuery)) {
                    $note['relatedTitle'] = $noteGroup['title'];
                    $note['type'] = $text['group'];
                    if ($noteGroup['secret']) {
                        $note['relatedSecret'] = true;
                    }
                    if ($noteGroup['deleted']) {
                        $note['relatedDeleted'] = true;
                    }
                    if ($noteGroup['archived'] == 'True') {
                        $note['relatedArchived'] = true;
                    }
                    $note['relatedLink'] = "/readgroup.php?rid=".$noteGroup['id']."&amp;hidenotes=0";
                }
                break;
            case 3:
                $noteCaseSql = "SELECT ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.secret AS 'secret', deleted,
                    CASE WHEN ( caseArchived < from_unixtime(1) OR caseArchived IS NULL) THEN 'False' ELSE 'True' END AS caseArchivedBool
                    FROM ".DB_PREFIX."case
                    WHERE ".DB_PREFIX."case.secret <= ".$user['aclSecret']." AND id = ".$note['iditem']." AND deleted <= ".$user['aclRoot']." ORDER BY title";
                $noteCaseQuery = mysqli_query($database, $noteCaseSql);
                while ($noteCase = mysqli_fetch_assoc($noteCaseQuery)) {
                    $note['relatedTitle'] = $noteCase['title'];
                    $note['type'] = $text['case'];
                    if ($noteCase['secret']) {
                        $note['relatedSecret'] = true;
                    }
                    if ($noteCase['deleted']) {
                        $note['relatedDeleted'] = true;
                    }
                    if ($noteCase['caseArchivedBool'] == 'True') {
                        $note['relatedArchived'] = true;
                    }
                    $note['relatedLink'] = "/readcase.php?rid=".$noteCase['id']."&amp;hidenotes=0";
                }
                break;
            case 4:
                $noteReportSql = 'SELECT '.DB_PREFIX.'report.reportName , '.DB_PREFIX.'report.reportId, '.DB_PREFIX.'report.reportSecret AS secret,
                        CASE WHEN ( reportDeleted < from_unixtime(1) OR reportDeleted IS NULL) THEN "False" ELSE "True" END AS deleted,
                        CASE WHEN ( reportArchived < from_unixtime(1) OR reportArchived IS NULL) THEN "False" ELSE "True" END AS reportArchivedBool
                    FROM '.DB_PREFIX.'report
                    WHERE '.DB_PREFIX.'report.reportSecret <= '.$user['aclSecret'].' AND reportId = '.$note['iditem'].'
                    ORDER BY reportName';
                $noteReportQuery = mysqli_query($database, $noteReportSql);
                if (mysqli_num_rows($noteReportQuery)) {
                    $noteReport = mysqli_fetch_assoc($noteReportQuery);
                    $note['relatedTitle'] = $noteReport['reportName'];
                    $note['type'] = $text['report'];
                    if ($noteReport['secret']) {
                        $note['relatedSecret'] = true;
                    }
                    if ($noteReport['deleted'] == 'True') {
                        $note['relatedDeleted'] = true;
                    }
                    if ($noteReport['reportArchivedBool'] == 'True') {
                        $note['relatedArchived'] = true;
                    }
                    $note['relatedLink'] = "/reports/".$noteReport['reportId'];
                } else {
                    $deleted = true;
                }
                break;
            case 7:
                $noteSymbolSql = "SELECT ".DB_PREFIX."symbol.desc , ".DB_PREFIX."symbol.id, ".DB_PREFIX."symbol.secret AS 'secret', ".DB_PREFIX."symbol.deleted,
                        CASE WHEN ( ".DB_PREFIX."symbol.archived < from_unixtime(1) OR ".DB_PREFIX."symbol.archived IS NULL) THEN 'False' ELSE 'True' END AS symbolArchivedBool
                    FROM ".DB_PREFIX."symbol
                    WHERE ".DB_PREFIX."symbol.secret <= ".$user['aclSecret']." AND id = ".$note['iditem']." AND deleted <= ".$user['aclRoot']." ORDER BY created desc";
                $noteSymbolQuery = mysqli_query($database, $noteSymbolSql);
                if ($noteSymbol = mysqli_fetch_assoc($noteSymbolQuery)) {
                    $note['relatedTitle'] = strip_tags($noteSymbol['desc']);
                    $note['type'] = $text['symbol'];
                    if ($noteSymbol['secret']) {
                        $note['relatedSecret'] = true;
                    }
                    if ($noteSymbol['deleted']) {
                        $note['relatedDeleted'] = true;
                    }
                    if ($noteSymbol['symbolArchivedBool'] == 'True') {
                        $note['relatedArchived'] = true;
                    }
                    $note['relatedLink'] = "/readsymbol.php?rid=".$noteSymbol['id'];
                } else {
                    $deleted = true;
                }
                break;
            default:
                $note['relatedTitle'] = $note['title'];
                $note['type'] = $text['other'];
                break;
        }

        if ((isset($note['relatedDeleted']) || $note['deleted']) && (!isset($filterSearch['deleted']) || $user['aclRoot'] < 1)) {
            $note['skip'] = 'DEL';
            $noteCount--;
        } elseif ((isset($note['relatedSecret']) || $note['secret']) && (!isset($filterSearch['secret']) || $user['aclSecret'] < 1)) {
            $note['skip'] = 'SEC';
            $noteCount--;
        } elseif (isset($note['relatedArchived']) && !isset($filterSearch['archived'])) {
            $note['skip'] = 'ARCH';
            $noteCount--;
        }
        if (!isset($note['skip'])) {
            $noteRecords[] = $note;
        }
    }
}

if ($noteCount != 0) {
    $latteParameters['noteRecords'] = $noteRecords;
    $latteParameters['noteCount'] = $noteCount;
} else {
    $latteParameters['warningNotes'] = $text['notificationListEmpty'];
}


latteDrawTemplate('sparklet');
latteDrawTemplate('search');
