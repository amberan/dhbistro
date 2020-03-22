<?php

// funkce na ukladani preference trideni
function sortingSet($object,$column,$linkedTable = null)
{
    global $database,$usrinfo,$config;
    $currentSorting = sortingGet($object,$linkedTable);
    if (mb_strpos($currentSorting,$column) AND mb_strpos($currentSorting,'DESC')) {
        mysqli_query($database,"UPDATE ".DB_PREFIX."sort set sortDirection='ASC' where objectType='$object' AND userId=".$usrinfo['id']);
    } elseif (mb_strpos($currentSorting,$column) AND mb_strpos($currentSorting,'ASC')) {
        mysqli_query($database,"UPDATE ".DB_PREFIX."sort set sortDirection='DESC' where objectType='$object' AND userId=".$usrinfo['id']);
    } elseif ((DBcolumnExist($object,$column) OR DBcolumnExist($linkedTable,$column)) AND mb_strlen($currentSorting) > 0 ) {
        mysqli_query($database,"UPDATE ".DB_PREFIX."sort set sortColumn='$column' , sortDirection='ASC' where objectType='$object' AND userId=".$usrinfo['id']);
    } elseif (DBcolumnExist($object,$column) OR DBcolumnExist($linkedTable,$column)) {
        mysqli_query($database,"INSERT INTO ".DB_PREFIX."sort (userId,objectType,sortColumn,sortDirection) VALUES (".$usrinfo['id'].",'$object','$column','ASC')");
    }
}


// funkce na nacitani preference trideni
function sortingGet($object,$linkedTable = null): string
{
    global $database,$usrinfo,$config;
    $query = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."sort where objectType='$object' AND userId=".$usrinfo['id']);
    $result = "";
    $sorter = mysqli_fetch_array ($query);
    if (DBcolumnExist($object,$sorter['sortColumn']) OR DBcolumnExist($linkedTable,$sorter['sortColumn'])) {
        $result = "ORDER BY ".$sorter['sortColumn']." ".$sorter['sortDirection'];
    }

    return $result;
}

// funkce na ukladani preference filtru
 
// funkce na nacitani preference filtru

?>