<?php

$lines = file($config['dbpass'],FILE_IGNORE_NEW_LINES) or die("fail pwd");
$password = $lines[2];

$database = mysqli_connect('localhost',$config['dbuser'],$password,$config['dbdatabase']) or die($_SERVER["SERVER_NAME"].":".mysqli_connect_errno()." ".mysqli_connect_error());

mysqli_query($database,"SET NAMES 'utf8'");

require_once SERVER_ROOT.'/inc/installer.php';

/**
 * Check existence of $column in $table.
 *
 * @param mixed $table
 * @param mixed $column
 */
function DBcolumnExist($table,$column)
{
    global $config,$database;
    $query = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'";
    $checkColumn = mysqli_query($database,$query);

    return mysqli_num_rows($checkColumn);
}

/**
 * Check existence of $table.
 *
 * @param mixed $table
 */
function DBtableExist($table)
{
    global $config,$database;
    $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'";
    $checkTable = mysqli_query($database,$query);

    return mysqli_num_rows($checkTable);
}

/**
 * Returns number of not empty $table.$column.
 *
 * @param mixed $table
 * @param mixed $column
 */
function DBcolumntNotEmpty($table,$column)
{
    global $config,$database;
    $result[] = '1';
    if (DBcolumnExist($table,$column) === true) {
        $query = "select count(*) from ".$config['dbdatabase'].".".DB_PREFIX.$table." where length($column) is not null;";
        $result = mysqli_fetch_array(mysqli_query($database,$query));
    }

    return $result[0];
}

/**
 * SQL injection mitigation.
 *
 * @param array array
 * @param mixed $array
 *
 * @return array escaped/slashed array
 */
function escape_array($array): array
{
    global $database;
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            escape_array($value);
        } else {
            $array[$key] = mysqli_real_escape_string($database, addslashes($value));
        }
    }

    return $array;
}
$_REQUEST = escape_array($_REQUEST);
$_POST = escape_array($_POST);
$_GET = escape_array($_GET);
