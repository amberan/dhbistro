<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

// smazat uzivatele
if (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'delete') {
    if (!$user['aclUser']) {
        unauthorizedAccess(8, 1, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        $data['userDeleted'] = 1;
        userChange($URL[3], $data, $text['uzivatelodstranen'], $text['akcinelzeprovest']);
    }
} // obnovit uzivatele
elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'restore') {
    if (!$user['aclUser']) {
        unauthorizedAccess(8, 1, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        $data['userDeleted'] = 0;
        userChange($URL[3], $data, $text['uzivatelobnoven'], $text['akcinelzeprovest']);
    }
} // zamknout uzivatele
elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'lock') {
    if (!$user['aclUser']) {
        unauthorizedAccess(8, 2, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        $data['userSuspended'] = 1;
        userChange($URL[3], $data, $text['uzivatelzablokovan'], $text['akcinelzeprovest']);
    }
} // odemknout uzivatele
elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'unlock') {
    if (!$user['aclUser']) {
        unauthorizedAccess(8, 2, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        $data['userSuspended'] = 0;
        userChange($URL[3], $data, $text['uzivatelodblokovan'], $text['akcinelzeprovest']);
    }
} // reset hesla uzivatele
elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] = 'reset') {
    if (!$user['aclUser']) {
        unauthorizedAccess(8, 11, 0, 0);
    } else {
        auditTrail(8, 11, @$URL[3]);
        $passwordNew = randomPassword();
        $data['userPassword'] = md5($passwordNew);
        userChange($URL[3], $data, $text['heslonastaveno'].$passwordNew, $text['akcinelzeprovest']);
    }
}  // vytvorit uzivatele
elseif (isset($_POST['insertuser']) && $user['aclUser'] && !preg_match('/^[[:blank:]]*$/i', $_POST['login']) && !preg_match('/^[[:blank:]]*$/i', $_POST['heslo'])) {
    $userExist = mysqli_query($database, "SELECT userId FROM ".DB_PREFIX."user WHERE UCASE(userName)=UCASE('".$_POST['login']."')");
    if (mysqli_num_rows($userExist)) {
        $latteParameters['message'] = $text['uzivatelexistuje'];
    } else {
        //TODO add validate_email
        auditTrail(8, 3, $uidarray['id']);
        $userCreate = "INSERT INTO ".DB_PREFIX."user (userName,userPassword) VALUES('".$_POST['login']."','".md5($_POST['heslo'])."')";
        mysqli_query($database, $userCreate);
        if (mysqli_affected_rows($database) > 0) {
            $uidarray = mysqli_fetch_assoc(mysqli_query($database, "SELECT userId FROM ".DB_PREFIX."user WHERE UCASE(userName)=UCASE('".$_POST['login']."')"));
            $data['aclRoot'] = $_POST['aclRoot'];
            $data['aclUser'] = $_POST['aclUser'];
            $data['aclBoard'] = $_POST['aclBoard'];
            $data['aclNews'] = $_POST['aclNews'];
            $data['aclTask'] = $_POST['aclTask'];
            $data['aclSecret'] = $_POST['aclSecret'];
            $data['aclAudit'] = $_POST['aclAudit'];
            $data['aclGroup'] = $_POST['aclGroup'];
            $data['aclPerson'] = $_POST['aclPerson'];
            $data['aclCase'] = $_POST['aclCase'];
            $data['aclHunt'] = $_POST['aclHunt'];
            $data['aclGamemaster'] = $_POST['aclGamemaster'];
            $data['aclReport'] = $_POST['aclReport'];
            $data['aclSymbol'] = $_POST['aclSymbol'];
            $data['aclAPI'] = $_POST['aclAPI'];
            if (validate_mail($_POST['email'])) {
                $data['userEmail'] = $_POST['email'];
            }
            $data['personId'] = $_POST['idperson'];
            userChange($uidarray['userId'], $data);
            $latteParameters['message'] = $text['uzivatelvytvoren'].$_POST['login'];
        } else {
            $latteParameters['message'] = $text['nevytvoreno'];
        }
    }
}

if (isset($_GET['sort'])) {
    sortingSet('user', $_GET['sort'], 'person');
}

$userList = userList();
if (count($userList) > 0) {
    $latteParameters['user_record'] = $userList;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

latteDrawTemplate('sparklet');
//TODO DODELAT FILTROVANI PODLE PRAV
latteDrawTemplate('users');
