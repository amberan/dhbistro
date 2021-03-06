<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

//TODO funkce pro prevod id fotek s symbolu na odkazy

/**
 * one person array.
 *
 * @param string personId
 * @param mixed $personId
 *
 * @return array nw_person row
 */
function personRead($personId): array
{
    global $database, $user, $text;
    $sql = "SELECT * FROM ".DB_PREFIX."person WHERE id = $personId AND ".$user['sqlDeleted']." AND ".$user['sqlDeleted'];
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        $person = mysqli_fetch_assoc($query);
        unset($person['deleted']);
    } else {
        $person[] = $text['zaznamnenalezen'];
    }

    return $person;
}

/**
 * person list array.
 *
 * @param string where where clause for SQL
 * @param string order order by clause for SQL
 * @param mixed $where
 * @param mixed $order
 *
 * @return array nw_person array
 *               TODO strankovani
 */
function personList($where = 1, $order = 1): array
{
    global $database, $user, $text;
    if (mb_strlen($where) < 1) {
        $where = 1;
    }
    if (mb_strlen($order) < 1) {
        $order = 1;
    }
    $sql = "SELECT * FROM ".DB_PREFIX."person WHERE ($where) AND ".$user['sqlDeleted']." AND ".$user['sqlDeleted']." ORDER BY $order";
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        while ($person = mysqli_fetch_assoc($query)) {
            unset($person['deleted']);
            $personList[] = $person;
        }
    } else {
        $personList[] = $text['prazdnyvypis'];
    }

    return $personList;
}
