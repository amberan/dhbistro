<?php

//TODO funkce pro prevod id fotek s symbolu na odkazy

/** 
* one person array
* @param string personId
* @return array nw_person row
*/
function personRead($personId)
{
    global $database, $usrinfo, $text;
    $sqlwhere = " id = $personId AND secret <=".$usrinfo['right_power'];
    if (isset($usrinfo['right_admin']) AND $usrinfo['right_admin'] > 0) {
        $sqlwhere .= " AND deleted = 1";
    } else {
        $sqlwhere .= " AND deleted = 0";
    }
    $sql = "SELECT * FROM ".DB_PREFIX."person WHERE $sqlwhere";
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        $person = mysqli_fetch_assoc($query);
        unset ($person['deleted']);
    } else {
        $person[] = $text['zaznamnenalezen'];
    }

    return $person;
}

/** 
* person list array
* @param string where where clause for SQL
* @param string order order by clause for SQL
* @return array nw_person array
* TODO strankovani
*/
function personList($where = 1, $order = 1)
{
    global $database, $usrinfo, $text;
    if (mb_strlen($where) < 1) {
        $where = 1;
    }
    if (mb_strlen($order) < 1) {
        $order = 1;
    }
    $sqlwhere = " ($where) AND secret <=".$usrinfo['right_power'];
    if (isset($usrinfo['right_admin']) AND $usrinfo['right_admin'] > 0) {
        $sqlwhere .= " AND deleted = 1";
    } else {
        $sqlwhere .= " AND deleted = 0";
    }
    $sql = "SELECT * FROM ".DB_PREFIX."person WHERE $sqlwhere ORDER BY $order";
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        while ($person = mysqli_fetch_assoc ($query)) {
            unset ($person['deleted']);
            $personList[] = $person;
        }
    } else {
        $personList[] = $text['prazdnyvypis'];
    }

    return $personList;
}

?>
