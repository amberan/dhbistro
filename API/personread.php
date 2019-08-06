<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/API/include.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);


header('Content-Type: application/json');

if (isset($_GET[sessionID])) {
    $usrinfo = session_validation($_GET['sessionID']);
    if (isset($_GET[personID])) {
        #process arguments
        $output = personRead($_GET['personID']);
    } else {
        #process arguments
        $output = personList($_GET['where'],$_GET['order']);
    }
    http_response_code(200);
    echo json_encode($output);

} else {
    http_response_code(401);
    echo json_encode(array( 'error' => 'You are unauthorized to make this request.'));    
}
?>