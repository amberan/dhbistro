<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);

$latteParameters['title'] = 'Úprava uživatele';

if ($usrinfo['right_power']<1) {
	unauthorizedAccess(8, 1, 0, 0);
	$_SESSION['message'] = "";
}
if (isset($_REQUEST['user_edit']) AND is_numeric($_REQUEST['user_edit'])) {
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
	mainMenu (2);
	sparklets ('<a href="./users.php">uživatelé</a> &raquo; <strong>úprava uživatele</strong>');
} elseif (isset($_REQUEST['user_new']) AND $_REQUEST['user_new'] == true ) {
	
$latteParameters['title'] = ('Nový uživatel');
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

	mainMenu (2);
	sparklets ('<a href="./users.php">uživatelé</a> &raquo; <strong>nový uživatel</strong>');
} else {
	auditTrail(8, 1, 0);

$latteParameters['title'] = ('Uživatelé');
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

	mainMenu (2);
	$custom_Filter = custom_Filter(8);
	sparklets ('<strong>uživatelé</strong>',(($usrinfo['right_power'])?'<a href="tasks.php">úkoly</a>; <a href="users.php?user_new=true">přidat uživatele</a>':'<a href="tasks.php">úkoly</a>'));
}


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
	case 1: $fsql_cat=' AND '.DB_PREFIX.'users.right_power=1 '; break;
	case 2: $fsql_cat=' AND '.DB_PREFIX.'users.right_text=1 '; break;
	default: $fsql_cat='';
}
switch ($f_sort) {
	case 1: $fsql_sort=' '.DB_PREFIX.'users.login ASC '; break;
	case 2: $fsql_sort=' '.DB_PREFIX.'users.login DESC '; break;
	default: $fsql_sort=' '.DB_PREFIX.'users.login ASC ';
}
function filter () {
    global $database,$f_cat,$f_sort;
	echo 
'<div id="filtr" class="table">
	<form action="/users.php" method="get">
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



if (isset($_REQUEST['user_edit']) AND is_numeric($_REQUEST['user_edit'])) {
	include ('usersedit.php');
} elseif (isset($_REQUEST['user_new']) AND $_REQUEST['user_new'] == true ) {
	include ('usersnew.php');
}
// *** vypis uživatelů
echo '<h1 class="center">Výpis uživatelů</h1>';
filter();
if ($usrinfo['right_org']) {
	$user_sql="SELECT nw_users.*,nw_persons.name,nw_persons.surname FROM ".DB_PREFIX."users left outer join `nw_persons` on nw_users.idperson=nw_persons.id WHERE ".DB_PREFIX."users.deleted=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
} else {
	$user_sql="SELECT nw_users.*,nw_persons.name,nw_persons.surname FROM ".DB_PREFIX."users left outer join `nw_persons` on nw_users.idperson=nw_persons.id WHERE ".DB_PREFIX."users.deleted=0 AND ".DB_PREFIX."users.right_org=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
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
				<a class="button" href="users.php?user_edit=<?php echo $user_record['id']?>">upravit</a>
	<?php	
		if ($user_record['id'] != $usrinfo['id']) {
				echo '<a class="button" href="users.php?user_reset='.$user_record['id'].'" onclick="'."return confirm('Opravdu vygenerovat nové heslo pro uživatele &quot;".$user_record['login']."&quot;?');".'">nové heslo</a>';
			if ($user_record['suspended'] == "1") {
				echo '<a class="button" href="users.php?user_unlock='.$user_record['id'].'" onclick="'."return confirm('Opravdu odemknout uživatele &quot;".$user_record['login']."&quot;?');".'">odemknout</a>';
			} else {
				echo '<a class="button" href="users.php?user_lock='.$user_record['id'].'" onclick="'."return confirm('Opravdu zamknout uživatele &quot;".$user_record['login']."&quot;?');".'">zamknout</a>';
			}
			echo '<a class="button" href="users.php?user_delete='.$user_record['id'].'" onclick="'."return confirm('Opravdu smazat uživatele &quot;".$user_record['login']."&quot;?');".'">smazat</a>';
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