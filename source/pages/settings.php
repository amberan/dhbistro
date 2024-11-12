<?php

$latteParameters['title'] = $text['menuSettings'];

if ((isset($_POST['userid']) and isset($_POST['edituser']) and !is_numeric($_REQUEST['timeout'])) and ($user['userId'] == $_POST['userid'])) {
    $latteParameters['message'] = $text['timeoutnenicislo'];
} else {
    if (isset($_REQUEST['editsettings']) && ($_REQUEST['timeout'] > $config['session_lenght'][1] || $_REQUEST['timeout'] < $config['session_lenght'][0])) {
        $latteParameters['message'] = $text['timeoutspatne'];
    } elseif (isset($_REQUEST['editsettings'], $_REQUEST['soucheslo']) && $_REQUEST['soucheslo'] != '') {
        $currentpwd = userRead($user['userId']);
        if ($currentpwd['userPassword'] == md5($_REQUEST['soucheslo'])) {
            if (strlen(trim($_POST['heslo'])) > 0) {
                userChange($user['userId'], ['userPassword' => md5($_POST['heslo'])], $text['nastaveniulozeno'], $text['akcinelzeprovest']);
            } else {
                $latteParameters['message'] = $text['newPasswordEmpty'];
            }
        } else {
            $latteParameters['message'] = $text['puvodniheslospatne'];
        }
    } elseif (isset($_REQUEST['editsettings'])) {
        if ((validate_mail($_POST['email']) == true && mb_strlen($_POST['email'])) || mb_strlen($_POST['email']) == 0) {
            $update = [
                'userEmail' => $_POST['email'],
                'planMD' => $_POST['plan'],
                'userTimeout' => $_POST['timeout'],
            ];
            userChange($user['userId'], $update, $text['nastaveniulozeno'], $text['akcinelzeprovest']);
            $latteParameters['user'] = $user = sessionUser($_SESSION['sid']);
        } else {
            $latteParameters['message'] = $text['neplatnyemail'] . mb_strlen($_POST['email']);
        }
    }
}

if ($user['personId'] > 0) {
    $person = personRead($user['personId']);
    $latteParameters['person'] = $person;
}

latteDrawTemplate('sparklet');
latteDrawTemplate('settings');
