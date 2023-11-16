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
    //str_replace(' ', '',str_replace('ASC', '',str_replace('DESC', '',str_replace('ORDERBY', '',
    $currentSorting = sortingGet($object, $linkedTable);
    $columnList = explode(',', $column);
    if (mb_strpos($currentSorting, $columnList[0]) and mb_strpos($currentSorting, 'DESC')) { //update to ASC
        $sortingSql = "UPDATE " . DB_PREFIX . "sort set sortDirection='ASC' where objectType='$object' AND userId=" . $user['userId'];
    } elseif (mb_strpos($currentSorting, $columnList[0]) and mb_strpos($currentSorting, 'ASC')) { //update to DESC
        $sortingSql = "UPDATE " . DB_PREFIX . "sort set sortDirection='DESC' where objectType='$object' AND userId=" . $user['userId'];
    } elseif ((DBcolumnExist($object, $columnList[0]) or DBcolumnExist($linkedTable, $columnList[0])) and mb_strlen($currentSorting) > 0) { //change column
        $sortingSql = "UPDATE " . DB_PREFIX . "sort set sortColumn='$column' , sortDirection='ASC' where objectType='$object' AND userId=" . $user['userId'];
    } elseif (DBcolumnExist($object, $columnList[0]) or DBcolumnExist($linkedTable, $columnList[0])) { //insert new
        $sortingSql = "INSERT INTO " . DB_PREFIX . "sort (userId,objectType,sortColumn,sortDirection) VALUES (" . $user['userId'] . ",'$object','$column','ASC')";
    } else {
        $sortingSql = 'SELECT NULL LIMIT 0;';
    }
    mysqli_query($database, $sortingSql);
}

/**
 * get current preference for sorting output for current user.
 *
 * @param mixed      $object      - type of object to be sorted
 * @param mixed|null $linkedTable - db name of table where the sorting column is located
 */
function sortingGet($object, $linkedTable = null): string
{
    global $database,$user;
    $result = "";
    $query = mysqli_query($database, "SELECT * FROM " . DB_PREFIX . "sort where objectType='$object' AND userId=" . $user['userId']);
    if (mysqli_num_rows($query) > 0) {
        $sorter = mysqli_fetch_array($query);
        $columnListDB = explode(',', $sorter['sortColumn']);
        foreach ($columnListDB as $columnDB) {
            if (DBcolumnExist($object, $columnDB) or DBcolumnExist($linkedTable, $columnDB)) {
                $columnList[] = $columnDB;
            }
        }
        $result = " ORDER BY " . implode(' ' . $sorter['sortDirection'] . ', ', $columnList) . " " . $sorter['sortDirection'];
    }
    return $result;
}

/**
 * saves filter preference for "object" in serialized field of "data" for current user.
 *
 * @param mixed $object - type of object to be filtered
 * @param mixed $data   - array of key/value for specific filter
 */
function filterSet($object, $data): void
{
    //TODO overeni ze bylo zapsano do db
    global $database,$user;
    $currentFilter = filterGet($object);
    if (is_array($data) and sizeof($data) > 0) {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    if (@$currentFilter['id'] != 'X') {
        $sql = "UPDATE " . DB_PREFIX . "filter SET filterPreference='" . $data . "' WHERE userId=" . $user['userId'] . " AND objectType='" . $object . "'";
    } else {
        $sql = "INSERT INTO " . DB_PREFIX . "filter (userId,objectType,filterPreference) VALUES (" . $user['userId'] . ",'" . $object . "','" . $data . "')";
    }
    mysqli_query($database, $sql);
}

/**
 * get value for "object" type of filter for current user.
 *
 * @param mixed $object - type of object to be filtered
 */
function filterGet($object)//:array
{
    global $database,$user,$config;
    $sql = "SELECT * FROM ".DB_PREFIX."filter where objectType='$object' AND userId=".$user['userId'];
    DebuggerLog('filterGet: '.$sql,"D");
    $query = mysqli_query($database, $sql);
    if (mysqli_num_rows($query) > 0) {
        $result = mysqli_fetch_array($query);
        $filter = json_decode($result['filterPreference'], true);
    } else {
        $filter = ["id" => 'X'];
    }

    return $filter;
}

function filterSide($side = null)
{
    global $text;
    $list = [
        0 => $text['vse'],
        1 => $text['neznama'],
        2 => $text['svetlo'],
        3 => $text['tma'],
        4 => $text['clovek'],
    ];
    $return = $list;
    if (isset($side) && is_numeric($side)) {
        $return = $list[$side];
    } elseif (isset($side) && is_string($side)) {
        $return = array_search($side, $list);
    }
    return $return;
}

function filterCategory($category = null)
{
    global $text;
    $list = [
        0 => $text['vse'],
        1 => $text['neznama'],
        2 => $text['prvni'],
        3 => $text['druha'],
        4 => $text['treti'],
        5 => $text['ctvrta'],
        6 => $text['pata'],
        7 => $text['sesta'],
        8 => $text['sedma'],
        9 => $text['mimokategorie'],
    ];
    $return = $list;
    if (isset($category) && is_numeric($category)) {
        $return = $list[$category];
    } elseif (isset($category) && is_string($category)) {
        $return = array_search($category, $list);
    }
    return $return;
}

function filterClass($class = null)
{
    global $text;
    $list = [
        0 => $text['vse'],
        1 => $text['neznama'],
        2 => $text['bilymag'],
        3 => $text['cernymag'],
        4 => $text['lecitel'],
        5 => $text['obraten'],
        6 => $text['upir'],
        7 => $text['vlkodlak'],
        8 => $text['vedma'],
        9 => $text['zarikavac'],
        10 => $text['vykladac'],
        11 => $text['jasnovidec'],
    ];
    $return = $list;
    if (isset($class) && is_numeric($class)) {
        $return = $list[$class];
    } elseif (isset($class) && is_string($class)) {
        $return = array_search($class, $list);
    }
    return $return;
}
