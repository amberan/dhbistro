<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');

  
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);

//auditni stopa
function auditTrail ($recordType,$operationType,$idrecord)
{
    global $database,$usrinfo;
    $sqlCheck = "SELECT * FROM ".DB_PREFIX."audit_trail WHERE iduser='".$usrinfo['id']."' AND time='".time()."'";
    $resCheck = mysqli_query ($database,$sqlCheck);
    if (mysqli_num_rows ($resCheck)) {
    } else {
        if (!$usrinfo['currip']) {
            $currip = $_SERVER['REMOTE_ADDR'];
        } else {
            $currip = $usrinfo['currip'];
        }
        $sqlAu = "INSERT INTO ".DB_PREFIX."audit_trail VALUES('','".$usrinfo['id']."','".time()."','".$operationType."','".$recordType."','".$idrecord."','".$currip."','".$usrinfo['right_org']."')";
        mysqli_query ($database,$sqlAu);
    }
}

//pokus o pristup k tajnemu, soukromemu nebo smazanemu zaznamu
function unauthorizedAccess ($recordType,$secret,$deleted,$idrecord)
{
    $latteParameters['title'] = 'Neautorizovaný přístup';
    switch ($recordType) {
            case 1:
                $link = '<a href="./persons.php">osoby</a>';
                break;
            case 2:
                $link = '<a href="./groups.php">skupiny</a>';
                break;
            case 3:
                $link = '<a href="./cases.php">případy</a>';
                break;
            case 4:
                $link = '<a href="./reports.php">hlášení</a>';
                break;
            case 8:
                $link = 'A ven!';
                break;
            case 11:
                $link = 'A ven!';
                break;
        }
    if ($deleted == 1) {
        auditTrail($recordType, 13, $idrecord);
    } else {
        auditTrail($recordType, 12, $idrecord);
    }
    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
    Header ('location: index.php');
}



?>
