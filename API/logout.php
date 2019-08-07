<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/API/include.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

if (isset($_GET['sessionID'])) { 
    mysqli_query ($database,"UPDATE ".DB_PREFIX."users set sid='' where sid='".session_id()."'");
    if (mysqli_affected_rows($database) > 0) {
        session_regenerate_id();
        session_destroy();
        Debugger::log("API-LOGOUT SUCCESS: ".$_GET['sessionID']);
        http_response_code(202);
        header('Content-Type: application/json');
        echo json_encode(array( 'message' => $text['odhlaseniuspesne']));
    } 
}
?>