<?php

function operationType($operationType)
{
    $operation = [
        1 => 'read',
        2 => 'edit',
        3 => 'new',
        4 => 'fileAdd',
        5 => 'fileDelete',
        6 => 'link',
        7 => 'noteNew',
        8 => 'noteDelete',
        9 => 'noteEdit',
        10 => 'GMedit',
        11 => 'delete',
        12 => 'unauthorizedAccess',
        13 => 'unauthorizedAccessDeleted',
        14 => 'secret',
        15 => 'unauthorizedAccessSecret',
        16 => 'passwordReset',
        17 => 'restore',
        18 => 'lock',
        19 => 'ulock',
    ];
    return array_search($operationType, $operation);
}

function recordType($recordType)
{
    $record = [
        1 => 'person',
        2 => 'group',
        3 => 'case',
        4 => 'report',
        5 => 'news',
        6 => 'dashboard',
        7 => 'symbol',
        8 => 'user',
        9 => 'point',
        10 => 'task',
        11 => 'audit',
        12 => 'other',
        13 => 'file',
        14 => 'backup',
        15 => 'settings',
    ];
    return array_search($recordType, $record);
}

function authorizedAccess($recordType, $operationType, $idrecord): void
{
    global $database,$user;
    // translation layer TEXT > ID
    if (is_string($recordType)) {
        $recordType = recordType($recordType);
    }
    if (is_string($operationType)) {
        $operationType = operationType($operationType);
    }
    //end of translation layer
    if (isset($user) && is_numeric($recordType) && is_numeric($operationType) && is_numeric($idrecord)) {
        $auditSql = "INSERT INTO ".DB_PREFIX."audit_trail (iduser, time, operation_type, record_type, idrecord, ip, org) VALUES('".$user['userId']."','".time()."','".$operationType."','".$recordType."','".$idrecord."','".$user['ipv4']."','".$user['aclGamemaster']."')";
        mysqli_query($database, $auditSql);
    }
}

function unauthorizedAccess($recordType, $operationType, $idrecord): void
{
    global $_SESSION,$text;
    // translation layer TEXT > ID
    if (is_string($recordType)) {
        $recordType = recordType($recordType);
    }
    if (is_string($operationType)) {
        $operationType = operationType($operationType);
    }
    //end of translation layer
    if (isset($user) && is_numeric($recordType) && is_numeric($operationType) && is_numeric($idrecord)) {
        authorizedAccess($recordType, $operationType + 100, $idrecord);
    }
    $_SESSION['message'] = $text['accessdeniedrecorded'];
    header('location: index.php');
}
