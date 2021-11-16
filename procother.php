<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

        // Ukoly
    if (isset($_REQUEST['acctask']) && is_numeric($_REQUEST['acctask']) && $user['aclTask']) {
        auditTrail(10, 2, $_REQUEST['acctask']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."task SET status=2, modified='".time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['acctask']);
        //		deleteAllUnread (1,$_REQUEST['delete']);
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
    if (isset($_REQUEST['rtrntask']) && is_numeric($_REQUEST['rtrntask']) && $user['aclTask']) {
        auditTrail(10, 2, $_REQUEST['rtrntask']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."task SET status=0, modified='".time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['rtrntask']);
        //		deleteAllUnread (1,$_REQUEST['delete']);
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
    if (isset($_REQUEST['fnshtask']) && is_numeric($_REQUEST['fnshtask'])) {
        auditTrail(10, 2, $_REQUEST['fnshtask']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."task SET status=1, modified='".time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['fnshtask']);
        //		deleteAllUnread (1,$_REQUEST['delete']);
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
    if (isset($_REQUEST['cncltask']) && is_numeric($_REQUEST['cncltask']) && $user['aclTask']) {
        auditTrail(10, 2, $_REQUEST['cncltask']);
        mysqli_query($database, "UPDATE ".DB_PREFIX."task SET status=3, modified='".time()."', modified_by='".$user['userId']."' WHERE id=".$_REQUEST['cncltask']);
        //		deleteAllUnread (1,$_REQUEST['delete']);
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
