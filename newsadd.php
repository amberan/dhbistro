<?php
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'news_add.latte', $latteParameters);
