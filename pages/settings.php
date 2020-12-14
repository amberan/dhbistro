<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latteParameters['title'] = $text['nastaveni'];

if ((isset($_POST['userid']) and isset($_POST['edituser']) and !is_numeric($_REQUEST['timeout'])) and ($user['userId'] === $_POST['userid'])) {
    $latteParameters['message'] = $text['timeoutnenicislo'];
} else {
    if (isset($_REQUEST['editsettings']) && ($_REQUEST['timeout'] > 1800 || $_REQUEST['timeout'] < 30)) {
        $latteParameters['message'] = $text['timeoutspatne'];
    } elseif (isset($_REQUEST['editsettings'], $_REQUEST['soucheslo']) && $_REQUEST['soucheslo'] !== '') {
        $currentpwd = userRead($user['userId']);
        if ($currentpwd['userPassword'] === md5($_REQUEST['soucheslo'])) {
            userChange($user['userId'],['userPassword' => md5($_POST['heslo'])],$text['nastaveniulozeno'],$text['akcinelzeprovest']);
        } else {
            $latteParameters['message'] = $text['puvodniheslospatne'];
        }
    } elseif (isset($_REQUEST['editsettings'])) {
        if (mb_strlen($_POST['email']) === 0 or (validate_mail($_POST['email']) === true and mb_strlen($_POST['email']))) {
            $update = [
                'userEmail' => $_POST['email'],
                'planMD' => $_POST['plan'],
                'userTimeout' => $_POST['timeout'],
            ];
            userChange($user['userId'],$update,$text['nastaveniulozeno'],$text['akcinelzeprovest']);
            $latteParameters['user'] = $user = $usrinfo = sessionUser($_SESSION['sid']);
        } else {
            $latteParameters['message'] = $text['neplatnyemail'].mb_strlen($_POST['email']);
        }
    }
}

if ($user['personId'] > 0) {
    $person = personRead($user['personId']);
    $latteParameters['person'] = $person;
}

latteDrawTemplate('sparklet');
latteDrawTemplate('settings');
