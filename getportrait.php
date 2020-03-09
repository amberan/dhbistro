<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);


header('Content-Type: image/jpg');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
if (isset($_REQUEST['rid']) && is_numeric ($_REQUEST['rid'])) { //portret
    $getres = mysqli_query ($database,"SELECT portrait FROM ".DB_PREFIX."person WHERE ".(($usrinfo['right_power']) ? '' : ' secret=0 AND ')." id=".$_REQUEST['rid']);
    if ($getrec = mysqli_fetch_assoc ($getres)) {
        header('Content-Disposition: inline; filename="portrait'.$_REQUEST['rid'].'.jpg"');
        if (mb_strlen($getrec['portrait']) > 0 and file_exists($config['folder_portrait'].$getrec['portrait']) ) {
            $getf = FOpen ($config['folder_portrait'].$getrec['portrait'],"r");
        } else {
            $getf = FOpen (SERVER_ROOT."/images/placeholder.jpg","r");
        }
    }
} elseif (isset($_REQUEST['nrid']) && is_numeric ($_REQUEST['nrid'])) { //symbol
    $getres = mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."symbol WHERE ".(($usrinfo['right_power']) ? '' : ' secret=0 AND ')." id=".$_REQUEST['nrid']);
    if ($getrec = mysqli_fetch_assoc ($getres)) {
        header('Content-Disposition: inline; filename="symbol'.$_REQUEST['nrid'].'.jpg"');
        if (file_exists($config['folder_symbol'].$getrec['symbol'])) {
            $getf = FOpen ($config['folder_symbol'].$getrec['symbol'],"r");
        }
    }
} else {
    $getf = FOpen (SERVER_ROOT."/images/nosymbol.png","r");
}

FPassThru ($getf);
?>
