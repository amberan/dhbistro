<?php
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);

function randomPassword() {
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$pass = array(); 
	$alphaLength = strlen($alphabet) - 1; 
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); 
}

// smazat uzivatele
if (isset($_REQUEST['user_delete']) && is_numeric($_REQUEST['user_delete'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 1, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_delete']);
		Debugger::log("USER DELETED");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET deleted=1 WHERE id=".$_REQUEST['user_delete']);
		$_SESSION['message'] = "Uživatelský účet odstraněn!";
	}
}// zamknout uzivatele
elseif (isset($_REQUEST['user_lock']) && is_numeric($_REQUEST['user_lock'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_lock']);
		Debugger::log("USER LOCKED");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET suspended=1 WHERE id=".$_REQUEST['user_lock']);
		$_SESSION['message'] = "Uživatelský účet zablokován!";
	}
}// odemknout uzivatele
elseif (isset($_REQUEST['user_unlock']) && is_numeric($_REQUEST['user_unlock'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
	} else {
		auditTrail(8, 11, $_REQUEST['user_unlock']);
		Debugger::log("USER UNLOCKED");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET suspended=0 WHERE id=".$_REQUEST['user_unlock']);
		$_SESSION['message'] = "Uživatelský účet odblokován!";
	}
}// reset hesla uzivatele
elseif (isset($_REQUEST['user_reset']) && is_numeric($_REQUEST['user_reset'])) {
	if (!$usrinfo['right_power']) {
		unauthorizedAccess(8, 2, 0, 0);
		$_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	} else {
		$newpassword = randomPassword();
		auditTrail(8, 11, $_REQUEST['user_reset']);
		Debugger::log("USER PASSWORD RESET");
		mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET pwd=md5('".$newpassword."') WHERE id=".$_REQUEST['user_reset']);
		$_SESSION['message'] = "Nové heslo nastaveno: ".$newpassword; 
	}
}


// vytvorit uzivatele
if (isset($_POST['insertuser']) && $usrinfo['right_power'] && !preg_match ('/^[[:blank:]]*$/i',$_POST['login']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['heslo']) && is_numeric($_POST['power']) && is_numeric($_POST['texty'])) {
	$ures=mysqli_query ($database,"SELECT id FROM ".DB_PREFIX."user WHERE UCASE(login)=UCASE('".$_POST['login']."')");
	if (mysqli_num_rows ($ures)) {
		$_SESSION['message']= "Uživatel již existuje, změňte jeho jméno.";
	} else {
		mysqli_query ($database,"INSERT INTO ".DB_PREFIX."user (login,pwd,right_power,right_text,timeout) VALUES('".$_POST['login']."',md5('".$_POST['heslo']."'),'".$_POST['power']."','".$_POST['texty']."','600')");
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
			$_SESSION['message']= "Uživatel ".$_POST['login']." vytvořen.";
		} else {
			$_SESSION['message']= "Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.";
		}
	}
}



    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
	$custom_Filter = custom_Filter(8);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'users.latte', $latteParameters);
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
echo '<h1 class="center">Výpis uživatelů</h1>';
filter();
if ($usrinfo['right_org']) {
	$user_sql="SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname FROM ".DB_PREFIX."user left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.idperson=".DB_PREFIX."person.id WHERE ".DB_PREFIX."user.deleted=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
} else {
	$user_sql="SELECT ".DB_PREFIX."user.*,".DB_PREFIX."person.name,".DB_PREFIX."person.surname FROM ".DB_PREFIX."user left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.idperson=".DB_PREFIX."person.id WHERE ".DB_PREFIX."user.deleted=0 AND ".DB_PREFIX."user.right_org=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
}
$user_query = mysqli_query ($database,$user_sql);
if (mysqli_num_rows ($user_query)) {
	echo '<div class="table" id="users">';
	$even = 0;
	while ($user_record=mysqli_fetch_assoc($user_query)) { 	?>
<div class="row <?php if ($even%2==0) { echo 'even';} else { echo'odd';} ?>">
    <div class="cell">
        <div class="name">&nbsp;<?php echo $user_record['name']." ".$user_record['surname'];?></div>
        <div><?php echo $user_record['login'];?></div>
    </div>
    <div class="cell">
        <div class="permissions"> &nbsp;
            <?php if ($user_record['right_power']) { echo '<span class="button">POWER USER</span>'; } ?>
            <?php if ($user_record['right_text']) { echo '<span class="button">EDITOR</span>'; } ?>
            <?php if ($user_record['right_org']) { echo '<span class="button">ORGANIZATOR</span>'; } ?>
            <?php if ($user_record['right_aud']) { echo '<span class="button">AUDITOR</span>'; } ?>
        </div>
        <div>Naposledy: <?php  if ($user_record['lastlogon']) { echo webdatetime($user_record['lastlogon']);} else { echo 'nikdy';}?> </div>
    </div>
    <div class="cell middle">
        <a class="button" href="/users/edit/<?php echo $user_record['id']?>">upravit</a>
        <?php	
		if ($user_record['id'] != $usrinfo['id']) {
				echo '<a class="button" href="/users/reset/'.$user_record['id'].'" onclick="'."return confirm('Opravdu vygenerovat nové heslo pro uživatele &quot;".$user_record['login']."&quot;?');".'">nové heslo</a>';
			if ($user_record['suspended'] == "1") {
				echo '<a class="button" href="/users/user/unlock/'.$user_record['id'].'" onclick="'."return confirm('Opravdu odemknout uživatele &quot;".$user_record['login']."&quot;?');".'">odemknout</a>';
			} else {
				echo '<a class="button" href="/users/lock/'.$user_record['id'].'" onclick="'."return confirm('Opravdu zamknout uživatele &quot;".$user_record['login']."&quot;?');".'">zamknout</a>';
			}
			echo '<a class="button" href="/users/delete/'.$user_record['id'].'" onclick="'."return confirm('Opravdu smazat uživatele &quot;".$user_record['login']."&quot;?');".'">smazat</a>';
		}	
			?>
    </div>
</div>

<?php
			$even++;
		}
	echo '</div>';
	
} else {
  echo '<div id="obsah"><p>Žádní uživatelé neodpovídají výběru.</p></div>';
}
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>
