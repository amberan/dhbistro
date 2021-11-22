<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

// REPORTS
$reportsAssigned = reportsAssignedTo($user['userId']);
if (sizeof($reportsAssigned) > 1) {
    $latteParameters['reports'] = $reportsAssigned;
} else {
    $latteParameters['reportsnone'] = $text['zadnanedokoncenahlaseni'];
}

// CASES
$casesAssigned = casesAssignedTo($user['userId']);
if (sizeof($casesAssigned)> 1) {
    $latteParameters['cases'] = $casesAssigned;
} else {
    $latteParameters['casesnone'] = $text['zadnenadokoncenepripady'];
}

// TASKS
$tasksAssigned = tasksAssignedTo($user['userId']);
if (sizeof($tasksAssigned) > 1) {
    $latteParameters['tasks'] = $tasksAssigned;
} else {
    $latteParameters['tasksnone'] = $text['zadnenedokonceneukoly'];
}
