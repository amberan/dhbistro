<?php

$lines = file($config['dbpass'],FILE_IGNORE_NEW_LINES) or die("fail pwd");
$password = $lines[2];

$database = mysqli_connect ('localhost',$config['dbuser'],$password,$config['dbdatabase']) or die ($_SERVER["SERVER_NAME"].":".mysqli_connect_errno()." ".mysqli_connect_error());

mysqli_query ($database,"SET NAMES 'utf8'");

require_once SERVER_ROOT.'/inc/installer.php';

/** 
 * Check existence of $column in $table
 */
function DBcolumnExist($table,$column)
{
    global $config,$database;
    $query = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'";
    $checkColumn = mysqli_query($database,$query);

    return mysqli_num_rows($checkColumn);
}
 
/** 
 * Check existence of $table
 */
function DBtableExist($table)
{
    global $config,$database;
    $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table'";
    $checkTable = mysqli_query($database,$query);

    return mysqli_num_rows($checkTable);
}


?>
