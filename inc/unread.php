<?php

if (isset($_REQUEST['delallnew']) || (isset($URL[1]) && $URL[1] == 'readall') || (isset($URL[2]) && $URL[2] == 'readall')) {
    if (unreadTableIdReadall($URL[1])) {
        $sqlReadAll = "DELETE FROM " . DB_PREFIX . "unread WHERE iduser = " . $user['userId'] . " AND idtable=" . unreadTableIdReadall($URL[1]);
    } else {
        $sqlReadAll = "DELETE FROM " . DB_PREFIX . "unread WHERE iduser = " . $user['userId'];
    }
    mysqli_query($database, $sqlReadAll);
    $_SESSION['message'] = "Označeno jako přečtené";
}

function unreadTableIdReadall($objectId)
{
    $object = [
        1 => 'persons',
        2 => 'groups',
        3 => 'cases',
        4 => 'reports',
        7 => 'symbols',
    ];
    if (isset($objectId) && is_numeric($objectId)) {
        $return = $object[$objectId];
    } elseif (isset($objectId) && is_string($objectId)) {
        $return = array_search($objectId, $object);
    } else {
        $return = $object;
    }
    return $return;
}

function unreadTableId($objectId)
{
    $object = [
        1 => 'person',
        2 => 'group',
        3 => 'case',
        4 => 'report',
        5 => 'news',
        6 => 'board',
        7 => 'symbol',
    ];
    if (isset($objectId) && is_numeric($objectId)) {
        $return = $object[$objectId];
    } elseif (isset($objectId) && is_string($objectId)) {
        $return = array_search($objectId, $object);
    } else {
        $return = $object;
    }
    return $return;
}

// zaznam do tabulek neprectenych
function unreadRecords($tablenum, $rid)
{
    global $database, $user, $_POST;
    if (!is_numeric($tablenum)) {
        $tablenum = unreadTableId($tablenum);
    }
    $secret = 0;
    if (isset($_POST['secret'], $_POST['noteSecret'], $_POST['notePrivate'])) {
        $secret = $_POST['secret'];
    }
    if (isset($_POST['nsecret'])) {
        $secret = $_POST['nsecret'];
    }
    $unreadSql = "SELECT " . DB_PREFIX . "user.userId as 'id', " . DB_PREFIX . "user.aclSecret, " . DB_PREFIX . "user.userDeleted as 'deleted' FROM " . DB_PREFIX . "user";
    $unreadResult = mysqli_query($database, $unreadSql);
    while ($unreadRecord = mysqli_fetch_assoc($unreadResult)) {
        if (
            (($secret > 0 && $unreadRecord['deleted'] <> 1) && ($unreadRecord['id'] <> $user['userId'] && $unreadRecord['aclSecret'] > 0))
            || (($secret == 0 && $unreadRecord['deleted'] <> 1) && ($unreadRecord['id'] <> $user['userId']))
        ) {
            $srsql = "INSERT INTO " . DB_PREFIX . "unread (idtable, idrecord, iduser) VALUES('" . $tablenum . "', '" . $rid . "', '" . $unreadRecord['id'] . "')";
            mysqli_query($database, $srsql);
        }
    }
}

// vymaz z tabulek neprectenych pri precteni
function deleteUnread($tablenum, $rid)
{
    global $database,$user;
    if (!is_numeric($tablenum)) {
        $tablenum = unreadTableId($tablenum);
    }
    if (isset($user['userId'])) {
        if ($rid <> 'none' && $user['userId']) {
            $unreadSql = "DELETE FROM " . DB_PREFIX . "unread WHERE idtable=" . $tablenum . " AND idrecord=" . $rid . " AND iduser=" . $user['userId'];
        } elseif ($user['userId']) {
            $unreadSql = "DELETE FROM " . DB_PREFIX . "unread WHERE idtable=" . $tablenum . " AND iduser=" . $user['userId'];
        }
        if (isset($unreadSql)) {
            mysqli_query($database, $unreadSql);
        }
    }
}

// vymaz z tabulek neprectenych pri smazani zaznamu
function deleteAllUnread($tablenum, $rid)
{
    global $database;
    if (!is_numeric($tablenum)) {
        $tablenum = unreadTableId($tablenum);
    }
    $unreadSql = "SELECT " . DB_PREFIX . "user.userId as 'id', " . DB_PREFIX . "user.aclSecret FROM " . DB_PREFIX . "user";
    $unreadResult = mysqli_query($database, $unreadSql);
    while ($unreadRecord = mysqli_fetch_assoc($unreadResult)) {
        $srsql = "DELETE FROM " . DB_PREFIX . "unread WHERE idtable=" . $tablenum . " AND idrecord=" . $rid . " AND iduser=" . $unreadRecord['id'];
        mysqli_query($database, $srsql);
    }
}

// natazeni tabulky neprectenych zaznamu do promenne
if (isset($_SESSION['sid'])) {
    $unreadSql = "SELECT idtable, count(distinct idrecord) as count FROM " . DB_PREFIX . "unread WHERE iduser=" . $user['userId'] . " GROUP BY idtable";
    $unreadResult =
    $unreadResult = mysqli_query($database, $unreadSql);
    while ($unread[] = mysqli_fetch_array($unreadResult));
}

// vyhledani zaznamu v neprectenych zaznamech - cases, groups, persons, reports, symbols,
function searchRecord($tablenum, $recordnum)
{
    global $database,$user;
    $unreadSql = "SELECT * FROM " . DB_PREFIX . "unread WHERE iduser=" . $user['userId'] . " and idtable=" . $tablenum . " and idrecord=" . $recordnum;
    $unreadResult = mysqli_num_rows(mysqli_query($database, $unreadSql));
    if ($unreadResult > 0) {
        return true;
    } else {
        return false;
    }
}

// vyhledani tabulky v neprectenych zaznamech
function unreadItems($tablenum)
{
    global $unread;
    foreach ((array) $unread as $record) {
        if (isset($record) and $record['idtable'] == $tablenum) {
            return $record['count'];
        }
    }

    return false;
}
