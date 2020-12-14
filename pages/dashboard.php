<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

// REPORTS
$reportsAssigned = reportsAssignedTo($user['userId']);
if (isset($reportsAssigned[0][0])) {
    $latteParameters['reports'] = $reportsAssigned;
} else {
    $latteParameters['reportsnone'] = $text['zadnanedokoncenahlaseni'];
}

// CASES
$casesAssigned = casesAssignedTo($user['userId']);
if (isset($casesAssigned[0][0])) {
    $latteParameters['cases'] = $casesAssigned;
} else {
    $latteParameters['casesnone'] = $text['zadnenadokoncenepripady'];
}

// TASKS
$tasksAssigned = tasksAssignedTo($user['userId']);
if (isset($tasksAssigned[0][0])) {
    $latteParameters['tasks'] = $tasksAssigned;
} else {
    $latteParameters['tasksnone'] = $text['zadnenedokonceneukoly'];
}
