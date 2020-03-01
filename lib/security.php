<?php
/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */

 
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
