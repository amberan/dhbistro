<?php
	require_once ('./inc/func_main.php');
	auditTrail(3, 1, 0);
	pageStart ('Skupiny');
	mainMenu (3);
	sparklets ('<strong>skupiny</strong>','<a href="newgroup.php">přidat skupinu</a>');
	// zpracovani filtru
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	if (!isset($_REQUEST['sec'])) {
		$f_sec=0;
	} else {
		$f_sec=1;
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'groups.title ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'groups.title DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'groups.title ASC ';
	}
	switch ($f_sec) {
		case 0: $fsql_sec=''; break;
		case 1: $fsql_sec=' AND '.DB_PREFIX.'groups.secret=1 '; break;
		default: $fsql_sec='';
	}
	//
	function filter () {
	  global $f_sort, $f_sec, $usrinfo;
	  echo '<div id="filter-wrapper"><form action="groups.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny skupiny a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>názvu vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>názvu sestupně</option>
</select>.';
	if ($usrinfo['right_power']) {
		echo '<br /> <input type="checkbox" name="sec" value="sec" class="checkbox"'.(($f_sec==1)?' checked="checked"':'').' /> Jen tajné.</p>';
	}  else {
		echo '</p>';
	}
	echo '
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis skupin
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id' FROM ".DB_PREFIX."groups WHERE ".DB_PREFIX."groups.deleted=0".$fsql_sec." ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id' FROM ".DB_PREFIX."groups WHERE ".DB_PREFIX."groups.deleted=0".$fsql_sec." AND ".DB_PREFIX."groups.secret=0 ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.((searchRecord(2,$rec['id']))?' unread_record':(($even%2==0)?'even':'odd')).'">
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></span>':'<a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a>').'</td>
'.(($usrinfo['right_text'])?'	<td><a href="editgroup.php?rid='.$rec['id'].'">upravit</a> | <a href="procgroup.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat skupinu &quot;".StripSlashes($rec['title'])."&quot;?');".'">smazat</a></td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=6">přidat poznámku</a></td>').'
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