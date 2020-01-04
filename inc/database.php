<?php

$lines = file($config['dbpass'],FILE_IGNORE_NEW_LINES) or die("fail pwd");;
$password = $lines[2];

$database = mysqli_connect ('localhost',$config['dbuser'],$password,$config['dbdatabase']) or die (mysqli_connect_errno()." ".mysqli_connect_error());

mysqli_query ($database,"SET NAMES 'utf8'");

/**
* SQL injection mitigation
* @param array array 
* @return array escaped/slashed array
*/
function escape_array($array): array
{
    global $database;
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            escape_array($value);
        } else {
            $array[$key] = mysqli_real_escape_string($database, addslashes( $value));
        }
    }

    return $array;
}

 $_REQUEST = escape_array ($_REQUEST);
 $_POST = escape_array ($_POST);
 $_GET = escape_array ($_GET);
 
?>
