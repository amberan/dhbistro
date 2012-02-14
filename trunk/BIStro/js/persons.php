<?php
	require_once ('./inc/func_main.php');
	pageStart ('Osoby');
	mainMenu (5);
	sparklets ('<strong>osoby</strong>','<a href="newperson.php">přidat osobu</a>');
	// zpracovani filtru
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	if (!isset($_POST['sportraits'])) {
		$sportraits=false;
	} else {
		$sportraits=$_POST['sportraits'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC ';
	}
	//
	function filter () {
		global $f_sort, $sportraits;
	  echo '<form action="persons.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>příjmení a jména sestupně</option>
</select>.</p>
		<p><input type="checkbox" name="sportraits" value="1"'.(($sportraits)?' checked="checked"':'').'> zobrazit portréty</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form>';
	}
	filter();
	// vypis osob
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id' FROM ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.deleted=0 ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id' FROM ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
'.(($sportraits)?'<th>Portrét</th>':'').'
	  <th>Jméno</th>
		<th>Telefon</th>
'.(($usrinfo['right_text'])?'	  <th>Akce</th>':'').'
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
'.(($sportraits)?'<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>':'').'
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a></span>':'<a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a>').'</td>
	<td>'.$rec['phone'].'</td>
'.(($usrinfo['right_text'])?'	<td><a href="editperson.php?rid='.$rec['id'].'">upravit</a> | <a href="procperson.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat osobu &quot;".implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name'])))."&quot;?');".'">smazat</a></td>':'').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
	pageEnd ();
?>