<?php

/** 
 * list unfinished reports assigned to 
 * @param int userId
 * @return array [id][name]
 */
function reportsAssignedTo($userid): array
{
    global $database;

    $reportsListSql = "SELECT ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.iduser=".$userid." AND ".DB_PREFIX."report.status=0 AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC";
    $reportsList = mysqli_query ($database,$reportsListSql);
    while ($unfinishedReport = mysqli_fetch_assoc ($reportsList)) {
        $unfinishedReports[] = array ($unfinishedReport['id'], $unfinishedReport['label']);
    }

    return @$unfinishedReports;
}