<?php
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

//TODO evaluation of "new"
// if data are send undelete draft
// else create "deleted" record
// TODO LATTE if new - add draft flag, new form target, hide items that needs ID for creating relations


if (isset($URL[2]) && $URL[2] == 'new') {
    //create new report in draft
    $newSql = 'INSERT INTO '.DB_PREFIX.'report (reportOwner,reportCreatedBy,reportCreated,reportStatus,reportSecret,reportType,reportEventStart,reportEventEnd,reportEventDate)
        VALUES ('.$user['userId'].','.$user['userId'].',NOW(),0,0,1,\'-\',\'-\',now())';
    mysqli_query($database, $newSql);
    $URL[2] = $reportNewId = mysqli_insert_id($database);

    $latteParameters['reportNewId'] = $reportNewId;
}


if (isset($_POST['reportId'],$_POST['reportName'],$_POST['reportType']) && $user['aclReport'] >= 1) {
    authorizedAccess(4, 2, $_POST['reportId']);
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



if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['reportId'])) {
    authorizedAccess(4, 4, $_POST['reportId']);
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
        authorizedAccess(4, 5, $_POST['reportId']);
        $deleteCheckSql = "SELECT *
            FROM ".DB_PREFIX."file
            join ".DB_PREFIX."report on ".DB_PREFIX."file.iditem = ".DB_PREFIX."report.reportId
            WHERE ".DB_PREFIX."file.id=".$_GET['deletefile'];
        $deleteCheckQuery = mysqli_query($database, $deleteCheckSql);
        $deleteCheck = mysqli_fetch_assoc($deleteCheckQuery);
        if ($deleteCheck['iduser'] == $user['userId'] || $deleteCheck['reportOwner'] == $user['userId']) {
            UnLink('./files/'.$deleteCheck['uniquename']);
            mysqli_query($database, "DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
            $_SESSION['message'] = 'Soubor odstranen';
        }
    }





$sqlFilter = DB_PREFIX."report.reportId = ".$URL[2];
if ($user['aclRoot'] < 1) {
    $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
}
$reportSql = "SELECT
    ".DB_PREFIX."report.*,
    concat(ownerPerson.name,' ',ownerPerson.surname) as reportOwnerName,
    ownerUser.userName as reportOwnerUserName,
    concat(createdPerson.name,' ',createdPerson.surname) as reportCreatedByName,
    createdUser.userName as reportCreatedByUserName,
    concat(modifiedPerson.name,' ',modifiedPerson.surname) as reportModifiedByName,
    modifiedUser.userName as reportModifiedByUserName,
    date(".DB_PREFIX."report.reportEventDate) as reportEventDate,
    ".DB_PREFIX."unread.id AS 'unread'
    FROM ".DB_PREFIX."report
    LEFT JOIN ".DB_PREFIX."unread on  ".DB_PREFIX."report.reportId =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 4 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    LEFT JOIN ".DB_PREFIX."user as ownerUser on ".DB_PREFIX."report.reportOwner = ownerUser.userId
    LEFT JOIN ".DB_PREFIX."person as ownerPerson on ownerUser.personId = ownerPerson.id AND ownerPerson.deleted = 0 AND ownerPerson.secret <= ".$user['aclSecret']."
    LEFT JOIN ".DB_PREFIX."user as createdUser on ".DB_PREFIX."report.reportCreatedBy = createdUser.userId
    LEFT JOIN ".DB_PREFIX."person as createdPerson on createdUser.personId = createdPerson.id AND createdPerson.deleted = 0 AND createdPerson.secret <= ".$user['aclSecret']."
    LEFT JOIN ".DB_PREFIX."user as modifiedUser on ".DB_PREFIX."report.reportModifiedBy = modifiedUser.userId
    LEFT JOIN ".DB_PREFIX."person as modifiedPerson on modifiedUser.personId = modifiedPerson.id and modifiedPerson.deleted = 0 AND modifiedPerson.secret <= ".$user['aclSecret']."
    WHERE ".$sqlFilter;
$reportQuery = mysqli_query($database, $reportSql);
$report = mysqli_fetch_assoc($reportQuery);
if (!is_numeric($URL[2])  || $user['aclReport'] < 1 || mysqli_num_rows($reportQuery) < 1|| ($report['reportSecret'] > $user['aclSecret'] && $report['reportOwner'] == $user['userId'])) {
    unauthorizedAccess(4, 1, $URL[2]);
} else {
    authorizedAccess(4, 1, $URL[2]);
    deleteUnread(4, $URL[2]);
    $latteParameters['title'] = $text['hlaseni']." ".reportType($report['reportType']).": ".stripslashes($report['reportName']);
    $latteParameters['reportType'] = reportType();
    $latteParameters['reportParticipants'] = reportParticipants($URL[2]);
    $latteParameters['reportRole'] = reportRole();
    $latteParameters['reportCases'] = reportCases($URL[2]);
    $latteParameters['reportSymbols'] = reportSymbols($URL[2]);
    $latteParameters['reportFiles'] = reportFiles($URL[2]);
    $latteParameters['reportNotes'] = reportNotes($URL[2]);
    $latteParameters['reportStatus'] = reportStatus();
    $latteParameters['suitableUsers'] = listUsersSuitable();
    $latteParameters['report'] = $report;
    latteDrawTemplate('sparklet');
    latteDrawTemplate('report_edit');
}
