<?php

// Use in the “Post-Receive URLs” section of your GitHub repo.

if ( $_POST['payload'] ) {
	$output = shell_exec( 'cd '.$_SERVER['DOCUMENT_ROOT'] .' && git reset --hard HEAD && git pull' );
	echo "<pre>$output</pre>";
} else {
	Header ('location: login.php');
}
?>