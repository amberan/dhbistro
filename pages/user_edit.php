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
    if (count($personList) > 1 ) {

    foreach ($personList as $personList) {
        $persons[] = array ($personList['id'], $personList['surname'], $personList['name']);
    } 
    $latteParameters['persons'] = $persons;
    }

    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."user WHERE userId=".$URL[3]);
	if ($rec = mysqli_fetch_assoc ($res)) {
	    $latteParameters['userEdit'] = $rec;

        $reportsAssignedToUser = reportsAssignedTo($rec['userId']);
        if ($reportsAssignedToUser) { 
            $latteParameters['userEdit']['hlaseni'] = $reportsAssignedToUser;
        }


        $casesAssignedToUser = casesAssignedTo($rec['userId']);
        if ($casesAssignedToUser) { 
            $latteParameters['userEdit']['pripady'] = $casesAssignedToUser;
        }

        $tasksAssignedToUser = tasksAssignedTo($rec['userId']);
        if ($tasksAssignedToUser) { 
            $latteParameters['userEdit']['ukoly'] = $tasksAssignedToUser;
        }

	} else {
	    $latteParameters['warning'] = $text['zaznamnenalezen'];
	}
latteDrawTemplate('sparklet');
latteDrawTemplate('user_edit');
?>
