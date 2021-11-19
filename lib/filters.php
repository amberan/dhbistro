<?php

/**
 * save user sorting preferencess for current user.
 *
 * @param string object - type of object to be sorted
 * @param string column - name of the column to sort by
 * @param string linkedTable - db name of table where column is located
 * @param mixed      $object
 * @param mixed      $column
 * @param mixed|null $linkedTable
 */
function sortingSet($object, $column, $linkedTable = null): void
{
    global $database,$user;
    $currentSorting = sortingGet($object, $linkedTable);
    //TODO overeni ze bylo zapsano do db
    if (mb_strpos($currentSorting, $column) and mb_strpos($currentSorting, 'DESC')) {
        mysqli_query($database, "UPDATE ".DB_PREFIX."sort set sortDirection='ASC' where objectType='$object' AND userId=".$user['userId']);
    } elseif (mb_strpos($currentSorting, $column) and mb_strpos($currentSorting, 'ASC')) {
        mysqli_query($database, "UPDATE ".DB_PREFIX."sort set sortDirection='DESC' where objectType='$object' AND userId=".$user['userId']);
    } elseif ((DBcolumnExist($object, $column) or DBcolumnExist($linkedTable, $column)) and mb_strlen($currentSorting) > 0) {
        mysqli_query($database, "UPDATE ".DB_PREFIX."sort set sortColumn='$column' , sortDirection='ASC' where objectType='$object' AND userId=".$user['userId']);
    } elseif (DBcolumnExist($object, $column) or DBcolumnExist($linkedTable, $column)) {
        mysqli_query($database, "INSERT INTO ".DB_PREFIX."sort (userId,objectType,sortColumn,sortDirection) VALUES (".$user['userId'].",'$object','$column','ASC')");
    }
}

/**
 * get current preference for sorting output for current user.
 *
 * @param string object - type of object to be sorted
 * @param string linkedTable - db name of table where the sorting column is located
 * @param mixed      $object
 * @param mixed|null $linkedTable
 */
function sortingGet($object, $linkedTable = null): string
{
    global $database,$user;
    $result = "";
    $query = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."sort where objectType='$object' AND userId=".$user['userId']);
    if (mysqli_num_rows($query) > 0) {
        $sorter = mysqli_fetch_array($query);
        if (DBcolumnExist($object, $sorter['sortColumn']) or DBcolumnExist($linkedTable, $sorter['sortColumn'])) {
            $result = " ORDER BY ".$sorter['sortColumn']." ".$sorter['sortDirection'];
        }
    }

    return $result;
}

/**
 * saves filter preference for "object" in serialized field of "data" for current user.
 *
 * @param string object - type of object to be filtered
 * @param array data - array of key/value for specific filter
 * @param mixed $object
 * @param mixed $data
 */
function filterSet($object, $data): void
{
    //TODO overeni ze bylo zapsano do db
    global $database,$user;
    $currentFilter = filterGet($object);
    if (is_array($data) and sizeof($data) > 0) {
        $data = json_encode($data);
    }
    if (@$currentFilter['id'] != 'X') {
        $sql = "UPDATE ".DB_PREFIX."filter SET filterPreference='".$data."' WHERE userId=".$user['userId']." AND objectType='".$object."'";
    } else {
        $sql = "INSERT INTO ".DB_PREFIX."filter (userId,objectType,filterPreference) VALUES (".$user['userId'].",'".$object."','".$data."')";
    }
    mysqli_query($database, $sql);
}

/**
 * get value for "object" type of filter for current user.
 *
 * @param string object - type of object to be filtered
 * @param mixed $object
 */
function filterGet($object)//:array
{
    global $database,$user;
    $sql = "SELECT * FROM ".DB_PREFIX."filter where objectType='$object' AND userId=".$user['userId'];
    $query = mysqli_query($database, $sql);
    if (mysqli_num_rows($query) > 0) {
        $result = mysqli_fetch_array($query);
        $filter = json_decode($result['filterPreference'], true);
    } else {
        $filter = ["id" => 'X'];
    }

    return $filter;
}
