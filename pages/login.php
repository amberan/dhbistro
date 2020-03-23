<?php

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);

if (isset($_SESSION['message'])) {
    $latteParameters['message'] = $_SESSION['message'];
    unset($_SESSION['message']);
}

$latteParameters['title'] = 'Přihlášení do systému';
$latte->render($config['folder_templates'].'login.latte', $latteParameters);
