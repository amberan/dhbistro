<?php

/**
 * Retrieves user information based on the session ID.
 *
 * @param  string     $sid the session ID
 * @return array|null the user information as an associative array if found, or null if not found
 */
function sessionUser($sid)
{
    global $database, $_SESSION;
    $usersql = "SELECT * FROM " . DB_PREFIX . "user
    WHERE userDeleted=0 AND userSuspended=0 AND " . DB_PREFIX . "user.sid ='$sid' AND userAgent='" . $_SERVER['HTTP_USER_AGENT'] . "'";
    if ($user = mysqli_fetch_assoc(mysqli_query($database, $usersql))) {
        $user['planMD'] = stripslashes($user['planMD'] . ' ');
    } else {
        unset($_SESSION['sid']);
    }
    if (isset($user)) {
        return $user;
    } else {
        return null;
    }
}

/**
 * Deletes the session ID from the database.
 *
 * @param string $sid the session ID to delete
 */
function sessionDBwipe($sid): void
{
    global $database;
    mysqli_query($database, "UPDATE " . DB_PREFIX . "user set sid=null WHERE sid='$sid'");
}

/**
 * Forces the user to log out and displays a message.
 *
 * @param string $msg the message to display
 */
function logout_forced($msg): void
{
    global $_SESSION,$URL;
    if (isset($_SESSION['sid'])) {
        sessionDBwipe($_SESSION['sid']);
    }
    session_regenerate_id();
    session_destroy();
    session_start();
    if (strlen($msg) > 0) {
        @$_SESSION['message'] .= $msg;
    }
    header('location: /');
}

/*
 * PROCESS LOGIN FORM
 */
if (isset($_POST['logmein']) and mb_strlen($_POST['loginname']) and mb_strlen($_POST['loginpwd'])) {
    $logonSql = "SELECT userId FROM " . DB_PREFIX . "user WHERE userName='" . trim($_POST['loginname']) . "' AND userPassword='" . md5(trim($_POST['loginpwd'])) . "' and userDeleted=0 and userSuspended=0";
    $logon = mysqli_query($database, $logonSql);
    if (mysqli_num_rows($logon) && $logonUser = mysqli_fetch_array($logon)) {
        $_SESSION['sid'] = session_id();
        sessionDBwipe($_SESSION['sid']);
        $logonUpdateSql = "UPDATE " . DB_PREFIX . "user SET sid='" . $_SESSION['sid'] . "', lastLogin=" . time() . ", ipv4='" . $_SERVER['REMOTE_ADDR'] . "', userAgent='" . $_SERVER['HTTP_USER_AGENT'] . "' WHERE userId=" . $logonUser['userId'];
        mysqli_query($database, $logonUpdateSql);
    } else {
        DebuggerLog("LOGIN FAILED: " . $_POST['loginname'], "N");
    }
}

/*
 * GET $user by SESSION['sid']
 */
if (isset($_SESSION['sid'])) {
    $latteParameters['user'] = $user = sessionUser($_SESSION['sid']);
}

/*
 * LOGOUT
 */
if (!isset($_SESSION['sid']) and (in_array($URL[1], $config['page_free'], true) == false)) { //neprihlaseny, zkousi, legacy only
    logout_forced($text['notificationHttp401']);
}
if (isset($user) && (intval(@$user['userTimeout']) + intval(@$_SESSION['timestamp']) < time()) && !isset($_POST['logmein'])) { //neprihlasuje se, je prihlaseny, ale vyprsel timeout
    logout_forced($text['nuceneodhlaseni']);
}
if ($URL[1] == 'logout') { //user logout
    logout_forced($text['odhlaseniuspesne']);
}

$_SESSION['timestamp'] = time();
