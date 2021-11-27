<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

if (isset($URL[2]) && ($user['aclRoot'] > 1) && $URL[2] == 'now') {
    backup_process();
    $latteParameters['message'] = $text['zalohavytvorena'];
}

if (isset($_GET['sort'])) {
    sortingSet('backup', $_GET['sort']);
}
$latteParameters['backup'] = backupListDatabase();
latteDrawTemplate('sparklet');
latteDrawTemplate('backup');
