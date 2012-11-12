<?php
	require_once ('./inc/func_main.php');
	pageStart ($point.'y');
	mainMenu (2);
	sparklets ('<strong>'.$point.'y</strong>',(($usrinfo['right_power'])?'aktuální stav':''));
	//Přidání zlobodů
	if (isset($_POST['addpoints'])) {
		if (is_numeric($_POST['plus'])) {
			$ep_result=$_POST['oldpoints']+$_POST['plus'];
			MySQL_Query ("UPDATE ".DB_PREFIX."users SET zlobody=".$ep_result." WHERE id=".$_POST['usrid']."");
			echo '<div id="obsah"><p>Zlobody přidány.</p></div>';
		} else {
			echo '<div id="obsah"><p>Přidané '.$point.'y musí být číselné.</p></div>';
		}
	}
	
	// zpracovani filtru
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'users.login ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'users.login DESC '; break;
	  case 3: $fsql_sort=' '.DB_PREFIX.'users.zlobody ASC '; break;
	  case 4: $fsql_sort=' '.DB_PREFIX.'users.zlobody DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'users.login ASC ';
	}
	// Filtr
	function filter () {
	  global $f_cat, $point;
		global $f_sort;
	  echo '<form action="evilpoints.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny uživatele a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>jména vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>jména sestupně</option>
	<option value="3"'.(($f_sort==3)?' selected="selected"':'').'>'.$point.'ů vzestupně</option>
	<option value="4"'.(($f_sort==4)?' selected="selected"':'').'>'.$point.'ů sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form>';
	}
	filter();
	// vypis uživatelů
	$sql="SELECT * FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.deleted=0 ORDER BY ".$fsql_sort;
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Kódové označení</th>
	  <th>Aktuální počet '.$point.'ů</th>
	  '.(($usrinfo['right_power'])?'	  <th>Akce</th>':'').'
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td>'.StripSlashes($rec['login']).'</td>
	<td>'.($rec['zlobody']).'</td>
	'.(($usrinfo['right_power'])?'<td>
			<form action="evilpoints.php" method="post" id="inputform" class="evilform">
			<input class="plus" type="text" name="plus" id="plus" />
			<input type="hidden" name="usrid" value="'.($rec['id']).'" />
			<input type="hidden" name="oldpoints" value="'.($rec['zlobody']).'" />
			<input type="submit" name="addpoints" value="Přidat" />
			</form>
	</td>':'').'
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
