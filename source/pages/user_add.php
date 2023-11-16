<?php

if (!isset($_POST['heslo']) || (isset($_POST['heslo']) && strlen($_POST['heslo'])) < 1) {
    $userEdit['newPassword'] = randomPassword();
} else {
    $userEdit['newPassword'] = trim(@$_POST['heslo']);
}
if (count(($_POST)) > 1) {
    $userEdit['aclRoot'] = @$_POST['aclRoot'];
    $userEdit['aclUser'] = @$_POST['aclUser'];
    $userEdit['aclBoard'] = @$_POST['aclBoard'];
    $userEdit['aclNews'] = @$_POST['aclNews'];
    $userEdit['aclSecret'] = @$_POST['aclSecret'];
    $userEdit['aclAudit'] = @$_POST['aclAudit'];
    $userEdit['aclGroup'] = @$_POST['aclGroup'];
    $userEdit['aclPerson'] = @$_POST['aclPerson'];
    $userEdit['aclCase'] = @$_POST['aclCase'];
    $userEdit['aclHunt'] = @$_POST['aclHunt'];
    $userEdit['aclGamemaster'] = @$_POST['aclGamemaster'];
    $userEdit['aclReport'] = @$_POST['aclReport'];
    $userEdit['aclSymbol'] = @$_POST['aclSymbol'];
    $userEdit['aclAPI'] = @$_POST['aclAPI'];
    $userEdit['userEmail'] = @$_POST['userEmail'];
    $userEdit['userName'] = trim(@$_POST['login']);
    $userEdit['personId'] = @$_POST['idperson'];

    $latteParameters['userEdit'] = $userEdit;

    if (isset($_POST['edituser']) && strlen($userEdit['userName']) == 0) {
        $latteParameters['message'] = $text['prazdnyuzivatel'] . "\n";
    } else {
        $validateUserNameSql =
        $validateUserName = mysqli_query($database, "SELECT userId FROM " . DB_PREFIX . "user WHERE UCASE(userName)=UCASE('" . $userEdit['userName'] . "')");
        if (mysqli_num_rows($validateUserName) == 1) {
            $latteParameters['message'] .= $text['uzivatelexistuje'] . "\n";
        }
    }
    if (strlen($userEdit['userEmail']) > 0 && !validate_mail($userEdit['userEmail'])) {
        $latteParameters['message'] .= $text['nevalidniemail'] . "\n";
    }

    if (!isset($latteParameters['message']) && $userEdit['userName']) {
        $userCreate = "INSERT INTO " . DB_PREFIX . "user (userName,userPassword) VALUES('" . $userEdit['userName'] . "','" . md5($userEdit['newPassword']) . "')";
        mysqli_query($database, $userCreate);
        $userEdit['userId'] = mysqli_insert_id($database);
        if (is_numeric($userEdit['userId'])) {
            unset($latteParameters['userId']);
            authorizedAccess('user', 'new', $userEdit['userId']);
            userChange($userEdit['userId'], $userEdit);
            $latteParameters['userEdit'] = $userEdit;
            $latteParameters['message'] = $text['uzivatelvytvoren'];
        } else {
            $latteParameters['message'] = $text['nevytvoreno'];
        }
    }
}

$latteParameters['persons'] = personsUnlinked();
$latteParameters['userEdit'] = $userEdit;

latteDrawTemplate('sparklet');
latteDrawTemplate('user_edit');
