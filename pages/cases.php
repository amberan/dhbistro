<?php

//TODO DODELAT FILTROVANI PODLE PRAV

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

    if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) { //delete case
        auditTrail(3, 11, $_REQUEST['delete']);
        mysqli_query($database,"UPDATE ".DB_PREFIX."case SET deleted=1 WHERE id=".$_REQUEST['delete']);
        deleteAllUnread(3,$_REQUEST['delete']);
    }
    if (isset($_POST['insertcase']) && !preg_match('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
        $ures = mysqli_query($database,"SELECT id FROM ".DB_PREFIX."case WHERE UCASE(title)=UCASE('".$_POST['title']."')");
        if (mysqli_num_rows($ures)) {
            $_SESSION['message'] = 'Případ již existuje, změňte jeho jméno.';
        } else {
            mysqli_query($database,"INSERT INTO ".DB_PREFIX."case  (caseCreated, title, datum, iduser, contents, secret, deleted, status) VALUES(CURRENT_TIMESTAMP, '".$_POST['title']."','".time()."','".$user['userId']."','".$_POST['contents']."','".$_POST['secret']."','0','".$_POST['status']."')");
            $cidarray = mysqli_fetch_assoc(mysqli_query($database,"SELECT id FROM ".DB_PREFIX."case WHERE UCASE(title)=UCASE('".$_POST['title']."')"));
            $cid = $cidarray['id'];
            auditTrail(3, 3, $cid);
            if (!isset($_POST['notnew'])) {
                unreadRecords(3,$cid);
            }
            $_SESSION['message'] = 'Případ vytvořen.';
        }
    } else {
        if (isset($_POST['insertcase'])) {
            $_SESSION['message'] = 'Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
        }
    }
    if (isset($_POST['caseid'], $_POST['editcase']) && $user['aclCase'] && !preg_match('/^[[:blank:]]*$/i',$_POST['title']) && !preg_match('/^[[:blank:]]*$/i',$_POST['contents']) && is_numeric($_POST['secret']) && is_numeric($_POST['status'])) {
        auditTrail(3, 2, $_POST['caseid']);
        if (!isset($_POST['notnew'])) {
            unreadRecords(3,$_POST['caseid']);
        }
        $ures = mysqli_query($database,"SELECT id FROM ".DB_PREFIX."case WHERE UCASE(title)=UCASE('".$_POST['title']."') AND id<>".$_POST['caseid']);
        if (mysqli_num_rows($ures)) {
            $_SESSION['message'] = 'Případ již existuje, změňte jeho jméno.';
        } else {
            if ($user['aclGamemaster'] == 1) {
                $sqlCaseUpdate = "UPDATE ".DB_PREFIX."case SET title='".$_POST['title']."', contents='".$_POST['contents']."', secret='".$_POST['secret']."', status='".$_POST['status']."' WHERE id=".$_POST['caseid'];
                mysqli_query($database,$sqlCaseUpdate);
            } else {
                $sqlCaseUpdate = "UPDATE ".DB_PREFIX."case SET title='".$_POST['title']."', datum='".time()."', iduser='".$user['userId']."', contents='".$_POST['contents']."', secret='".$_POST['secret']."', status='".$_POST['status']."' WHERE id=".$_POST['caseid'];
                mysqli_query($database,$sqlCaseUpdate);
            }
            $_SESSION['message'] = 'Případ upraven.';
        }
    } else {
        if (isset($_POST['editcase'])) {
            $_SESSION['message'] = 'Chyba při ukládání změn, ujistěte se, že jste vše provedli správně a máte potřebná práva.';
        }
    }
    if (isset($_POST['uploadfile']) && is_uploaded_file($_FILES['attachment']['tmp_name']) && is_numeric($_POST['caseid']) && is_numeric($_POST['secret'])) {
        auditTrail(3, 4, $_POST['caseid']);
        $newname = time().md5(uniqid(time().random_int(0, getrandmax())));
        move_uploaded_file($_FILES['attachment']['tmp_name'],'./files/'.$newname);
        $sql = "INSERT INTO ".DB_PREFIX."file (uniquename,originalname,mime,size,datum,iduser,idtable,iditem,secret) VALUES('".$newname."','".$_FILES['attachment']['name']."','".$_FILES['attachment']['type']."','".$_FILES['attachment']['size']."','".time()."','".$user['userId']."','3','".$_POST['caseid']."','".$_POST['secret']."')";
        mysqli_query($database,$sql);
        if (!isset($_POST['fnotnew'])) {
            unreadRecords(3,$_POST['caseid']);
        }
    } else {
        if (isset($_POST['uploadfile'])) {
            $_SESSION['message'] = 'Soubor nebyl přiložen, něco se nepodařilo. Možná nebyl zvolen přikládaný soubor.';
        }
    }
    if (isset($_GET['deletefile']) && is_numeric($_GET['deletefile'])) {
        auditTrail(3, 5, $_GET['caseid']);
        if ($user['right_text']) {
            $fres = mysqli_query($database,"SELECT uniquename FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
            $frec = mysqli_fetch_assoc($fres);
            unlink('./files/'.$frec['uniquename']);
            mysqli_query($database,"DELETE FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.id=".$_GET['deletefile']);
        }
        header('Location: editcase.php?rid='.$_GET['caseid']);
    }

//FILTER
if (isset($_GET['sort'])) {
    sortingSet('case',$_GET['sort'],'case');
}
if (sizeof($_POST['filter']) > 0) {
    filterSet('case',@$_POST['filter']);
}
$filter = filterGet('case');
$sqlFilter = DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];
switch (@$filter['stat']) {
    case 'on': $sqlFilter .= ' AND '.DB_PREFIX.'case.status in (0,1)'; break;
    default: $sqlFilter .= ' AND '.DB_PREFIX.'case.status=0 ';
}
$latteParameters['filter'] = $filter;

//CASE LIST
$sql = "SELECT ".DB_PREFIX."case.datum as date_changed, ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.deleted AS 'deleted' , ".DB_PREFIX."case.caseCreated
FROM ".DB_PREFIX."case 
WHERE ".$sqlFilter.sortingGet('case');
$caseList = mysqli_query($database,$sql);

if (mysqli_num_rows($caseList) > 0) {
    $latteParameters['case_record'] = $caseList;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}

latteDrawTemplate('sparklet');
latteDrawTemplate('cases');
