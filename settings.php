<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latteParameters['title'] = 'Nastavení';

$latteParameters['menu'] = $menu;
$latteParameters['menu2'] = $menu2;
$latteParameters['settings_timeout'] = $usrinfo['timeout'];
$latteParameters['settings_plan'] = stripslashes($usrinfo['plan_md']);
$latteParameters['current_location'] = $_SERVER["PHP_SELF"];;

    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'settings.latte', $latteParameters);
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footerMD.latte', $latteParameters);
?>