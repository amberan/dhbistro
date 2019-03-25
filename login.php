<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');

$latteParameters['title'] = 'Přihlášení do systému';

	  
		use Tracy\Debugger;
		Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);
		$latte = new Latte\Engine;
		$latte->setTempDirectory($config['folder_cache']);

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'login.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);

?>