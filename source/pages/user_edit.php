<?php

// upravit uzivatele
if (isset($_POST['userid'], $_POST['edituser']) && $user['aclUser'] && !preg_match('/^[[:blank:]]*$/i', $_POST['login'])) {
    authorizedAccess('user', 'edit', $_POST['userid']);
    $usernameConflict = mysqli_query($database, "SELECT userId FROM " . DB_PREFIX . "user WHERE UCASE(userName)=UCASE('" . $_POST['login'] . "') AND userId<>" . $_POST['userid']);
    if (mysqli_num_rows($usernameConflict)) {
        $latteParameters['message'] = "Uživatel již existuje, změňte jeho jméno.";
    } else {
        $data['userName'] = $_POST['login'];
        $data['aclReport'] = $_POST['aclReport'];
        $data['aclPerson'] = $_POST['aclPerson'];
        $data['aclCase'] = $_POST['aclCase'];
        $data['aclGroup'] = $_POST['aclGroup'];
        $data['aclSymbol'] = $_POST['aclSymbol'];
        if ($user['aclHunt'] > 0) {
            $data['aclHunt'] = $_POST['aclHunt'];
        }
        if ($user['aclSecret'] > 0) {
            $data['aclSecret'] = $_POST['aclSecret'];
        }
        if ($user['aclNews'] > 0) {
            $data['aclNews'] = $_POST['aclNews'];
        }
        if ($user['aclBoard'] > 0) {
            $data['aclBoard'] = $_POST['aclBoard'];
        }
        if ($user['aclAudit'] > 0) {
            $data['aclAudit'] = $_POST['aclAudit'];
        }
        if ($user['aclRoot'] > 0 || $user['aclGamemaster'] > 0 || $user['aclUser'] > 0) {
            $data['aclUser'] = $_POST['aclUser'];
        }
        if ($user['aclGamemaster'] > 0 || $user['aclRoot'] > 0) {
            $data['aclGamemaster'] = $_POST['aclGamemaster'];
        }
        if ($user['aclRoot'] > 0) {
            $data['aclRoot'] = $_POST['aclRoot'];
        }
        if ($user['aclRoot'] > 0 || $user['aclAPI'] > 0) {
            $data['aclAPI'] = $_POST['aclAPI'];
        }
        if (validate_mail($_POST['userEmail'])) {
            $data['userEmail'] = $_POST['userEmail'];
        }
        $data['personId'] = $_POST['idperson'];
        userChange($_POST['userid'], $data);
        $latteParameters['message'] = "Uživatel " . $_POST['login'] . " upraven.";
    }
}

$latteParameters['persons'] = personsUnlinked($URL[3]);

$res = mysqli_query($database, "SELECT * FROM " . DB_PREFIX . "user WHERE userId=" . $URL[3]);
if ($rec = mysqli_fetch_assoc($res)) {
    $latteParameters['userEdit'] = $rec;

    $reportsAssignedToUser = reportsAssignedTo($rec['userId']);
    if (sizeof($reportsAssignedToUser) > 1) {
        $latteParameters['userEdit']['hlaseni'] = $reportsAssignedToUser;
    }

    $casesAssignedToUser = casesAssignedTo($rec['userId']);
    if (sizeof($casesAssignedToUser) > 1) {
        $latteParameters['userEdit']['pripady'] = $casesAssignedToUser;
    }
} else {
    $latteParameters['warning'] = $text['notificationRecordNotFound'];
}
latteDrawTemplate('sparklet');
latteDrawTemplate('user_edit');
