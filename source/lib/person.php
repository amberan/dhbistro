<?php

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

    $sql = 'SELECT * FROM ' . DB_PREFIX . 'person WHERE id = ' . $personId . ' AND deleted <= ' . $user['aclRoot'] . ' AND secret <= ' . $user['aclSecret'];
    $query = mysqli_query($database, $sql);
    if (mysqli_num_rows($query) > 0) {
        $person = mysqli_fetch_assoc($query);
        unset($person['deleted']);
    } else {
        $person[] = $text['notificationRecordNotFound'];
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
    $sql = 'SELECT * FROM ' . DB_PREFIX . 'person WHERE (' . $where . ') AND deleted <=' . $user['aclRoot'] . ' AND secret <=' . $user['aclSecret'] . ' ORDER BY ' . $order;
    $query = mysqli_query($database, $sql);
    $personList = [];
    if (mysqli_num_rows($query) > 0) {
        while ($person = mysqli_fetch_assoc($query)) {
            unset($person['deleted']);
            $personList[] = $person;
        }
    } else {
        $personList[] = $text['notificationListEmpty'];
    }

    return $personList;
}

/**
 * if unchecked null $field, if checked and db null update to current timestamp.
 *
 * @param int $column id of person
 * @param bool checked/unchecked
 * @param mixed $id
 * @param mixed $checkbox
 */
function personCheckboxUpdate($id, $column, $checkbox = null): void
{
    DBCheckboxUpdate('person', $id, $column, $checkbox);
}

function personDelete($id): void
{
    global $database,$user;
    if ($user['aclPerson'] > 0) {
        authorizedAccess('person', 'delete', $id);
        //TODO deleted to timestamp
        $sqlUpdate = 'update ' . DB_PREFIX . 'person set deleted=1 where id=' . $id;
        mysqli_query($database, $sqlUpdate);
        DebuggerLog('PERSON.'.$id.' DELETED '.$sqlUpdate,"D");
        deleteAllUnread('person', $id);
    } else {
        unauthorizedAccess('person', 'delete', $id);
    }
}

function personRestore($id): void
{
    global $database,$user;
    if ($user['aclRoot'] > 0) {
        authorizedAccess('person', 'restore', $id);
        $sqlUpdate = 'update ' . DB_PREFIX . 'person set deleted=0 where id=' . $id;
        mysqli_query($database, $sqlUpdate);
        DebuggerLog('PERSON.'.$id.' RESTORED '.$sqlUpdate,"D");
        deleteAllUnread('person', $id);
    } else {
        unauthorizedAccess('person', 'restore', $id);
    }
}

//list of unlinked persons for user editing (includes person linked to $editedUser)
function personsUnlinked($editedUser = 0): array
{
    global $database;

    $personLinked = [];
    //list linked persons except person linked to $editedUser
    $personLinkedSql = "SELECT " . DB_PREFIX . "user.personId FROM " . DB_PREFIX . "user where personId != 0 AND userId != " . $editedUser . " ORDER BY personId";
    $personLinkedQuery = mysqli_query($database, $personLinkedSql);
    while ($personLinkedRecord = mysqli_fetch_assoc($personLinkedQuery)) {
        $personLinked[] = $personLinkedRecord['personId'];
    }
    $personList = personList('deleted=0 and  (archived is null OR archived  < from_unixtime(1)) ', 'surname');
    //substract linked from all undeleted
    foreach ($personList as $personList) {
        if (!in_array($personList['id'], $personLinked, true)) {
            $person[] = [$personList['id'], $personList['surname'], $personList['name']];
        }
    }
    return $person;
}
