<?php
$latteParameters['title'] = 'Nastavení';

$latteParameters['settings_timeout'] = $usrinfo['timeout'];
$latteParameters['settings_plan'] = stripslashes($usrinfo['plan_md']);

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'settings.latte', $latteParameters);
?>
