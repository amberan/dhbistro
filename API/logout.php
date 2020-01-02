<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/API/include.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
header('Content-Type: application/json');

if (isset($_GET[sessionID])) { # verify user
    $usrinfo = session_validation($_GET['sessionID']);
}

if ($usrinfo == null) { //invalid user
    http_response_code(401);
    echo json_encode(array( 'error' => $text['http401']));
} else { //valid user
    mysqli_query ($database,"UPDATE ".DB_PREFIX."user set sid='' where sid='".$_GET['sessionID']."'");
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

