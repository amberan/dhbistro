<?php 
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

if (($usrinfo['right_super'] > 1) and $URL[2] == 'now') {
    backup_process();
    $latteParameters['message'] = $text['zalohavytvorena'];
}

$backups_sql="SELECT ".DB_PREFIX."backup.* FROM ".DB_PREFIX."backup ORDER BY id DESC";
$backups_query = mysqli_query ($database,$backups_sql);
while ($backup_record=mysqli_fetch_assoc($backups_query)) { 	

    $backup['file'] = $file = end(explode( "/",$backup_record['file']));
    $backup['datetime'] = webDateTime($backup_record['time']);
    $backup['version'] = $backup_record['version'];
    $backup['filesize'] = human_filesize(filesize($_SERVER['DOCUMENT_ROOT'].$config['folder_backup'].$file))."B";

    $backup_array[] = $backup;
}
$latteParameters['backup'] = $backup_array;
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'backup.latte', $latteParameters);
    
?>
