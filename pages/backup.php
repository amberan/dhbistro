<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

if (isset($URL[2]) && ($user['aclRoot'] > 0) && $URL[2] == 'now') {
    bistroBackupGenerate();
    $latteParameters['message'] = $text['zalohavytvorena'];
}

if (isset($_GET['sort'])) {
    sortingSet('backup', $_GET['sort']);
}
$latteParameters['backup'] = bistroBackupList();
latteDrawTemplate('sparklet');
latteDrawTemplate('backup');
