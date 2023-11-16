<?php

//TODO separate secret and private flag in DB
//TODO function for deleting note with timestamp of deletion and person responsible
//TODO function for editing note

function noteType($noteType = null)
{
    global $text;
    $list = [
        0 => $text['aclSecretLevel'][0],
        1 => $text['aclSecretLevel'][2],
        2 => $text['aclSecretLevel'][1],
    ];
    $return = $list;
    if (isset($noteType) && is_numeric($noteType)) {
        $return = $list[$noteType];
    } elseif (isset($noteType) && is_string($noteType)) {
        $return = array_search($noteType, $list);
    }
    return $return;
}

/**
 * when creating a note with name of object, translates into id.
 * @param  mixed           $objectId
 * @return bool|int|string
 */
function noteObjectId($objectId = null)
{
    $object = [
        1 => 'person',
        2 => 'group',
        3 => 'case',
        4 => 'report',
        5 => 'symbol',
    ];
    $return = $object;
    if (is_numeric($objectId)) {
        $return = $object[$objectId];
    } elseif (is_string($objectId)) {
        $return = array_search($objectId, $object);
    }
    return $return;
}

function noteCreate($object, $objectId, $title, $body, $secret, $userId)
{
    global $database;
    if (!is_numeric($object)) {
        $object = noteObjectId($object);
    }
    $noteCreateSql = "INSERT INTO " . DB_PREFIX . "note (note, title, datum, iduser, idtable, iditem, secret, deleted)
            VALUES('" . $body . "','" . $title . "','" . Time() . "','" . $userId . "','" . $object . "','" . $objectId . "'," . $secret . ",0)";
    mysqli_query($database, $noteCreateSql);
    if (mysqli_affected_rows($database)) {
        $noteId = mysqli_insert_id($database);
    }
    return $noteId;
}
