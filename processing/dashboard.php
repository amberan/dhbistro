<?php

// REPORTS
$sql_unfinishedreports = "SELECT ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.iduser=".$usrinfo['id']." AND ".DB_PREFIX."report.status=0 AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC";
$unfinishedreports = mysqli_query ($database,$sql_unfinishedreports);
$unfinishedreports_count = mysqli_num_rows ($unfinishedreports);
$latteParameters['unfinishedreports'] = $unfinishedreports_count;
if ($unfinishedreports_count > 0) {
    while ($unfinishedreports_list = mysqli_fetch_assoc ($unfinishedreports)) {
        $reports[] = array ($unfinishedreports_list['id'], StripSlashes ($unfinishedreports_list['label']));
    }
    $latteParameters['reports'] = $reports;
} else {
    $latteParameters['reportsnone'] = $text['zadnanedokoncenahlaseni'];
}

// CASES
$sql_unfinishedcases = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." AND ".DB_PREFIX."case.status<>1 AND ".DB_PREFIX."case.deleted=0 ORDER BY ".DB_PREFIX."case.title ASC";
$unfinishedcases = mysqli_query ($database,$sql_unfinishedcases);
$unfinishedcases_count = mysqli_num_rows ($unfinishedcases);
$latteParameters['unfinishedcases'] = $unfinishedcases_count;
if ($unfinishedcases_count > 0) {
    while ($unfinishedcases_list = mysqli_fetch_assoc ($unfinishedcases)) {
        $cases[] = array ($unfinishedcases_list['id'], StripSlashes ($unfinishedcases_list['title']));
    }
    $latteParameters['cases'] = $cases;
} else {
    $latteParameters['casesnone'] = $text['zadnanedokoncenahlaseni'];
}

// TASKS
$sql_unfinishedtasks = "SELECT * FROM ".DB_PREFIX."task WHERE ".DB_PREFIX."task.iduser=".$usrinfo['id']." AND ".DB_PREFIX."task.status=0 ORDER BY ".DB_PREFIX."task.created ASC";
$unfinishedtasks = mysqli_query ($database,$sql_unfinishedtasks);
$unfinishedtasks_count = mysqli_num_rows ($unfinishedtasks);
$latteParameters['unfinishedtasks'] = $unfinishedtasks_count;
if ($unfinishedtasks_count > 0 ) {
    while ($unfinishedtasks_list = mysqli_fetch_assoc ($unfinishedtasks)) {
        $tasks[] = array ($unfinishedtasks_list['id'], StripSlashes ($unfinishedtasks_list['task']),  getAuthor($unfinishedtasks_list['created_by'],0));
    }
    $latteParameters['tasks'] = $tasks;
} else {
    $latteParameters['tasksnone'] = $text['zadnenedokonceneukoly'];
}
?>
