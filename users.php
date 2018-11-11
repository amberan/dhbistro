<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
if ($usrinfo['right_power']<5) {
	unauthorizedAccess(8, 1, 0, 0);
	$_SESSION['message'] = "";
} else {
	auditTrail(8, 1, 0);
	pageStart ('Uživatelé');
	mainMenu (2);
    $custom_Filter = custom_Filter(8);
	sparklets ('<strong>uživatelé</strong>',(($usrinfo['right_power'])?'<a href="tasks.php">úkoly</a>; <a href="newuser.php">přidat uživatele</a>':'<a href="tasks.php">úkoly</a>'));
	// zpracovani filtru
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
	//
	function filter () {
	  global $database,$f_cat,$f_sort;
	  echo '<div id="filter-wrapper"><form action="/users.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechny uživatele</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>power usery</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>uživatele s právem změn</option>
</select> a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>jména vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>jména sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis uživatelů
        if ($usrinfo['right_org']) {
            $sql="SELECT * FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.deleted=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
        } else {
            $sql="SELECT * FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.deleted=0 AND ".DB_PREFIX."users.right_org=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
        }
	$res=mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Login</th>
	  <th>Poslední přihlášení</th>
	  <th>Power user</th>
	  <th>Editace hlavních textů</th>
	  '.(($usrinfo['right_org'])?'<th>Organizátor</th>':'').'
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=mysqli_fetch_assoc ($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td>'.StripSlashes($rec['login']).'</td>
	<td>'.(($rec['lastlogon'])?Date ('d. m. Y (H:i:s)',$rec['lastlogon']):'nikdy').'</td>
	<td>'.(($rec['right_power'])?'ano':'ne').'</td>
	<td>'.(($rec['right_text'])?'ano':'ne').'</td>
	'.(($usrinfo['right_org'])?'<td>'.(($rec['right_org'])?'ano':'ne').'</td>':'').'
	<td><a class="button" href="edituser.php?rid='.$rec['id'].'">upravit</a>';
			if ($rec['id'] != $usrinfo['id']) {
				echo '  <a class="button" href="users.php?user_reset='.$rec['id'].'" onclick="'."return confirm('Opravdu vygenerovat nové heslo pro uživatele &quot;".$rec['login']."&quot;?');".'">nové heslo</a>';
				if ($rec['suspended'] == "1") {
					echo '  <a class="button" href="users.php?user_unlock='.$rec['id'].'" onclick="'."return confirm('Opravdu odemknout uživatele &quot;".$rec['login']."&quot;?');".'">odemknout</a>';
				} else {
					echo '  <a class="button" href="users.php?user_lock='.$rec['id'].'" onclick="'."return confirm('Opravdu zamknout uživatele &quot;".$rec['login']."&quot;?');".'">zamknout</a>';
				}
			echo '  <a class="button" href="users.php?user_delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat uživatele &quot;".$rec['login']."&quot;?');".'">smazat</a></td>';
			}
		echo '</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>
';
	} else {
	  echo '<div id="obsah"><p>Žádní uživatelé neodpovídají výběru.</p></div>';
	}
}
?>
<?php
	pageEnd ();
?>
