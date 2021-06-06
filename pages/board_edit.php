<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

$latteParameters['title'] = $text['nastenka'];

$sql_dashboard = mysqli_query($database,"SELECT * FROM ".DB_PREFIX."dashboard ORDER BY id DESC LIMIT 1");
if ($dashboard = mysqli_fetch_assoc($sql_dashboard)) {
    if (isset($dashboard['content'])) {
        $latteParameters['dashboard'] = stripslashes($dashboard['contentMD']);
    } else {
        $latteParameters['dashboard'] = '';
    }
}
latteDrawTemplate('sparklet');
latteDrawTemplate('board_edit');
