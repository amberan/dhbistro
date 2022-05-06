<?php
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

$sqlFilter = DB_PREFIX.'report.reportId = '.$URL[2].' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
$reportSql = "SELECT
    ".DB_PREFIX."report.*,
    concat(ownerPerson.name,' ',ownerPerson.surname) as reportOwnerName,
    ownerUser.userName as reportOwnerUserName,
    concat(createdPerson.name,' ',createdPerson.surname) as reportCreatedByName,
    createdUser.userName as reportCreatedByUserName,
    concat(modifiedPerson.name,' ',modifiedPerson.surname) as reportModifiedByName,
    modifiedUser.userName as reportModifiedByUserName,
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
if (!is_numeric($URL[2])|| $user['aclReport'] < 1 || mysqli_num_rows($reportQuery) < 1 || ($report['reportSecret'] > $user['aclSecret'] && $report['reportOwner'] != $user['userId'])) {
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
    $latteParameters['report'] = $report;
    latteDrawTemplate('sparklet');
    latteDrawTemplate('report_view');
}
