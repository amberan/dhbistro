<?php

function symbolCases($symbolId)
{
    global $database,$user;
    $caseSql = 'SELECT ' . DB_PREFIX . 'case.id AS caseId, ' . DB_PREFIX . 'case.title AS caseName
                FROM ' . DB_PREFIX . 'symbol2all as symbol2all
                JOIN ' . DB_PREFIX . 'case on symbol2all.idrecord = ' . DB_PREFIX . 'case.id
                WHERE ' . DB_PREFIX . 'case.deleted in (0,' . $user['aclRoot'] . ') AND ' . DB_PREFIX . 'case.secret<=' . $user['aclSecret'] . ' AND ' . DB_PREFIX . 'case.id=symbol2all.idrecord AND symbol2all.idsymbol=' . $symbolId . ' AND symbol2all.table=3
                ORDER BY ' . DB_PREFIX . 'case.title ASC';
    if ($caseList = mysqli_query($database, $caseSql)) {
        $cases = [];
        while ($case = mysqli_fetch_assoc($caseList)) {
            $cases[] = [
                'caseId' => $case['caseId'],
                'caseName' => $case['caseName'],
            ];
        }
    }
    if (isset($cases)) {
        return $cases;
    } else {
        return false;
    }
}

function symbolNotes($symbolId)
{
    //      <br/> {$note['noteSecret']}: {$note['noteTitle']} {$note['noteCreatedBy']} {$note['noteCreatedByPerson']} {$note['noteCreatedByUser']} {$note['noteBody']} <br/>

    global $database,$user;
    $noteSql = 'SELECT note.iduser AS noteCreatedBy, note.title AS noteTitle, note.note AS note, note.secret AS noteSecret, ' . DB_PREFIX . 'user.userName AS userName, note.id AS noteId,
            note.datum as noteCreated, person.name as personName, person.surname as personSurname, note.deleted as noteDeleted
        FROM ' . DB_PREFIX . 'note as note
        JOIN ' . DB_PREFIX . 'user ON note.iduser = ' . DB_PREFIX . 'user.userId
        JOIN ' . DB_PREFIX . 'person as person ON ' . DB_PREFIX . 'user.personId = person.id
        WHERE note.deleted in (0,' . $user['aclRoot'] . ') AND (note.secret<=' . $user['aclSecret'] . ' OR note.iduser=' . $user['userId'] . ' ) AND note.iduser=' . DB_PREFIX . 'user.userId AND note.iditem=' . $symbolId . ' AND note.idtable=7
        ORDER BY note.datum DESC';
    if ($noteList = mysqli_query($database, $noteSql)) {
        $notes = [];
        while ($note = mysqli_fetch_assoc($noteList)) {
            $notes[] = [
                'noteId' => $note['noteId'],
                'noteCreated' => webdateTime($note['noteCreated']),
                'noteCreatedBy' => $note['noteCreatedBy'],
                'noteCreatedByPerson' => $note['personName'] . ' ' . $note['personSurname'],
                'noteCreatedByUser' => $note['userName'],
                'noteTitle' => $note['noteTitle'],
                'noteBody' => strip_tags($note['note']),
                'noteDeleted' => $note['noteDeleted'],
                'noteSecret' => $note['noteSecret'],
            ];
        }
    }
    if (isset($notes)) {
        return $notes;
    } else {
        return false;
    }
}

function symbolReports($symbolId)
{
    global $database,$user;
    //TODO deleted reports for root
    $reportSql = 'SELECT report.reportId, report.reportName
    FROM ' . DB_PREFIX . 'symbol2all as symbol2all
    JOIN ' . DB_PREFIX . 'report as report on symbol2all.idrecord = report.reportId
    WHERE report.reportSecret<=' . $user['aclSecret'] . ' AND (report.reportDeleted is null OR report.reportDeleted < from_unixtime(1)) AND report.reportId=symbol2all.idrecord AND symbol2all.idsymbol=' . $symbolId . ' AND symbol2all.table=4
    ORDER BY report.reportName ASC';

    if ($reportList = mysqli_query($database, $reportSql)) {
        $reports = [];
        while ($report = mysqli_fetch_assoc($reportList)) {
            $reports[] = [
                'reportId' => $report['reportId'],
                'reportName' => $report['reportName'],
            ];
        }
    }
    if (isset($reports)) {
        return $reports;
    } else {
        return false;
    }
}
