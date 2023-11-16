<?php

$latteParameters['title'] = $text['menuDashboard'];

$sql_dashboard = mysqli_query($database, "SELECT * FROM " . DB_PREFIX . "dashboard ORDER BY id DESC LIMIT 1");
$latteParameters['dashboard'] = ' ';
if ($dashboard = mysqli_fetch_assoc($sql_dashboard)) {
    if (isset($dashboard['contentMD'])) {
        $latteParameters['dashboard'] = stripslashes($dashboard['contentMD']);
    }
}

latteDrawTemplate('sparklet');
latteDrawTemplate('board_edit');
