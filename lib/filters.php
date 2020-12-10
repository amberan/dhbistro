<?php

/** 
 * save user sorting preferencess
 * @param string object - type of object to be sorted 
 * @param string column - name of the column to sort by
 * @param string linkedTable - db name of table where column is located
 */
function sortingSet($object,$column,$linkedTable = null)
{
    global $database,$user;
    $currentSorting = sortingGet($object,$linkedTable);
    if (mb_strpos($currentSorting,$column) AND mb_strpos($currentSorting,'DESC')) {
        mysqli_query($database,"UPDATE ".DB_PREFIX."sort set sortDirection='ASC' where objectType='$object' AND userId=".$user['userId']);
    } elseif (mb_strpos($currentSorting,$column) AND mb_strpos($currentSorting,'ASC')) {
        mysqli_query($database,"UPDATE ".DB_PREFIX."sort set sortDirection='DESC' where objectType='$object' AND userId=".$user['userId']);
    } elseif ((DBcolumnExist($object,$column) OR DBcolumnExist($linkedTable,$column)) AND mb_strlen($currentSorting) > 0 ) {
        mysqli_query($database,"UPDATE ".DB_PREFIX."sort set sortColumn='$column' , sortDirection='ASC' where objectType='$object' AND userId=".$user['userId']);
    } elseif (DBcolumnExist($object,$column) OR DBcolumnExist($linkedTable,$column)) {
        mysqli_query($database,"INSERT INTO ".DB_PREFIX."sort (userId,objectType,sortColumn,sortDirection) VALUES (".$user['userId'].",'$object','$column','ASC')");
    }
}

/**
 * get current preference for sorting output
 * @param string object - type of object to be sorted
 * @param string linkedTable - db name of table where the sorting column is located
 */
function sortingGet($object,$linkedTable = null): string
{
    global $database,$user;
    $result = "";
    $query = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."sort where objectType='$object' AND userId=".$user['userId']);
    mysqli_num_rows($query);
    if (mysqli_num_rows($query) > 0) {
        $sorter = mysqli_fetch_array ($query);
        if (DBcolumnExist($object,$sorter['sortColumn']) OR DBcolumnExist($linkedTable,$sorter['sortColumn'])) {
            $result = " ORDER BY ".$sorter['sortColumn']." ".$sorter['sortDirection'];
        }
    }
   return $result;
}

// funkce na ukladani preference filtru
 
// funkce na nacitani preference filtru

?>
