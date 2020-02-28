<?php
use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

$latteParameters['title'] = $text['nastenka'];
auditTrail(6, 1, 0);
deleteUnread (6,0);

// BOARD MEMO SAVE
if (isset($_POST['editdashboard']) AND $usrinfo['right_power'] > 0) {
    auditTrail(6, 2, 0);
    $sql = "INSERT INTO ".DB_PREFIX."dashboard ( created, iduser, content,  content_md) VALUES('".Time()."','".$usrinfo['id']."','','".$_REQUEST['dashboard']."')";
    mysqli_query ($database,$sql);
    unreadRecords (6,0);
    $latteParameters['message'] = $text['nastenkaupravena'];
}


// BOARD MEMO
$sql_dashboard = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
$dashboard = mysqli_fetch_assoc ($sql_dashboard);
if (isset($dashboard['content'])) {
    $latteParameters['board'] = $converter->convertToHtml($dashboard['content_md']);
    $latteParameters['board_created'] = webdate($dashboard['created']);
    $latteParameters['board_author'] = getAuthor($dashboard['iduser'],0);
} else {
    $latteParameters['board'] = $text['nastenkaprazdna'];
}

$latte->render($config['folder_templates'].'sparklet.latte', $latteParameters);
$latte->render($config['folder_templates'].'dashboard.latte', $latteParameters);
$latte->render($config['folder_templates'].'board.latte', $latteParameters);


?>
