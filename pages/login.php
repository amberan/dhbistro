<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

$latteParameters['title'] = 'Přihlášení do systému';
$latte->render($config['folder_templates'].'login.latte', $latteParameters);
