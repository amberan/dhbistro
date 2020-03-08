<?php

$lines = file($config['dbpass'],FILE_IGNORE_NEW_LINES) or die("fail pwd");
$password = $lines[2];

$database = mysqli_connect ('localhost',$config['dbuser'],$password,$config['dbdatabase']) or die ($_SERVER["SERVER_NAME"].":".mysqli_connect_errno()." ".mysqli_connect_error());

mysqli_query ($database,"SET NAMES 'utf8'");

require_once SERVER_ROOT.'/inc/installer.php';
 
?>
