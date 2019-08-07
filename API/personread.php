<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/API/include.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
header('Content-Type: application/json');

if (isset($_GET[sessionID])) { # verify user
    $usrinfo = session_validation($_GET['sessionID']);
}

if ($usrinfo == null) { //invalid user
    http_response_code(401);
    echo json_encode(array( 'error' => $text['http401']));    
} else { //valid user
    if (isset($_GET[personID])) {
        $output = personRead($_GET['personID']);
    } else {
        $output = personList($_GET['where'],$_GET['order']);
    }
    http_response_code(200);
    echo json_encode($output);
}
?>