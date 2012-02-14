<?php
	require_once ('./inc/func_main.php');
	pageStart ('Skupiny');
	mainMenu (3);
	sparklets ('<strong>skupiny</strong>','<a href="newgroup.php">přidat skupinu</a>');
	// zpracovani filtru
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'groups.title ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'groups.title DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'groups.title ASC ';
	}
	//
	function filter () {
	  global $f_sort;
	  echo '<form action="groups.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny skupiny a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>názvu vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>názvu sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form>';
	}
	filter();
	// vypis skupin
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id' FROM ".DB_PREFIX."groups WHERE ".DB_PREFIX."groups.deleted=0 ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id' FROM ".DB_PREFIX."groups WHERE ".DB_PREFIX."groups.deleted=0 AND ".DB_PREFIX."groups.secret=0 ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název</th>
'.(($usrinfo['right_text'])?'	  <th>Akce</th>':'').'
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readgroup.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a></span>':'<a href="readgroup.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a>').'</td>
'.(($usrinfo['right_text'])?'	<td><a href="editgroup.php?rid='.$rec['id'].'">upravit</a> | <a href="procgroup.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat skupinu &quot;".StripSlashes($rec['title'])."&quot;?');".'">smazat</a></td>':'').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>
';
	} else {
	  echo '<div id="obsah"><p>Žádné skupiny neodpovídají výběru.</p></div>';
	}
	pageEnd ();
?>