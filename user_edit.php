<?php 
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);

// upravit uzivatele
if (isset($_POST['userid']) && isset($_POST['edituser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	auditTrail(8, 2, $_POST['userid']);
	$ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."user WHERE UCASE(login)=UCASE('".$_POST['login']."') AND id<>".$_POST['userid']);
	if (mysqli_num_rows ($ures)) {
		$latteParameters['message']= "Uživatel již existuje, změňte jeho jméno.";
	} else {
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET login='".$_POST['login']."', right_power='".$_POST['power']."', right_text='".$_POST['texty']."', idperson='".$_POST['idperson']."' WHERE id=".$_POST['userid']);
		if ($usrinfo['right_aud'] > 0) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."user set right_aud='".$_POST['auditor']."' WHERE id=".$_POST['userid']);
			Debugger::log('USERID: '.$_POST['userid'].' AUDIT='.$_POST['auditor']);
		}
		if ($usrinfo['right_org'] > 0) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."user set right_org='".$_POST['organizator']."' WHERE id=".$_POST['userid']);
			Debugger::log('USERID: '.$_POST['userid'].' ORG='.$_POST['organizator']);
		}
		$latteParameters['message']= "Uživatel ".$_POST['login']." upraven.";
	}
} // zmeny sam sebe
else if ((isset($_POST['userid']) AND isset($_POST['edituser']) AND !is_numeric($_REQUEST['timeout'])) AND ($usrinfo['id'] == $_POST['userid'] )) {
		$latteParameters['message'] = "Timeout není číslo, nastavení nebylo uloženo.";
	} else if (isset($_REQUEST['editsettings']) && ($_REQUEST['timeout'] > 1800 || $_REQUEST['timeout'] < 30)) {
		$latteParameters['message'] = "Timeout nesouhlasí, je buď příliš malý nebo příliš velký.";
	} elseif (isset($_REQUEST['editsettings']) && isset($_REQUEST['soucheslo']) && $_REQUEST['soucheslo']<>'') {
		$currentpwd=mysqli_fetch_assoc (mysqli_query ($database,"SELECT pwd FROM ".DB_PREFIX."user WHERE sid='".$_SESSION['sid']."'"));
		if ($currentpwd['pwd'] == md5($_REQUEST['soucheslo'])) {
			mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET pwd=md5('".$_POST['heslo']."'), plan_md='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
			$latteParameters['message'] = "Nastavení s novým heslem uloženo.";
		} else {
			$latteParameters['message'] = "Nesouhlasí staré heslo, nastavení nebylo uloženo.";
		}
	} elseif (isset($_REQUEST['editsettings'])) {
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET plan_md='".$_REQUEST['plan']."', timeout='".$_REQUEST['timeout']."' WHERE sid='".$_SESSION['sid']."'");
		$latteParameters['message'] = "Nastavení uloženo.";
		read_user();
} 

    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
	$custom_Filter = custom_Filter(8);

    $res=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."user WHERE id=".$URL[3]);
	if ($rec=mysqli_fetch_assoc ($res)) {
        $latteParameters['user'] = $rec;

        $hlaseni_sql=mysqli_query ($database,"SELECT ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.iduser=".$rec['id']." AND ".DB_PREFIX."report.status=0 AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC");
        while ($hlaseni=mysqli_fetch_assoc ($hlaseni_sql)) {
            $hlaseni_array[] =  array ($hlaseni['id'],$hlaseni['label']);
        }
        $latteParameters['user']['hlaseni'] = $hlaseni_array;

        $pripady_sql=mysqli_query ($database,"SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$rec['id']." ORDER BY ".DB_PREFIX."case.title ASC");
        while ($pripady=mysqli_fetch_assoc ($pripady_sql)) {
            $pripady_array[]  =  array ($pripady['id'], $pripady['title']);
        }
        $latteParameters['user']['pripady'] = $pripady_array;
        
        $ukoly_sql=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."task WHERE ".DB_PREFIX."task.iduser=".$rec['id']." AND ".DB_PREFIX."task.status=0 ORDER BY ".DB_PREFIX."task.created ASC");
        while ($ukoly=mysqli_fetch_assoc ($ukoly_sql)) {
            $ukoly_array[] = array ($ukoly['id'],$ukoly['task']);
        }
        $latteParameters['user']['ukoly'] = $ukoly_array;
    } else {
        $latteParameters['warning'] = $text['zaznamnenalezen'];
    }
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'user_edit.latte', $latteParameters);
?>
