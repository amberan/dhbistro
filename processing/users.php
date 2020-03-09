<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

// smazat uzivatele
if (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'delete') {
    if (!$usrinfo['right_power']) {
        unauthorizedAccess(8, 1, 0, 0);
    } else {
        auditTrail(8, 11, $_REQUEST['user_delete']);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET deleted=1 WHERE id=".$URL[3]);
        $latteParameters['message'] = $text['uzivatelodstranen'];
    }
}// zamknout uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'lock') {
    if (!$usrinfo['right_power']) {
        unauthorizedAccess(8, 2, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET suspended=1 WHERE id=".$URL[3]);
        $latteParameters['message'] = $text['uzivatelzablokovan'];
    }
}// odemknout uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] == 'unlock') {
    if (!$usrinfo['right_power']) {
        unauthorizedAccess(8, 2, 0, 0);
    } else {
        auditTrail(8, 11, $URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET suspended=0 WHERE id=".$URL[3]);
        $latteParameters['message'] = $text['uzivatelodblokovan'];
    }
}// reset hesla uzivatele
elseif (isset($URL[3]) AND is_numeric($URL[3]) AND $URL[2] = 'reset') {
    if (!$usrinfo['right_power']) {
        unauthorizedAccess(8, 11, 0, 0);
    } else {
        $newpassword = randomPassword();
        auditTrail(8, 11, @$URL[3]);
        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET pwd=md5('".$newpassword."') WHERE id=".$URL[3]);
        $latteParameters['message'] = $text['heslonastaveno'].$newpassword;
    }
}  // vytvorit uzivatele
elseif (isset($_POST['insertuser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['heslo']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
    $ures = mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."user WHERE UCASE(login)=UCASE('".$_POST['login']."')");
    if (mysqli_num_rows ($ures)) {
        $latteParameters['message'] = $text['uzivatelexistuje'];
    } else {
        //TODO add validate_email
        mysqli_query ($database,"INSERT INTO ".DB_PREFIX."user (login,pwd,email,right_power,right_text,timeout,idperson) VALUES('".$_POST['login']."',md5('".$_POST['heslo']."'),'".$_POST['email']."','".$_POST['power']."','".$_POST['texty']."','600','0".$_POST['idperson']."')");
        if (mysqli_affected_rows($database) > 0) {
            $uidarray = mysqli_fetch_assoc (mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."user WHERE UCASE(login)=UCASE('".$_POST['login']."')"));
            if ($usrinfo['right_aud'] > 0) {
                mysqli_query ($database,"UPDATE ".DB_PREFIX."user set right_aud='".$_POST['auditor']."' WHERE id=".$uidarray['id']);
            }
            if ($usrinfo['right_org'] > 0) {
                mysqli_query ($database,"UPDATE ".DB_PREFIX."user set right_org='".$_POST['organizator']."' WHERE id=".$uidarray['id']);
            }
            auditTrail(8, 3, $uidarray['id']);
            $latteParameters['message'] = $text['uzivatelvytvoren'].$_POST['login'];
        } else {
            $latteParameters['message'] = $text['neytvoreno'];
        }
    }
}


	$customFilter = custom_Filter(8);

// *** zpracovani filtru
if (!isset($customFilter['kategorie'])) {
    $filterCat = 0;
} else {
    $filterCat = $customFilter['kategorie'];
}
if (!isset($customFilter['sort'])) {
    $filterSort = 1;
} else {
    $filterSort = $customFilter['sort'];
}
switch ($filterCat) {
	case 0: $filterSqlCat = ''; break;
	case 1: $filterSqlCat = ' AND '.DB_PREFIX.'user.right_power=1 '; break;
	case 2: $filterSqlCat = ' AND '.DB_PREFIX.'user.right_text=1 '; break;
	default: $filterSqlCat = '';
}
switch ($filterSort) {
	case 1: $filterSqlSort = ' '.DB_PREFIX.'user.login ASC '; break;
	case 2: $filterSqlSort = ' '.DB_PREFIX.'user.login DESC '; break;
	default: $filterSqlSort = ' '.DB_PREFIX.'user.login ASC ';
}
function filter ()
{
    global $filterCat,$filterSort;
    echo
'<div id="filtr" class="table">
	<form action="/users/" method="get">
		<p>Vypsat
			<select name="kategorie">
				<option value="0"'.(($filterCat == 0) ? ' selected="selected"' : '').'>všechny uživatele</option>
				<option value="1"'.(($filterCat == 1) ? ' selected="selected"' : '').'>power usery</option>
				<option value="2"'.(($filterCat == 2) ? ' selected="selected"' : '').'>editory</option>
			</select> 
			a seřadit je podle 
			<select name="sort">
				<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>ID vzestupně</option>
				<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>ID sestupně</option>
			</select>
		.</p>
	  <input type="submit" id="filterbutton" name="filter" value="Filtrovat" />
	</form>
</div>';
}




// *** vypis uživatelů


if ($usrinfo['right_org']) {
    $user_sql = "SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname FROM ".DB_PREFIX."user left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.idperson=".DB_PREFIX."person.id WHERE ".DB_PREFIX."user.deleted=0 ".$filterSqlCat." ORDER BY ".$filterSqlSort;
} else {
    $user_sql = "SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname FROM ".DB_PREFIX."user left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.idperson=".DB_PREFIX."person.id WHERE ".DB_PREFIX."user.deleted=0 AND ".DB_PREFIX."user.right_org=0 ".$filterSqlCat." ORDER BY ".$filterSqlSort;
}
$user_query = mysqli_query ($database,$user_sql);
if (mysqli_num_rows ($user_query)) {
    while ($user_record = mysqli_fetch_assoc($user_query)) {
        $user_record['lastlogon'] = webdatetime($user_record['lastlogon']);
        $user_array[] = $user_record;
    }
    $latteParameters['user_record'] = $user_array;
} else {
    $latteParameters['warning'] = $text['prazdnyvypis'];
}
latteDrawTemplate('sparklet');
filter();
latteDrawTemplate('users');

?>
