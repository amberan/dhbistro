<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

/**
 * GET USER INFO.
 *
 * @param string sid session_id();
 * @param mixed $sid
 *
 * @return array
 *               TODO remove legacy login masking
 */
function sessionUser($sid): array
{
    global $database, $_SESSION;
    $usersql = "SELECT * FROM ".DB_PREFIX."user 
    WHERE userDeleted=0 AND userSuspended=0 AND ".DB_PREFIX."user.sid ='$sid' AND userAgent='".$_SERVER['HTTP_USER_AGENT']."'";
    if ($usrinfo = mysqli_fetch_assoc(mysqli_query($database,$usersql))) {
        //$_SESSION['inactiveallowance'] = $usrinfo['userTimeout'];
        $user['userId'] = $usrinfo['userId'];
        $usrinfo['login'] = $usrinfo['userName'];
        $usrinfo['idperson'] = $usrinfo['personId'];
        $usrinfo['lastaction'] = $usrinfo['lastLogin'];
        $usrinfo['currip'] = $usrinfo['ipv4'];
        $usrinfo['user_agent'] = $usrinfo['userAgent'];
        $usrinfo['email'] = $usrinfo['userEmail'];
        $usrinfo['deleted'] = $usrinfo['userDeleted'];
        $usrinfo['suspended'] = $usrinfo['userSuspended'];
        $usrinfo['zlobody'] = $usrinfo['zlobod'];
        $user['aclDirector'] = $usrinfo['aclDirector'];
        $usrinfo['right_text'] = $usrinfo['aclPerson'];
        $user['aclGamemaster'] = $usrinfo['aclGamemaster'];
        $user['aclAudit'] = $usrinfo['aclAudit'];
        $user['aclRoot'] = $usrinfo['aclRoot'];
        $usrinfo['plan_md'] = $usrinfo['planMD'] = stripslashes($usrinfo['planMD']);

        $usrinfo['sqlDeleted'] = " deleted <= ".$usrinfo['aclRoot'];
        $usrinfo['sqlSecret'] = " secret <= ".$usrinfo['aclSecret'];
    } else {
        unset($_SESSION['sid']);
    }

    return $usrinfo;
}

/**
 * DELETE SID FROM DB.
 *
 * @param string sid
 * @param mixed $sid
 */
function sessionDBwipe($sid): void
{
    global $database;
    mysqli_query($database,"UPDATE ".DB_PREFIX."user set sid=null WHERE sid='$sid'");
}

/**
 * FORCED LOGOUT.
 *
 * @param string msg to display
 * @param mixed $msg
 */
function logout_forced($msg): void
{
    global $_SESSION;
    if (isset($_SESSION['sid'])) {
        sessionDBwipe($_SESSION['sid']);
    }
    session_regenerate_id();
    session_destroy();
    session_start();
    if (isset($msg)) {
        @$_SESSION['message'] .= $msg;
        Debugger::log($_SESSION['message']);
    }
    header('location: '.siteURL());
}

/*
 * PROCESS LOGIN FORM
 */
if (isset($_POST['logmein']) and mb_strlen($_POST['loginname']) and mb_strlen($_POST['loginpwd'])) {
    $logonSql = "SELECT userId FROM ".DB_PREFIX."user WHERE userName='".$_POST['loginname']."' AND userPassword='".md5($_POST['loginpwd'])."' and userDeleted=0 and userSuspended=0";
    $logon = mysqli_query($database,$logonSql);
    if ($logonUser = mysqli_fetch_array($logon)) {
        $_SESSION['sid'] = session_id();
        sessionDBwipe($_SESSION['sid']);
        $logonUpdateSql = "UPDATE ".DB_PREFIX."user SET sid='".$_SESSION['sid']."', lastLogin=".time().", ipv4='".$_SERVER['REMOTE_ADDR']."', userAgent='".$_SERVER['HTTP_USER_AGENT']."' WHERE userId=".$logonUser['userId'];
        mysqli_query($database,$logonUpdateSql);
        Debugger::log("LOGIN SUCCESS: ".$_POST['loginname']);
    } else {
        Debugger::log("LOGIN FAILED: ".$_POST['loginname']);
    }
}

/*
 * GET $user by SESSION['sid']
 * TODO remove legacy $usrinfo
 */
if (isset($_SESSION['sid'])) {
    $latteParameters['user'] = $user = $usrinfo = sessionUser($_SESSION['sid']);
}

/*
 * LOGOUT
 */
if (!isset($_SESSION['sid']) and (in_array($URL[1],$config['page_free'], true) === false)) { //neprihlaseny, zkousi
    logout_forced($text['http401']);
}
if (isset($user) and (@$user['userTimeout'] + @$_SESSION['timestamp'] < time()) and !isset($_POST['logmein'])) { //neprihlasuje se, je prihlaseny, ale vyprsel timeout
    logout_forced($text['nuceneodhlaseni']);
}
if ($URL[1] === 'logout') { //user logout
    logout_forced($text['odhlaseniuspesne']);
}

$_SESSION['timestamp'] = time();
