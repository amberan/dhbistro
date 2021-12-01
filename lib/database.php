<?php

/**
 * testing database connection
 */

function DBTest($dbHost, $dbUser, $dbPassword, $dbDatabase)
{
    $dbtest = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDatabase);
    if (mysqli_connect_errno($dbtest)) {
        return false;
    }
    return true;
}

/**
 * Check existence of $column in $table.
 *
 * @param mixed $table
 * @param mixed $column
 */
function DBcolumnExist($table, $column)
{
    global $configDB,$database;
    $query = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$configDB['dbDatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'";
    $checkColumn = mysqli_query($database, $query);

    return mysqli_num_rows($checkColumn);
}

/**
 * Check existence of $table.
 *
 * @param mixed $table
 */
function DBtableExist($table)
{
    global $configDB,$database;
    $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='".$configDB['dbDatabase']."' AND table_name='".DB_PREFIX."$table'";
    $checkTable = mysqli_query($database, $query);

    return mysqli_num_rows($checkTable);
}

/**
 * Returns number of not empty $table.$column.
 *
 * @param mixed $table
 * @param mixed $column
 */
function DBcolumntNotEmpty($table, $column)
{
    global $configDB,$database;
    $result[] = '1';
    if (DBcolumnExist($table, $column) == true) {
        $query = "select count(*) from ".$configDB['dbDatabase'].".".DB_PREFIX.$table." where length($column) is not null;";
        $result = mysqli_fetch_array(mysqli_query($database, $query));
    }

    return $result[0];
}
