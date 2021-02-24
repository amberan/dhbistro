<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

//auditni stopa
function auditTrail($recordType,$operationType,$idrecord): void
{
    global $database,$user;
    $sqlCheck = "SELECT * FROM ".DB_PREFIX."audit_trail WHERE iduser='".$user['userId']."' AND time='".time()."'";
    $resCheck = mysqli_query($database,$sqlCheck);
    if (mysqli_num_rows($resCheck)) {
    } else {
        if (!$user['ipv4']) {
            $currip = $_SERVER['REMOTE_ADDR'];
        } else {
            $currip = $user['ipv4'];
        }
        $sqlAu = "INSERT INTO ".DB_PREFIX."audit_trail VALUES('','".$user['userId']."','".time()."','".$operationType."','".$recordType."','".$idrecord."','".$currip."','".$user['aclGamemaster']."')";
        mysqli_query($database,$sqlAu);
    }
}

//pokus o pristup k tajnemu, soukromemu nebo smazanemu zaznamu
function unauthorizedAccess($recordType,$secret,$deleted,$idrecord): void
{
    global $latteParameters;
    $latteParameters['title'] = 'Neautorizovaný přístup';
    if ($deleted > 0) {
        auditTrail($recordType, 13, $idrecord);
    } elseif ($secret > 0) {
        auditTrail($recordType, 15, $idrecord);
    } else {
        auditTrail($recordType, 12, $idrecord);
    }
    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
    header('location: index.php');
}
