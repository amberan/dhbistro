<?php
	require_once ('./inc/func_main.php');
	pageStart ('Případy');
	mainMenu (4);
	sparklets ('<strong>případy</strong>','<a href="newcase.php">přidat případ</a>');
	// zpracovani filtru
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'cases.title ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'cases.title DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'cases.title ASC ';
	}
	//
	function filter () {
	  global $f_sort;
	  echo '<form action="cases.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny případy a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>názvu vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>názvu sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form>';
	}
	filter();
	// vypis pripadu
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."cases.status AS 'status', ".DB_PREFIX."cases.secret AS 'secret', ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id' FROM ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.deleted=0 ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."cases.status AS 'status', ".DB_PREFIX."cases.secret AS 'secret', ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id' FROM ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.deleted=0 AND ".DB_PREFIX."cases.secret=0 ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název</th>
	  <th>Stav</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').(($rec['status'])?' solved':'').'">
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></span>':'<a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a>').'</td>
	<td>'.(($rec['status'])?'uzavřený':'otevřený').'</td>
'.(($usrinfo['right_text'])?'	<td><a href="editcase.php?rid='.$rec['id'].'">upravit</a> | <a href="proccase.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat případ &quot;".StripSlashes($rec['title'])."&quot;?');".'">smazat</a></td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=3">přidat poznámku</a></td>').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>
';
	} else {
	  echo '<div id="obsah"><p>Žádné případy neodpovídají výběru.</p></div>';
	}
	pageEnd ();
?>