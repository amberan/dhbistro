<?php
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

/**
 * list unfinished cases assigned to.
 *
 * @param int userId
 * @param mixed $userid
 *
 * @return array [id][name]
 */
function casesAssignedTo($userid): array
{
    global $database;

    $unfinishedcases[] = [];

    $casesListSql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title'
		FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case
		WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."case.status=0 AND ".DB_PREFIX."c2s.idsolver=".$userid."
        ORDER BY ".DB_PREFIX."case.title ASC";
    if ($casesList = mysqli_query($database, $casesListSql)) {
        while ($unfinishedcase = mysqli_fetch_assoc($casesList)) {
            $unfinishedcases[] = [$unfinishedcase['id'], $unfinishedcase['title']];
        }
    }
    return @$unfinishedcases;
}

//vypis pripadu musi oznacovat NEW - if (@$filter['new'] == null || ($filter['new'] == on && searchRecord(3,$rec['id']))) {
//pridat sloupec pro created
//prejmenovat datum za edited
