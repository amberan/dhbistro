<?php

use Tracy\Debugger;

if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['reportId'])) {
    authorizedAccess('report', 'fileAdd', $_POST['reportId']);
    $newname = Time().MD5(uniqid(Time().Rand()));
    move_uploaded_file($_FILES['attachment']['tmp_name'], './files/'.$newname);
    if (isset($_POST['secret']) && $_POST['secret'] == 'on') {
        $_POST['secret'] = 1;
    } else {
        $_POST['secret'] = 0;
    }

    $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".Time()."','".$user['userId']."','4','".$_POST['reportId']."',".$_POST['secret'].")";
    mysqli_query($database, $sql);
    unreadRecords(4, $_POST['reportId']);
    $_SESSION['message'] = 'Soubor uložen';
} else {
    if (isset($_POST['uploadfile'])) {
        $_SESSION['message'] = 'Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.';
    }
}
if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
    authorizedAccess('report', 'fileDelete', $_POST['reportId']);
    $deleteCheckSql = "SELECT *
            FROM ".DB_PREFIX."file
            join ".DB_PREFIX."report on ".DB_PREFIX."file.iditem = ".DB_PREFIX."report.reportId
            WHERE ".DB_PREFIX."file.id=".$_GET['deletefile'];
    $deleteCheckQuery = mysqli_query($database, $deleteCheckSql);
    $deleteCheck = mysqli_fetch_assoc($deleteCheckQuery);
    if ($deleteCheck['iduser'] == $user['userId'] || $deleteCheck['reportOwner'] == $user['userId'] || $userId['aclReport'] > 1) {
        UnLink('./files/'.$deleteCheck['uniquename']);
        mysqli_query($database, "DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
        $_SESSION['message'] = 'Soubor odstranen';
    }
}

if (isset($_POST['addtoareport'])) {
    authorizedAccess('report', 'link', $_POST['reportid']);

    switch ($_POST['fdead']) {
        case 0: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
            break;
        case 1: $fsql_dead = '';
            break;
        default: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
    }
    switch ($_POST['farchiv']) {
        case 0: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  ';
            break;
        case 1: $fsql_archiv = '';
            break;
        default: $fsql_archiv = ' AND ('.DB_PREFIX.'person.archived is null OR '.DB_PREFIX.'person.archived  < from_unixtime(1))  ';
    }
    $sqlFilter = " ".DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret']." ";

    $deletePersonFromReportSql = 'DELETE c FROM '.DB_PREFIX.'ar2p as c, '.DB_PREFIX.'person WHERE '.$sqlFilter.$fsql_dead.$fsql_archiv.' AND  c.idperson='.DB_PREFIX.'person.id AND c.idreport='.$_POST['reportid'];
    mysqli_query($database, $deletePersonFromReportSql);

    if (isset($_POST['person'])) {
        $person = $_POST['person'];
    }
    if (isset($_POST['role'])) {
        $role = $_POST['role'];
    }
    if (isset($_POST['person'])) {
        for ($i = 0; $i < Count($person); $i++) {
            $insertPersonToReportSql = "INSERT INTO " . DB_PREFIX . "ar2p (idperson, idreport, iduser, role)
                VALUES('" . $person[$i] . "','" . $_POST['reportid'] . "','" . $user['userId'] . "','0" . $role[$i] . "');";
            Debugger::log($config['version'].': '.$insertPersonToReportSql);
            mysqli_query($database, $insertPersonToReportSql);
        }
    }
    $latteParameters['message'] = 'Osoby příslušné k hlášení uloženy.';
}


// CREATE
if (isset($_POST['reportName'],$_POST['reportType']) && $user['aclReport'] >= 1 && $URL[2] == 0) {
    if (!isset($_POST['reportSecret'])) {
        $_POST['reportSecret'] = 0;
    }
    $newSql = 'INSERT INTO '.DB_PREFIX.'report (reportOwner,reportCreatedBy,reportCreated,reportStatus,reportSecret,reportType,reportEventStart,reportEventEnd,reportEventDate)
        VALUES ('.$_POST['reportOwner'].','.$user['userId'].',NOW(),'.$_POST['reportStatus'].','.$_POST['reportSecret'].','.$_POST['reportType'].',"'.$_POST['reportEventStart'].'","'.$_POST['reportEventEnd'].'","'.$_POST['reportEventDate'].'")';
    mysqli_query($database, $newSql);
    $_POST['reportId'] = $URL[2] = mysqli_insert_id($database);
}
// UPDATE
if (isset($_POST['reportId'],$_POST['reportName'],$_POST['reportType']) && $user['aclReport'] >= 1) {
    authorizedAccess('report', 'edit', $_POST['reportId']);
    if ($_POST['reportStatus'] <> 0) {
        unreadRecords(4, $_POST['reportId']);
    }
    $sqlArchived = ' ';
    if (isset($_POST['reportArchivedCheck']) && !isset($_POST['reportArchived'])) {
        $sqlArchived = 'reportArchived=NOW(),';
    }
    if (!isset($_POST['reportSecret'])) {
        $_POST['reportSecret'] = 0;
    }
    $updateSql = "UPDATE ".DB_PREFIX."report SET
    reportName='".$_POST['reportName']."',
    reportTask='".$_POST['reportTask']."',
    reportType='".$_POST['reportType']."',
    reportOwner='".$_POST['reportOwner']."',
    reportSecret='".$_POST['reportSecret']."',
    reportEventDate='".$_POST['reportEventDate']."',
    reportEventStart='".$_POST['reportEventStart']."',
    reportEventEnd='".$_POST['reportEventEnd']."',
    reportStatus='".$_POST['reportStatus']."',
    reportSummary='".$_POST['reportSummary']."',
    reportImpact='".$_POST['reportImpact']."',
    reportDetail='".$_POST['reportDetail']."',
    reportCost='".$_POST['reportCost']."',
    reportInput='".$_POST['reportInput']."',
    ".$sqlArchived."
    reportModifiedBy=".$user['userId'].",
    reportModified=NOW()
    WHERE reportId=".$_POST['reportId'];
    mysqli_query($database, $updateSql);
    $_SESSION['message'] = 'Hlášení uloženo';
}
// READ
$sqlFilter = DB_PREFIX."report.reportId = ".$URL[2];
if ($user['aclRoot'] < 1) {
    $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
}
$reportSql = "SELECT
    ".DB_PREFIX."report.*,
    date(".DB_PREFIX."report.reportEventDate) as reportEventDate,
    ".DB_PREFIX."unread.id AS 'unread'
    FROM ".DB_PREFIX."report
    LEFT JOIN ".DB_PREFIX."unread on  ".DB_PREFIX."report.reportId =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 4 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    WHERE ".$sqlFilter;
$reportQuery = mysqli_query($database, $reportSql);
$report = mysqli_fetch_assoc($reportQuery);
if (!is_numeric($URL[2]) || $user['aclReport'] < 1 || (isset($report) && $report['reportSecret'] > $user['aclSecret'] && $report['reportOwner'] != $user['userId'])) {
    unauthorizedAccess('report', 'read', $URL[2]);
} else {
    authorizedAccess('report', 'read', $URL[2]);
    deleteUnread('report', $URL[2]);
    if (isset($report)) {
        $report['reportName'] = stripslashes($report['reportName'].'');
        $latteParameters['title'] = $text['menuReports']." ".reportType($report['reportType']).": ".stripslashes($report['reportName'].'');
        $latteParameters['reportType'] = reportType();
        $report['reportOwnerName'] = AuthorDB($report['reportOwner']);
        $report['reportCreatedByName'] = AuthorDB($report['reportCreatedBy']);
        $report['reportModifiedByName'] = AuthorDB($report['reportModifiedBy']);
        $latteParameters['reportParticipants'] = reportParticipants($URL[2]);
        $latteParameters['reportRole'] = reportRole();
        $latteParameters['reportCases'] = reportCases($URL[2]);
        $latteParameters['reportSymbols'] = reportSymbols($URL[2]);
        $latteParameters['reportFiles'] = reportFiles($URL[2]);
        $latteParameters['reportNotes'] = reportNotes($URL[2]);
        $latteParameters['reportStatus'] = reportStatus();
        $latteParameters['suitableUsers'] = listUsersSuitable();
        $latteParameters['report'] = $report;
        $latteParameters['noteAddURL'] = '/reports/' . $URL[2];
    } else { // NEW
        $latteParameters['title'] = $text['menuReports'];
        $latteParameters['reportType'] = reportType();
        $latteParameters['reportRole'] = reportRole();
        $latteParameters['reportStatus'] = reportStatus();
        $latteParameters['suitableUsers'] = listUsersSuitable();
        $report = [
            'reportId' => '0',
            'reportEventDate' => date('Y-m-d',time()),
            'reportOwner' => $user['userId'],
        ];
        $latteParameters['report'] = $report;
    }
    latteDrawTemplate('sparklet');
    latteDrawTemplate('report_edit');
}
