<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);
    

// upravit uzivatele
if (isset($_POST['userid'], $_POST['edituser']) && $user['aclDirector'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
    auditTrail(8, 2, $_POST['userid']);
    $ures = mysqli_query ($database,"SELECT userId FROM ".DB_PREFIX."user WHERE UCASE(userName)=UCASE('".$_POST['login']."') AND userId<>".$_POST['userid']);
    if (mysqli_num_rows ($ures)) {
        $latteParameters['message'] = "Uživatel již existuje, změňte jeho jméno.";
    } else {
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET userName='".$_POST['login']."', userEmail='".$_POST['email']."', aclDirector='".$_POST['power']."', aclTask='".$_POST['texty']."', personId='".$_POST['idperson']."' WHERE userId=".$_POST['userid']);
        if ($user['aclAudit'] > 0) {
            mysqli_query ($database,"UPDATE ".DB_PREFIX."user set aclAudit='".$_POST['auditor']."' WHERE userId=".$_POST['userid']);
        }
        if ($user['aclGamemaster'] > 0) {
            mysqli_query ($database,"UPDATE ".DB_PREFIX."user set aclGamemaster='".$_POST['organizator']."' WHERE userId=".$_POST['userid']);
        }
        $latteParameters['message'] = "Uživatel ".$_POST['login']." upraven.";
    }
}

	$customFilter = custom_Filter(8);

    $personList = personList('deleted=0 and archiv=0 and dead=0','surname');
    foreach ($personList as $personList) {
        $persons[] = array ($personList['id'], $personList['surname'], $personList['name']);
    }
    $latteParameters['persons'] = $persons;


    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."user WHERE userId=".$URL[3]);
	if ($rec = mysqli_fetch_assoc ($res)) {
	    $latteParameters['user'] = $rec;

	    $hlaseni_sql = mysqli_query ($database,"SELECT ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.iduser=".$rec['id']." AND ".DB_PREFIX."report.status=0 AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC");
	    while ($hlaseni = mysqli_fetch_assoc ($hlaseni_sql)) {
	        $hlaseni_array[] = array ($hlaseni['id'], $hlaseni['label']);
	    }
	    if (isset($hlaseni_array)) {
	        $latteParameters['user']['hlaseni'] = $hlaseni_array;
	    }

	    $pripady_sql = mysqli_query ($database,"SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."case.status=0 AND ".DB_PREFIX."c2s.idsolver=".$rec['id']." ORDER BY ".DB_PREFIX."case.title ASC");
	    while ($pripady = mysqli_fetch_assoc ($pripady_sql)) {
	        $pripady_array[] = array ($pripady['id'], $pripady['title']);
	    }
	    if (isset($pripady_array)) {
	        $latteParameters['user']['pripady'] = $pripady_array;
	    }
        
	    $ukoly_sql = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."task WHERE ".DB_PREFIX."task.iduser=".$rec['id']." AND ".DB_PREFIX."task.status=0 ORDER BY ".DB_PREFIX."task.created ASC");
	    while ($ukoly = mysqli_fetch_assoc ($ukoly_sql)) {
	        $ukoly_array[] = array ($ukoly['id'], $ukoly['task']);
	    }
	    if (isset($ukoly_array)) {
	        $latteParameters['user']['ukoly'] = $ukoly_array;
	    }
	} else {
	    $latteParameters['warning'] = $text['zaznamnenalezen'];
	}
latteDrawTemplate('sparklet');
latteDrawTemplate('user_edit');
?>
