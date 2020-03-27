<?php


/** 
 * list unfinished cases assigned to 
 * @param int userId
 * @return array [id][name]
 */
function casesAssignedTo($userid) {
        global $database;


        $casesListSql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."case.status=0 AND ".DB_PREFIX."c2s.idsolver=".$userid." ORDER BY ".DB_PREFIX."case.title ASC";
	    $casesList = mysqli_query ($database,$casesListSql);
	    while ($unfinishedcase = mysqli_fetch_assoc ($casesList)) {
	        $unfinishedcases[] = array ($unfinishedcase['id'], $unfinishedcase['title']);
        }
        return $unfinishedcases;
    }