<?php

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latteParameters['title'] = $text['nastenka'];

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
