<?php

$sqlFilter = DB_PREFIX.'report.reportId = '.$URL[2].' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
$reportSql = "SELECT
    ".DB_PREFIX."report.*,

    ".DB_PREFIX."unread.id AS 'unread'
    FROM ".DB_PREFIX."report
    LEFT JOIN ".DB_PREFIX."unread on  ".DB_PREFIX."report.reportId =  ".DB_PREFIX."unread.idrecord AND  ".DB_PREFIX."unread.idtable = 4 and  ".DB_PREFIX."unread.iduser=".$user['userId']."
    WHERE ".$sqlFilter;
$reportQuery = mysqli_query($database, $reportSql);
$report = mysqli_fetch_assoc($reportQuery);
if (!is_numeric($URL[2]) || $user['aclReport'] < 1 || mysqli_num_rows($reportQuery) < 1 || ($report['reportSecret'] > $user['aclSecret'] && $report['reportOwner'] != $user['userId'])) {
    unauthorizedAccess('report', 'read', $URL[2]);
} else {
    authorizedAccess('report', 'read', $URL[2]);
    if (isset($_POST['noteCreate'], $_POST['noteTitle'], $_POST['noteBody'])) {
        //TODO DB separate secret & private for notes
        $noteSecret = 0;
        if (isset($_POST['noteSecret'])) {
            $noteSecret = 1;
        }
        if (isset($_POST['notePrivate'])) {
            $noteSecret = 2;
        }

        if (noteCreate('report', $URL[2], $_POST['noteTitle'], $_POST['noteBody'], $noteSecret, $user['userId'])) {
            $_SESSION['message'] = $text['notificationCreated'];
            if (!isset($_POST['noteNotNew'])) {
                unreadRecords('report', $URL[2]);
            }
        } else {
            $_SESSION['message'] = $text['notificationNotCreated'];
        }
    }

    deleteUnread('report', $URL[2]);
    $report['reportName'] = stripslashes($report['reportName']);
    $latteParameters['title'] = $text['menuReports']." ".reportType($report['reportType']).": ".stripslashes($report['reportName']);
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
    $latteParameters['report'] = $report;
    $latteParameters['noteAddURL'] = '/reports/' . $URL[2];
    latteDrawTemplate('sparklet');
    latteDrawTemplate('report_view');
}
