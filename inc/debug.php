<?php

function debug($object,$class = null) {
	global $config;
	if ($config['debug']) {
		echo "<pre class=\"$class\">\n";
		print_r ($object);
		echo "\n</pre>";
		
	}
}

?>