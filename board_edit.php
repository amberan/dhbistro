<?php

use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latteParameters['title'] = $text['nastenka'];

// DASHBOARD (nastenka) SAVE
if (isset($_POST['editdashboard'])) {
    auditTrail(6, 2, 0);
    $sql = "INSERT INTO ".DB_PREFIX."dashboard ( created, iduser, content,  content_md) VALUES('".Time()."','".$usrinfo['id']."','','".$_REQUEST['dashboard']."')";
    mysqli_query ($database,$sql);
    unreadRecords (6,0);
    $latteParameters['message'] = $text['nastenkaupravena'];
}

$sql_dashboard = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
if ($dashboard = mysqli_fetch_assoc ($sql_dashboard)) {
    if (isset($dashboard['content'])) {
        $latteParameters['dashboard'] = StripSlashes($dashboard['content_md']);
    } else {
        $latteParameters['dashboard'] = '';
    }
}

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'board_edit.latte', $latteParameters);

?>
