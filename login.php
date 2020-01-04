<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');

$latteParameters['title'] = 'Přihlášení do systému';

	  
		use Tracy\Debugger;
		Debugger::enable(Debugger::DETECT,$config['folder_logs']);
		$latte = new Latte\Engine();
		$latte->setTempDirectory($config['folder_cache']);

$latte->render($config['folder_templates'].'header.latte', $latteParameters);
$latte->render($config['folder_templates'].'login.latte', $latteParameters);
$latte->render($config['folder_templates'].'footer.latte', $latteParameters);

?>