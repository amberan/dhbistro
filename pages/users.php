<?php




if (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'delete') {
    authorizedAccess('user', 'delete', $URL[3]);
    $data['userDeleted'] = 1;
    userChange($URL[3], $data, $text['notificationDeleted'], $text['akcinelzeprovest']);
} elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'restore') {
    authorizedAccess('user', 'restore', $URL[3]);
    $data['userDeleted'] = 0;
    userChange($URL[3], $data, $text['notificationRestored'], $text['akcinelzeprovest']);
} elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'lock') {
    authorizedAccess('user', 'lock', $URL[3]);
    $data['userSuspended'] = 1;
    userChange($URL[3], $data, $text['uzivatelzablokovan'], $text['akcinelzeprovest']);
} elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] == 'unlock') {
    authorizedAccess('user', 'unlock', $URL[3]);
    $data['userSuspended'] = 0;
    userChange($URL[3], $data, $text['uzivatelodblokovan'], $text['akcinelzeprovest']);
} elseif (isset($URL[3]) and is_numeric($URL[3]) and $URL[2] = 'reset') {
    authorizedAccess('user', 'passwordReset', @$URL[3]);
    $passwordNew = randomPassword();
    $data['userPassword'] = md5($passwordNew);
    userChange($URL[3], $data, $text['heslonastaveno'].$passwordNew, $text['akcinelzeprovest']);
}

if (isset($_GET['sort'])) {
    sortingSet('user', $_GET['sort'], 'person');
}

$userList = userList();
if (count($userList) > 0) {
    $latteParameters['user_record'] = $userList;
} else {
    $latteParameters['warning'] = $text['notificationListEmpty'];
}

latteDrawTemplate('sparklet');
//TODO DODELAT FILTROVANI PODLE PRAV
latteDrawTemplate('users');
