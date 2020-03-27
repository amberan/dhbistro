<?php
use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);


// REPORTS
$reportsAssigned = reportsAssignedTo($user['userId']);
if ($reportsAssigned) { 
    $latteParameters['reports'] = $reportsAssigned;
} else {
    $latteParameters['reportsnone'] = $text['zadnanedokoncenahlaseni'];   
}


// CASES
$casesAssigned = casesAssignedTo($user['userId']);
if ($casesAssigned) { 
    $latteParameters['cases'] = $casesAssigned;
} else {
    $latteParameters['casesnone'] = $text['zadnenadokoncenepripady'];
}



// TASKS
$tasksAssigned = tasksAssignedTo($user['userId']);
if ($tasksAssigned) { 
    $latteParameters['tasks'] = $tasksAssigned;
} else {
    $latteParameters['tasksnone'] = $text['zadnenedokonceneukoly'];
}

?>
