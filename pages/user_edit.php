<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);
    

// upravit uzivatele
if (isset($_POST['userid'], $_POST['edituser']) && $user['aclDirector'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) ) {
    auditTrail(8, 2, $_POST['userid']);
    $usernameConflict = mysqli_query ($database,"SELECT userId FROM ".DB_PREFIX."user WHERE UCASE(userName)=UCASE('".$_POST['login']."') AND userId<>".$_POST['userid']);
    if (mysqli_num_rows ($usernameConflict)) {
        $latteParameters['message'] = "Uživatel již existuje, změňte jeho jméno.";
    } else {
        $data['userName'] = $_POST['login'];
        $data['aclRoot'] = $_POST['aclRoot'];
        $data['aclDirector'] = $_POST['aclDirector'];
        $data['aclDeputy'] = $_POST['aclDeputy'];
        $data['aclTask'] = $_POST['aclTask'];
        $data['aclSecret'] = $_POST['aclSecret'];
        $data['aclAudit'] = $_POST['aclAudit'];
        $data['aclGroup'] = $_POST['aclGroup'];
        $data['aclPerson'] = $_POST['aclPerson'];
        $data['aclCase'] = $_POST['aclCase'];
        $data['aclHunt'] = $_POST['aclHunt'];
        $data['aclGamemaster'] = $_POST['aclGamemaster'];
        $data['aclAPI'] = $_POST['aclAPI'];
        if (validate_mail($_POST['userEmail'])) { $data['userEmail'] = $_POST['userEmail'];}
        $data['personId'] = $_POST['idperson'];
        userChange($_POST['userid'],$data);
        $latteParameters['message'] = "Uživatel ".$_POST['login']." upraven.";
    }
}

    $personList = personList('deleted=0 and archiv=0 and dead=0','surname');
    foreach ($personList as $personList) {
        $persons[] = array ($personList['id'], $personList['surname'], $personList['name']);
    }
    $latteParameters['persons'] = $persons;


    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."user WHERE userId=".$URL[3]);
	if ($rec = mysqli_fetch_assoc ($res)) {
	    $latteParameters['userEdit'] = $rec;

	    // $hlaseni_sql = mysqli_query ($database,"SELECT ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.iduser=".$rec['userId']." AND ".DB_PREFIX."report.status=0 AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC");
	    // while ($hlaseni = mysqli_fetch_assoc ($hlaseni_sql)) {
	    //     $hlaseni_array[] = array ($hlaseni['id'], $hlaseni['label']);
	    // }
	    // if (isset($hlaseni_array)) {
	    //     $latteParameters['userEdit']['hlaseni'] = $hlaseni_array;
        // }
        $reportsAssignedToUser = reportsAssignedTo($rec['userId']);
        if ($reportsAssignedToUser) { print_r($reportsAssignedToUser);
            $latteParameters['userEdit']['hlaseni'] = $reportsAssignedToUser;
        }


	    $pripady_sql = mysqli_query ($database,"SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."case.status=0 AND ".DB_PREFIX."c2s.idsolver=".$rec['userId']." ORDER BY ".DB_PREFIX."case.title ASC");
	    while ($pripady = mysqli_fetch_assoc ($pripady_sql)) {
	        $pripady_array[] = array ($pripady['id'], $pripady['title']);
	    }
	    if (isset($pripady_array)) {
	        $latteParameters['userEdit']['pripady'] = $pripady_array;
	    }
        
	    $ukoly_sql = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."task WHERE ".DB_PREFIX."task.iduser=".$rec['userId']." AND ".DB_PREFIX."task.status=0 ORDER BY ".DB_PREFIX."task.created ASC");
	    while ($ukoly = mysqli_fetch_assoc ($ukoly_sql)) {
	        $ukoly_array[] = array ($ukoly['id'], $ukoly['task']);
	    }
	    if (isset($ukoly_array)) {
	        $latteParameters['userEdit']['ukoly'] = $ukoly_array;
	    }
	} else {
	    $latteParameters['warning'] = $text['zaznamnenalezen'];
	}
latteDrawTemplate('sparklet');
latteDrawTemplate('user_edit');
?>
