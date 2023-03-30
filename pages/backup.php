<?php

use Tracy\Debugger;



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
