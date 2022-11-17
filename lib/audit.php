<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

function authorizedAccess($recordType, $operationType, $idrecord): void
{
    global $database,$user;
    if (isset($user)) {
        $auditSql = "INSERT INTO ".DB_PREFIX."audit_trail (iduser, time, operation_type, record_type, idrecord, ip, org) VALUES('".$user['userId']."','".time()."','".$operationType."','".$recordType."','".$idrecord."','".$user['ipv4']."','".$user['aclGamemaster']."')";
        mysqli_query($database, $auditSql);
    }
}

function unauthorizedAccess($recordType, $operationType, $idrecord): void
{
    global $_SESSION,$text;
    authorizedAccess($recordType, $operationType+100, $idrecord);
    $_SESSION['message'] = $text['accessdeniedrecorded'];
    header('location: index.php');
}
