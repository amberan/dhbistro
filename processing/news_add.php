<?php

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);

$latte->render($config['folder_templates'].'headerMD.latte', $latteParameters);
$latte->render($config['folder_templates'].'menu.latte', $latteParameters);
$latte->render($config['folder_templates'].'news_add.latte', $latteParameters);
