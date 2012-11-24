<?php
	require_once ('./inc/func_main.php');
	auditTrail(8, 1, 0);
	pageStart ('Uživatelé');
	mainMenu (2);
	sparklets ('<strong>uživatelé</strong>',(($usrinfo['right_power'])?'<a href="tasks.php">úkoly</a>; <a href="newuser.php">přidat uživatele</a>':'<a href="tasks.php">úkoly</a>'));
	// zpracovani filtru
	if (!isset($_REQUEST['kategorie'])) {
	  $f_cat=0;
	} else {
	  $f_cat=$_REQUEST['kategorie'];
	}
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
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
	  global $f_cat;
		global $f_sort;
	  echo '<div id="filter-wrapper"><form action="users.php" method="post" id="filter">
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
	$sql="SELECT * FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.deleted=0 ".$fsql_cat." ORDER BY ".$fsql_sort;
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
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
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td>'.StripSlashes($rec['login']).'</td>
	<td>'.(($rec['lastlogon'])?Date ('d. m. Y (H:i:s)',$rec['lastlogon']):'nikdy').'</td>
	<td>'.(($rec['right_power'])?'ano':'ne').'</td>
	<td>'.(($rec['right_text'])?'ano':'ne').'</td>
	'.(($usrinfo['right_org'])?'<td>'.(($rec['right_org'])?'ano':'ne').'</td>':'').'
	<td><a href="edituser.php?rid='.$rec['id'].'">upravit</a> | <a href="procuser.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat uživatele &quot;".$rec['login']."&quot;?');".'">smazat</a></td>
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>
';
	} else {
	  echo '<div id="obsah"><p>Žádní uživatelé neodpovídají výběru.</p></div>';
	}
?>
<?php
	pageEnd ();
?>
