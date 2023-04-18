<?php


//

// REPORTS
$reportsAssigned = reportsAssignedTo($user['userId']);
if (sizeof($reportsAssigned) > 1) {
    $latteParameters['reports'] = $reportsAssigned;
} else {
    $latteParameters['reportsnone'] = $text['zadnanedokoncenahlaseni'];
}

// CASES
$casesAssigned = casesAssignedTo($user['userId']);
if (sizeof($casesAssigned) > 1) {
    $latteParameters['cases'] = $casesAssigned;
} else {
    $latteParameters['casesnone'] = $text['notificationListEmpty'];
}
