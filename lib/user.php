<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

/** 
* get user details
* @param string userId
* @return array nw_user row
* TODO osetrit ze nevydim prava nizsi nez mam sam
*/
function userRead($userId): array
{
    global $database, $text, $user;

    $querySql = "SELECT * from ".DB_PREFIX."user where ".$user['sqlDeleted']." AND userId=".$userId;

    $query = mysqli_query($database,$querySql);
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
    } else {
        $person[] = $text['zaznamnenalezen'];
    }

    return $person;
}

/** 
* user list array
* @param string where where clause for SQL
* @param string order order by clause for SQL
* @return array nw_user array
* TODO strankovani
* TODO osetrit ze nevydim prava nizsi nez mam sam
*/
function userList($where = 1, $order = 1): array
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
