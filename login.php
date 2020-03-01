<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);

$latte->render($config['folder_templates'].'headerMD.latte', $latteParameters);

$latteParameters['title'] = 'Přihlášení do systému';
$latte->render($config['folder_templates'].'login.latte', $latteParameters);

$latte->render($config['folder_templates'].'footerMD.latte', $latteParameters);
?>