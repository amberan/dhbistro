<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/API/include.php');
use Tracy\Debugger;

Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);
header('Content-Type: application/json');

if (isset($_GET[sessionID])) { # verify user
    $user = session_validation($_GET['sessionID']);
}

if ($user == null) { //invalid user
    http_response_code(401);
    echo json_encode(['error' => $text['notificationHttp401']]);
} else { //valid user
    if (isset($_GET[personID])) {
        $output = personRead($_GET['personID']);
    //TODO prevadet id fotek s symbolu na odkazy
    } else {
        //TODO strankovani
        $output = personList($_GET['where'],$_GET['order']);
        //TODO prevadet id fotek s symbolu na odkazy
    }
    http_response_code(200);
    echo json_encode($output);
}
