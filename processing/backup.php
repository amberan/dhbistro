<?php

$latte->render($config['folder_templates'].'headerMD.latte', $latteParameters);
$latte->render($config['folder_templates'].'menu.latte', $latteParameters);

function human_filesize($bytes, $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = floor((mb_strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

if (isset($URL[2]) AND ($usrinfo['right_super'] > 1) AND $URL[2] == 'now') {
    backup_process();
    $latteParameters['message'] = $text['zalohavytvorena'];
}

$backups_sql = "SELECT ".DB_PREFIX."backup.* FROM ".DB_PREFIX."backup ORDER BY id DESC";
$backups_query = mysqli_query ($database,$backups_sql);
while ($backup_record = mysqli_fetch_assoc($backups_query)) {
    unset ($backup);
    $explode = explode( "/",$backup_record['file']);
    $backup['file'] = $file = end($explode);
    $backup['datetime'] = webDateTime($backup_record['time']);
    $backup['version'] = $backup_record['version'];
    if (file_exists($config['folder_backup'].$file)) {
        $backup['filesize'] = human_filesize(filesize($config['folder_backup'].$file))."B";
    }
    $backup_array[] = $backup;
}
$latteParameters['backup'] = $backup_array;
$latte->render($config['folder_templates'].'backup.latte', $latteParameters);
    
?>
