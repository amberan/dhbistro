<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');

$parameters = [
	'text' => $text, //textove pole ./custom/text-*.php
	'config' => $config, //konfiguracni parametry ./inc/func_main.php
	'title' => 'Přihlášení do systému' //page title
];

	require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
		use Tracy\Debugger;
		Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
		$latte = new Latte\Engine;
		$latte->setTempDirectory($config['folder_cache']);

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $parameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'login.latte', $parameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $parameters);

?>