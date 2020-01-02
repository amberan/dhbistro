<?php

$latteParameters['title'] = $text['nastenka'];

auditTrail(6, 1, 0);
deleteUnread (6,0);

// BOARD MEMO
$sql_dashboard = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
$dashboard = mysqli_fetch_assoc ($sql_dashboard);
if (isset($dashboard['content'])) {
    $latteParameters['board'] = StripSlashes($dashboard['content_md']);
    $latteParameters['board_created'] = webdate($dashboard['created']);
    $latteParameters['board_author'] = getAuthor($dashboard['iduser'],0);
} else {
    $latteParameters['board'] = $text['nastenkaprazdna'];
}

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'dashboard.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'board.latte', $latteParameters);


?>
