<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

function human_filesize($bytes, $decimals = 2)
{
    $size = 'BKMGTP';
    $factor = floor((mb_strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

if (isset($URL[2]) and ($user['aclRoot'] > 1) and $URL[2] === 'now') {
    backup_process();
    $latteParameters['message'] = $text['zalohavytvorena'];
}

if (isset($_GET['sort'])) {
    sortingSet('backup',$_GET['sort']);
}

$backups_sql = "SELECT ".DB_PREFIX."backup.* FROM ".DB_PREFIX."backup ".sortingGet('backup');
$backups_query = mysqli_query($database,$backups_sql);
while ($backup_record = mysqli_fetch_assoc($backups_query)) {
    unset($backup);
    $explode = explode("/",$backup_record['file']);
    $backup['file'] = $file = end($explode);
    $backup['datetime'] = webDateTime($backup_record['time']);
    $backup['version'] = $backup_record['version'];
    if (file_exists($config['folder_backup'].$file)) {
        $backup['filesize'] = human_filesize(filesize($config['folder_backup'].$file))."B";
    } else {
        $backup['filesize'] = $text['soubornenalezen'];
    }
    $backup_array[] = $backup;
}
$latteParameters['backup'] = $backup_array;
latteDrawTemplate('sparklet');
latteDrawTemplate('backup');
