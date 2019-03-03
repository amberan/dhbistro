<?php

$lines = file($_SERVER['DOCUMENT_ROOT'].$config['dbpass'],FILE_IGNORE_NEW_LINES) or die("fail pwd");;
$password = $lines[2];

$database = mysqli_connect ('localhost',$config['dbuser'],$password,$config['dbdatabase']) or die (mysqli_connect_errno()." ".mysqli_connect_error());

mysqli_query ($database,"SET NAMES 'utf8'");

//SQL injection  mitigation
function escape_array($array) {
	global $database;
	foreach($array as $key=>$value) {
	   if(is_array($value)) { escape_array($value); }
	   else { $array[$key] = mysqli_real_escape_string($database, addslashes( $value)) ; }
	}
	return $array;
 }

 $_REQUEST = escape_array ($_REQUEST);
 $_POST = escape_array ($_POST);
 $_GET = escape_array ($_GET);
 
?>
