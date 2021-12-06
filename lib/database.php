<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);


function DBconnect($configDB)
{
    $database = mysqli_connect($configDB['dbHost'], $configDB['dbUser'], $configDB['dbPassword'], $configDB['dbDatabase'])
    or die($_SERVER["SERVER_NAME"].":".mysqli_connect_errno()." ".mysqli_connect_error());
    mysqli_query($database, "SET NAMES 'utf8'");
    return $database;
}


function DBTest($configDB)
{
    $dbtest = @mysqli_connect($configDB['dbHost'], $configDB['dbUser'], $configDB['dbPassword'], $configDB['dbDatabase']);
    if (mysqli_connect_errno($dbtest)) {
        return false;
    }
    return true;
}

/**
 * populateDB - if $sqlFile not set search for latest /sql/default*sql
 */
function restoreDB($sqlFile = null)
{
    global $database;
    require $_SERVER['DOCUMENT_ROOT']."/.env.php";
    if (!file_exists($sqlFile)) {
        $dbScriptFileList = glob(SERVER_ROOT.'/sql/default*.sql');
        $sqlFile = end($dbScriptFileList);
        Debugger::log("DEBUG: creating new database from ".$sqlFile);
    }
    if (file_exists($sqlFile)) {
        Debugger::log("DEBUG: Database EMPTY, creating new from ".$sqlFile);
        $tempLine = '';
        $lines = file($sqlFile);
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }
            $tempLine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                mysqli_query($database, $tempLine) || Debugger::log("ERROR SQL IMPORT: " . $tempLine .":". mysqli_error($database));
                $tempLine = '';
            }
        }

        mysqli_close($database);
    } else {
        Debugger::log("ERROR: DB restore file ".$sqlFile." does not exist!");
    }
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
    if (DBcolumnExist($table, $column)) {
        $query = "select count(*) from ".$configDB['dbDatabase'].".".DB_PREFIX.$table." where length($column) is not null;";
        $result = mysqli_fetch_array(mysqli_query($database, $query));
    }

    return $result[0];
}

function DBListTables()
{
    global $database;
    $tables = array();
    $sql = "SHOW TABLES";
    $result = mysqli_query($database, $sql);

    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    return $tables;
}
