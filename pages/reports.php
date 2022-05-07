<?php
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);


if (isset($_POST['reportid'])) {
    $autharray = mysqli_fetch_assoc(mysqli_query($database, "SELECT iduser FROM ".DB_PREFIX."report WHERE id=".$_POST['reportid']));
    $author = $autharray['iduser'];
}
    if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
        authorizedAccess(4, 11, $_REQUEST['delete']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."report SET deleted=1 WHERE id=".$_REQUEST['delete']);
        deleteAllUnread($_REQUEST['table'], $_REQUEST['delete']);
    }
    if (isset($_POST['insertrep']) && !preg_match('/^[[:blank:]]*$/i', $_POST['label']) && !preg_match('/^[[:blank:]]*$/i', $_POST['task']) && !preg_match('/^[[:blank:]]*$/i', $_POST['summary']) && !preg_match('/^[[:blank:]]*$/i', $_POST['impact']) && !preg_match('/^[[:blank:]]*$/i', $_POST['details']) && !preg_match('/^[[:blank:]]*$/i', $_POST['start']) && !preg_match('/^[[:blank:]]*$/i', $_POST['end']) && !preg_match('/^[[:blank:]]*$/i', $_POST['energy']) && !preg_match('/^[[:blank:]]*$/i', $_POST['inputs']) && is_numeric($_POST['secret']) && is_numeric($_POST['status']) && is_numeric($_POST['type'])) {
        $adatum = mktime(0, 0, 0, $_POST['adatummonth'], $_POST['adatumday'], $_POST['adatumyear']);
        $ures = mysqli_query($database, "SELECT id FROM ".DB_PREFIX."report WHERE UCASE(label)=UCASE('".$_POST['label']."')");
        if (mysqli_num_rows($ures)) {
            $latteParameters['message'] = 'Hlášení nepřidáno - Toto označení hlášení již existuje, změňte ho.';
        } else {
            $latteParameters['message'] = 'Hlášení uloženo';
            mysqli_query($database, "INSERT INTO ".DB_PREFIX."report (label, datum, iduser, task, summary, impacts, details, secret, deleted, status, type, adatum, start, end, energy, inputs) VALUES('".$_POST['label']."','".Time()."','".$user['userId']."','".$_POST['task']."','".$_POST['summary']."','".$_POST['impact']."','".$_POST['details']."','".$_POST['secret']."','0','".$_POST['status']."','".$_POST['type']."','".$adatum."','".$_POST['start']."','".$_POST['end']."','".$_POST['energy']."','".$_POST['inputs']."')");
            $ridarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT id FROM ".DB_PREFIX."report WHERE UCASE(label)=UCASE('".$_POST['label']."')"));
            $rid = $ridarray['id'];
            authorizedAccess(4, 3, $rid);
            if ($_POST['status'] <> 0) {
                unreadRecords(4, $rid);
            }
            Header('Location: /reports/'.$rid);
        }
    } else {
        if (isset($_POST['insertrep'])) {
            $latteParameters['message'] = 'Hlášení nepřidáno - Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva. Pamatujte, že všechna pole musí být vyplněná.';
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


$reportsSql = "SELECT
    concat(ownerPerson.name,' ',ownerPerson.surname) as reportOwnerName,
    ownerUser.userName as reportOwnerUserName,
    concat(createdPerson.name,' ',createdPerson.surname) as reportCreatedByName,
    createdUser.userName as reportCreatedByUserName,
    concat(modifiedPerson.name,' ',modifiedPerson.surname) as reportModifiedByName,
    modifiedUser.userName as reportModifiedByUserName,
    ".DB_PREFIX."report.*,
    ".DB_PREFIX."unread.id AS 'unread'
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
    }
    $reportParticipants = reportsParticipants($reportIdList);
    foreach ($reportParticipants as $participant) {
        $reports[$participant['reportId']]['participant'][] = array('participantRole' => $participant['participantRole'],
                                                                    'participantName' => $participant['participantName']);
    }
    $latteParameters['reportsRecord'] = $reports;
    $latteParameters['reportCount'] = $reportCount;
    $latteParameters['reportType'] = reportType();
    $latteParameters['reportStatus'] = reportStatus();
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('reports');