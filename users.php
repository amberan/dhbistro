<?php
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);


// smazat uzivatele
if (is_numeric($URL[3]) and $URL[2] == 'delete') {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 1, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_delete']);
		Debugger::log("USER $URL[3] DELETED");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET deleted=1 WHERE id=".$URL[3]);
		$latteParameters['message'] = $text['uzivatelodstranen'];
	}
}// zamknout uzivatele
elseif (is_numeric($URL[3]) and $URL[2] == 'lock') {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
	} else {
		auditTrail(8, 11, $URL[3]);
		Debugger::log("USER $URL[3] LOCKED");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET suspended=1 WHERE id=".$URL[3]);
		$latteParameters['message'] = $text['uzivatelzablokovan'];
	}
}// odemknout uzivatele
elseif (is_numeric($URL[3]) and $URL[2] == 'unlock') {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
	} else {
		auditTrail(8, 11, $URL[3]);
		Debugger::log("USER $URL[3] UNLOCKED");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET suspended=0 WHERE id=".$URL[3]);
		$latteParameters['message'] = $text['uzivatelodblokovan'];
	}
}// reset hesla uzivatele
elseif (is_numeric($URL[3]) and $URL[2] = 'reset') {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 11, 0, 0);
	} else {    
        $newpassword = randomPassword();
        auditTrail(8, 11, $_REQUEST['user_reset']);
        Debugger::log("USER $URL[3] PASSWORD RESET");
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET pwd=md5('".$newpassword."') WHERE id=".$URL[3]);
        $latteParameters['message'] = $text['heslonastaveno'].$newpassword; 
    }
}  // vytvorit uzivatele
elseif (isset($_POST['insertuser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['heslo']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	$ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."user WHERE UCASE(login)=UCASE('".$_POST['login']."')");
	if (mysqli_num_rows ($ures)) {
		$latteParameters['message']= $text['uzivatelexistuje'];
	} else {
		mysqli_query ($database,"INSERT INTO ".DB_PREFIX."user (login,pwd,right_power,right_text,timeout,idperson) VALUES('".$_POST['login']."',md5('".$_POST['heslo']."'),'".$_POST['power']."','".$_POST['texty']."','600','".$_POST['idperson']."')");
		if (mysqli_affected_rows($database) > 0) { 
			$uidarray=mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."user WHERE UCASE(login)=UCASE('".$_POST['login']."')"));
			if ($usrinfo['right_aud'] > 0) {
				mysqli_query ($database,"UPDATE ".DB_PREFIX."user set right_aud='".$_POST['auditor']."' WHERE id=".$uidarray['id']);
			}
			if ($usrinfo['right_org'] > 0) {
				mysqli_query ($database,"UPDATE ".DB_PREFIX."user set right_org='".$_POST['organizator']."' WHERE id=".$uidarray['id']);
			}
			auditTrail(8, 3, $uidarray['id']);
			Debugger::log("USER ".$_POST['login']."[".$uidarray['id']."] CREATED");
			$latteParameters['message']= $text['uzivatelvytvoren'].$_POST['login'];
		} else {
			$latteParameters['message']= $text['neytvoreno'];
		}
	}
}



    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
	$custom_Filter = custom_Filter(8);

// *** zpracovani filtru
if (!isset($custom_Filter['kategorie'])) {
	$f_cat=0;
} else {
	$f_cat=$custom_Filter['kategorie'];
}
if (!isset($custom_Filter['sort'])) {
	$f_sort=1;
} else {
	$f_sort=$custom_Filter['sort'];
}
switch ($f_cat) {
	case 0: $fsql_cat=''; break;
	case 1: $fsql_cat=' AND '.DB_PREFIX.'user.right_power=1 '; break;
	case 2: $fsql_cat=' AND '.DB_PREFIX.'user.right_text=1 '; break;
	default: $fsql_cat='';
}
switch ($f_sort) {
	case 1: $fsql_sort=' '.DB_PREFIX.'user.login ASC '; break;
	case 2: $fsql_sort=' '.DB_PREFIX.'user.login DESC '; break;
	default: $fsql_sort=' '.DB_PREFIX.'user.login ASC ';
}
function filter () {
    global $database,$f_cat,$f_sort;
	echo 
'<div id="filtr" class="table">
	<form action="/users" method="get">
		<p>Vypsat
			<select name="kategorie">
				<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechny uživatele</option>
				<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>power usery</option>
				<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>editory</option>
			</select> 
			a seřadit je podle 
			<select name="sort">
				<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>ID vzestupně</option>
				<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>ID sestupně</option>
			</select>
		.</p>
	  <input type="submit" id="filterbutton" name="filter" value="Filtrovat" />
	</form>
</div>';
}




// *** vypis uživatelů

filter();
if ($usrinfo['right_org']) {
	$user_sql="SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname FROM ".DB_PREFIX."user left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.idperson=".DB_PREFIX."person.id WHERE ".DB_PREFIX."user.deleted=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
} else {
	$user_sql="SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname FROM ".DB_PREFIX."user left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.idperson=".DB_PREFIX."person.id WHERE ".DB_PREFIX."user.deleted=0 AND ".DB_PREFIX."user.right_org=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
}
$user_query = mysqli_query ($database,$user_sql);
if (mysqli_num_rows ($user_query)) {
	while ($user_record=mysqli_fetch_assoc($user_query)) { 	
        $user_record['lastlogon'] = webdatetime($user_record['lastlogon']);
        $user_array[] = $user_record;
    }
    $latteParameters['user_record'] = $user_array;
} else {
$latteParameters['warning'] = $text['prazdnyvypis'];
}
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'users.latte', $latteParameters);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);

?>
