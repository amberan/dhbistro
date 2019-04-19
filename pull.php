<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

// Use in the “Post-Receive URLs” section of your GitHub repo.
if ( $_POST['payload'] ) {
	Debugger::log('GIT WEBHOOK: '.$_POST['payload']);
	$output = shell_exec( 'cd '.$_SERVER['DOCUMENT_ROOT'] .' && git reset --hard HEAD && git pull' );
	Debugger::log('GIT PULL: '.$output);
	echo "<pre>$output</pre>";
} else {
	Debugger::log('GIT WEBHOOK (false): '.$_POST);
	Header ('location: login.php');
}
?>