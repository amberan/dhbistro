<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

// smazat uzivatele
if (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'delete') {
    if (!$user['aclDirector']) {
        unauthorizedAccess(8, 1, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET userDeleted=1 WHERE userId=".$URL[3]);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $text['uzivatelodstranen'];
        } else {
            $latteParameters['message'] = $text['akcinelzeprovest'];
        }
    }
}
// obnovit uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'restore') {
    if (!$user['aclDirector']) {
        unauthorizedAccess(8, 1, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET userDeleted=0 WHERE userId=".$URL[3]);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $text['uzivatelobnoven'];
        } else {
            $latteParameters['message'] = $text['akcinelzeprovest'];
        }
    }
}// zamknout uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'lock') {
    if (!$user['aclDirector']) {
        unauthorizedAccess(8, 2, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET userSuspended=1 WHERE userId=".$URL[3]);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $text['uzivatelzablokovan'];
        } else {
            $latteParameters['message'] = $text['akcinelzeprovest'];
        }
    }
}// odemknout uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'unlock') {
    if (!$user['aclDirector']) {
        unauthorizedAccess(8, 2, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET userSuspended=0 WHERE userId=".$URL[3]);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $text['uzivatelodblokovan'];
        } else {
            $latteParameters['message'] = $text['akcinelzeprovest'];
        }
    }
}// reset hesla uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] = 'reset') {
    if (!$user['aclDirector']) {
        unauthorizedAccess(8, 11, 0, 0);
    } else {
        $newpassword = randomPassword();
        auditTrail(8, 11, @$URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET userPassword=md5('".$newpassword."') WHERE userId=".$URL[3]);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $text['heslonastaveno'].$newpassword;
        } else {
            $latteParameters['message'] = $text['akcinelzeprovest'];
        }
    }
}  // vytvorit uzivatele
elseif (isset($_POST['insertuser']) && $user['aclDirector'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['heslo']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
    $ures = mysqli_query ($database,"SELECT userId FROM ".DB_PREFIX."user WHERE UCASE(userName)=UCASE('".$_POST['login']."')");
    if (mysqli_num_rows ($ures)) {
        $latteParameters['message'] = $text['uzivatelexistuje'];
    } else {
        //TODO add validate_email
        mysqli_query ($database,"INSERT INTO ".DB_PREFIX."user (userName,userPassword,userEmail,aclDirector,aclTask,userTimeout,personId) VALUES('".$_POST['login']."',md5('".$_POST['heslo']."'),'".$_POST['email']."','".$_POST['power']."','".$_POST['texty']."','600','0".$_POST['idperson']."')");
        if (mysqli_affected_rows($database) > 0) {
            $uidarray = mysqli_fetch_assoc (mysqli_query ($database,"SELECT userId FROM ".DB_PREFIX."user WHERE UCASE(userName)=UCASE('".$_POST['login']."')"));
            if ($user['aclAudit'] > 0) {
                mysqli_query ($database,"UPDATE ".DB_PREFIX."user set aclAudit='".$_POST['auditor']."' WHERE userId=".$uidarray['id']);
            }
            if ($user['aclGamemaster'] > 0) {
                mysqli_query ($database,"UPDATE ".DB_PREFIX."user set aclGamemaster='".$_POST['organizator']."' WHERE userId=".$uidarray['id']);
            }
            auditTrail(8, 3, $uidarray['id']);
            $latteParameters['message'] = $text['uzivatelvytvoren'].$_POST['login'];
        } else {
            $latteParameters['message'] = $text['neytvoreno'];
        }
    }
}

if (isset($_GET['sort'])) {
    sortingSet('user',$_GET['sort'],'person');
}

// *** vypis uživatelů
$user_sql = "SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname 
            FROM ".DB_PREFIX."user 
            left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.personId=".DB_PREFIX."person.id 
            WHERE ".$user['sqlDeleted']." AND ".DB_PREFIX."user.aclGamemaster <= ".$user['aclGamemaster']." ".sortingGet('user','person');

$user_query = mysqli_query ($database,$user_sql);
if (mysqli_num_rows ($user_query)) {
    while ($user_record = mysqli_fetch_assoc($user_query)) {
        if ($user_record['lastLogin'] < 1) { $user_record['lastLogin'] = $text['nikdy']; }
        else {$user_record['lastLogin'] = webdatetime($user_record['lastLogin']); }
        $user_array[] = $user_record;
    }
    $latteParameters['user_record'] = $user_array;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}
latteDrawTemplate('sparklet');
//TODO DODELAT FILTROVANI PODLE PRAV
latteDrawTemplate('users');

?>
