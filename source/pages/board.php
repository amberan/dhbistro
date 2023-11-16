<?php

$latteParameters['title'] = $text['menuDashboard'];
authorizedAccess('news', 'read', 0);
deleteUnread('board', 0);

// BOARD MEMO SAVE
if (isset($_POST['editdashboard']) and $user['aclBoard'] > 0) {
    authorizedAccess('news', 'edit', 0);
    $sql = "INSERT INTO " . DB_PREFIX . "dashboard ( created, iduser, contentMD) VALUES('" . time() . "','" . $user['userId'] . "','" . $_REQUEST['dashboard'] . "')";
    mysqli_query($database, $sql);
    unreadRecords('board', 0);
    $latteParameters['message'] = $text['notificationUpdated'];
}

// BOARD MEMO
$sql_dashboard = mysqli_query($database, "SELECT * FROM " . DB_PREFIX . "dashboard ORDER BY id DESC LIMIT 1");
$dashboard = mysqli_fetch_assoc($sql_dashboard);
if (isset($dashboard['contentMD']) && strlen($dashboard['contentMD']) > 0) {
    $latteParameters['board'] = $dashboard['contentMD'];
    $latteParameters['board_created'] = webdate($dashboard['created']);
    $latteParameters['board_author'] = AuthorDB($dashboard['iduser']);
} else {
    $latteParameters['board'] = $text['notificationListEmpty'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('dashboard');
latteDrawTemplate('board');
