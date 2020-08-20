<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latteParameters['title'] = $text['nastaveni'];


if ((isset($_POST['userid']) AND isset($_POST['edituser']) AND !is_numeric($_REQUEST['timeout'])) AND ($user['userId'] == $_POST['userid'] )) {
    $latteParameters['message'] = $text['timeoutnenicislo'];
} else {
    if (isset($_REQUEST['editsettings']) && ($_REQUEST['timeout'] > 1800 || $_REQUEST['timeout'] < 30)) {
        $latteParameters['message'] = $text['timeoutspatne'];
    } elseif (isset($_REQUEST['editsettings'], $_REQUEST['soucheslo']) && $_REQUEST['soucheslo'] <> '') {
        $currentpwd = mysqli_fetch_assoc (mysqli_query ($database,"SELECT pwd FROM ".DB_PREFIX."user WHERE sid='".$_SESSION['sid']."'"));
        if ($currentpwd['pwd'] == md5($_REQUEST['soucheslo'])) {
            mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET pwd=md5('".$_POST['heslo']."'), plan_md='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
            $latteParameters['message'] = $text['nastaveniulozeno'];
        } else {
            $latteParameters['message'] = $text['puvodniheslospatne'];
        }
    } elseif (isset($_REQUEST['editsettings'])) {
        if (validate_mail($_POST['email']) == true or mb_strlen($_POST['email']) < 1) {
            mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET email='".$_POST['email']."', plan_md='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
            $latteParameters['message'] = $text['nastaveniulozeno'];
            read_user();
        } else {
            $latteParameters['message'] = $text['neplatnyemail'];
        }
    }
}

if ($user['personId'] > 0) {
    $person = personRead($user['personId']);
    $latteParameters['person'] = $person;
}


// $latteParameters['settings_email'] = $usrinfo['email'];
// $latteParameters['settings_timeout'] = $usrinfo['timeout'];
// $latteParameters['settings_plan'] = stripslashes($usrinfo['plan_md']);

latteDrawTemplate('sparklet');
latteDrawTemplate('settings');
?>
