<?php


function xmp($object) {
	global $config;
	if ($config['debug']) {
		echo "<xmp>\n";
		print_r ($object);
		echo "\n</xmp>\n";
		
	}
}

function debug($object) {
	global $config;
	if ($config['debug']) {
		echo "<pre>\n";
		print_r ($object);
		echo "\n</pre>";
		
	}
}

?>