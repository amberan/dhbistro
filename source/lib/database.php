<?php

/**
 * saving checkboxes do db (int and timestamp).
 * @param mixed $table
 * @param mixed $id
 * @param mixed $column
 * @param mixed $checkbox
 */
function DBCheckboxUpdate($table, $id, $column, $checkbox = null): void
{
    global $database;
    //! check if $column is timestamp/int and current value
    //! int == 1 && !checkbox => set null
    //! int != 1 && $checkbox => set 1
    //! timestamp != null && !$checkbox => set null
    //! timestamp == null && $checkbox => set CURRENT_TIMESTAMP
    //! timestamp != null && $checkbox => nothing to do
    $sqlCheckColumn = 'select ' . $column . ' from ' . DB_PREFIX . $table . ' where id=' . $id;
    $sqlCheckColumnValue = mysqli_fetch_assoc(mysqli_query($database, $sqlCheckColumn));
    $sqlCheckColumnType = mysqli_fetch_field(mysqli_query($database, $sqlCheckColumn));

    if ($sqlCheckColumnType->type == 3 && $sqlCheckColumnValue[$column] == '1' && !$checkbox) {
        $sqlUpdate = '0';
    } elseif ($sqlCheckColumnType->type == 3 && $sqlCheckColumnValue[$column] != '1' && $checkbox) {
        $sqlUpdate = '1';
    } elseif ($sqlCheckColumnType->type == 7 && $sqlCheckColumnValue[$column] != null && !$checkbox) {
        $sqlUpdate = 'null';
    } elseif ($sqlCheckColumnType->type == 7 && $sqlCheckColumnValue[$column] == null && $checkbox) {
        $sqlUpdate = 'CURRENT_TIMESTAMP';
    }
    if (isset($sqlUpdate)) {
        mysqli_query($database, 'UPDATE ' . DB_PREFIX . $table . ' set ' . $column . ' = ' . $sqlUpdate . ' where id=' . $id);
    }
}

function DBconnect($configDB)
{
    $database = mysqli_connect($configDB['dbHost'], $configDB['dbUser'], $configDB['dbPassword'], $configDB['dbDatabase'])
    or die($_SERVER["SERVER_NAME"] . ":" . mysqli_connect_errno() . " " . mysqli_connect_error());
    mysqli_query($database, "SET NAMES 'utf8'");
    return $database;
}

function DBTest($configDB)
{
    mysqli_connect($configDB['dbHost'], $configDB['dbUser'], $configDB['dbPassword'], $configDB['dbDatabase']);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        DebuggerLog('DATABASE CONNECTION TEST: '.mysqli_connect_error(), "E");
        return false;
    }
    return true;
}

/**
 * populateDB - if $sqlFile not set search for latest /sql/default*sql.
 */
function restoreDB($sqlFile = null)
{
    global $database,$config;
    echo $sqlFile;

    if (!file_exists($sqlFile)) {
        $sqlDefaultFiles = filterDirectory($_SERVER['DOCUMENT_ROOT'] . "sql", "default");
        $sqlFile = $_SERVER['DOCUMENT_ROOT'] . "sql/" . end($sqlDefaultFiles);
    } else {
        $sqlFile = $config['folder_backup'] . $sqlFile;
    }
    if (file_exists($sqlFile)) {
        $tempLine = '';
        $lines = file($sqlFile);
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }
            $tempLine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                mysqli_query($database, $tempLine) || DebuggerLog("SQL IMPORT: " . $tempLine .":". mysqli_error($database),"E");
                $tempLine = '';
            }
        }
        DebuggerLog("INSTALLER: populating database from ".$sqlFile,"W");
    } else {
        DebuggerLog("INSTALLER: restore file ".$sqlFile." does not exist!","E");
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
    $query = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='" . $configDB['dbDatabase'] . "' AND table_name='" . DB_PREFIX . "$table' and column_name='$column'";
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
    $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='" . $configDB['dbDatabase'] . "' AND table_name='" . DB_PREFIX . "$table'";
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
        $query = "select count(*) from " . $configDB['dbDatabase'] . "." . DB_PREFIX . $table . " where length($column) > 0;";
        $result = mysqli_fetch_array(mysqli_query($database, $query));
    }

    return $result[0];
}

function DBListTables()
{
    global $database;
    $tables = [];
    $sql = "SHOW TABLES";
    $result = mysqli_query($database, $sql);

    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    return $tables;
}
