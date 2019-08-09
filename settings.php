<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);

$latteParameters['title'] = 'Nastavení';
$latteParameters['settings_timeout'] = $usrinfo['timeout'];
$latteParameters['settings_plan'] = stripslashes($usrinfo['plan_md']);



    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);


mainMenu (6);
sparklets ('<strong>nastavení</strong>');


    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'settings.latte', $latteParameters);
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footerMD.latte', $latteParameters);
?>