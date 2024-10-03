<?php

require_once(SERVER_ROOT.'API/include.php');


// TODO failsafe count number of failed login attempts in a period of time and if higher, deny servise ? use audit table

if (isset($_GET['username']) and isset($_GET['password'])) {
    // TODO pridat kontrolu na pravo API
    $usersql = mysqli_query($database, "SELECT * FROM " . DB_PREFIX . "user WHERE login='" . $_GET['username'] . "' AND pwd=md5('" . $_GET['password'] . "') and deleted=0 and suspended=0 ");
    if ($userresult = mysqli_fetch_array($usersql)) {
        session_regenerate_id();
        session_destroy();
        session_start();
        mysqli_query($database, "UPDATE " . DB_PREFIX . "user SET sid='" . session_id() . "', lastlogon=" . Time() . ", ip='" . $_SERVER['REMOTE_ADDR'] . "', user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "' WHERE id=" . $userresult['id']);
        http_response_code(202);
        header('Content-Type: application/json');
        echo json_encode(
            [
                'sessionID' => session_id(),
                'TTL' => time() + $userresult['timeout'],
            ]
        );
    } else {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => $text['notificationHttp401']]);
    }
}
