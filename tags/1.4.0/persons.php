<?php
	require_once ('./inc/func_main.php');
	auditTrail(1, 1, 0);
	pageStart ('Osoby');
	mainMenu (5);
	sparklets ('<strong>osoby</strong>','<a href="newperson.php">přidat osobu</a>; <a href="symbols.php">nepřiřazené symboly</a>');
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
	if (!isset($_POST['ssymbols'])) {
		$ssymbols=false;
	} else {
		$ssymbols=$_POST['ssymbols'];
	}
	if (!isset($_POST['fdead'])) {
		$fdead=0;
	} else {
		$fdead=1;
	}
	if (!isset($_POST['farchiv'])) {
		$farchiv=0;
	} else {
		$farchiv=1;
	}
	if (!isset($_REQUEST['sec'])) {
		$f_sec=0;
	} else {
		$f_sec=1;
	}
	if (!isset($_REQUEST['fspec'])) {
		$fspec=0;
	} else {
		$fspec=$_REQUEST['fspec'];
	}
	if (!isset($_REQUEST['fside'])) {
		$fside=0;
	} else {
		$fside=$_REQUEST['fside'];
	}
	if (!isset($_REQUEST['fpow'])) {
		$fpow=0;
	} else {
		$fpow=$_REQUEST['fpow'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC ';
	}
	switch ($f_sec) {
		case 0: $fsql_sec=''; break;
		case 1: $fsql_sec=' AND '.DB_PREFIX.'persons.secret=1 '; break;
		default: $fsql_sec='';
	}
	switch ($fdead) {
		case 0: $fsql_dead=' AND '.DB_PREFIX.'persons.dead=0 '; break;
		case 1: $fsql_dead=''; break;
		default: $fsql_dead=' AND '.DB_PREFIX.'persons.dead=0 ';
	}
	switch ($farchiv) {
		case 0: $fsql_archiv=' AND '.DB_PREFIX.'persons.archiv=0 '; break;
		case 1: $fsql_archiv=''; break;
		default: $fsql_archiv=' AND '.DB_PREFIX.'persons.archiv=0 ';
	}
		switch ($fspec) {
		case 0: $fsql_fspec=''; break;
		case 1: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=0 '; break;
		case 2: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=1 '; break;
		case 3: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=2 '; break;
		case 4: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=3 '; break;
		case 5: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=4 '; break;
		case 6: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=5 '; break;
		case 7: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=6 '; break;
		case 8: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=7 '; break;
		case 9: $fsql_fspec=' AND '.DB_PREFIX.'persons.spec=8 '; break;
		default: $fsql_fspec='';
	}
	switch ($fside) {
		case 0: $fsql_fside=''; break;
		case 1: $fsql_fside=' AND '.DB_PREFIX.'persons.side=0 '; break;
		case 2: $fsql_fside=' AND '.DB_PREFIX.'persons.side=1 '; break;
		case 3: $fsql_fside=' AND '.DB_PREFIX.'persons.side=2 '; break;
		case 4: $fsql_fside=' AND '.DB_PREFIX.'persons.side=3 '; break;
		default: $fsql_fside='';
	}
	switch ($fpow) {
		case 0: $fsql_fpow=''; break;
		case 1: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=0 '; break;
		case 2: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=1 '; break;
		case 3: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=2 '; break;
		case 4: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=3 '; break;
		case 5: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=4 '; break;
		case 6: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=5 '; break;
		case 7: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=6 '; break;
		case 8: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=7 '; break;
		case 9: $fsql_fpow=' AND '.DB_PREFIX.'persons.power=8 '; break;
		default: $fsql_fpow='';
	}
	// formular filtru
	function filter () {
		global $f_sort, $sportraits, $ssymbols, $f_sec, $fdead, $farchiv, $usrinfo, $fspec, $fside, $fpow;
	  echo '<div id="filter-wrapper"><form action="persons.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>příjmení a jména sestupně</option>
	</select>.</p>
	<p> Strana: 
	<select name="fside">
	<option value="0"'.(($fside==0)?' selected="selected"':'').'>vše</option>
	<option value="1"'.(($fside==1)?' selected="selected"':'').'>neznámá</option>
	<option value="2"'.(($fside==2)?' selected="selected"':'').'>světlo</option>
	<option value="3"'.(($fside==3)?' selected="selected"':'').'>tma</option>
	<option value="4"'.(($fside==4)?' selected="selected"':'').'>člověk</option>
	</select>
	 Specializace: 
	<select name="fspec">
	<option value="0"'.(($fspec==0)?' selected="selected"':'').'>vše</option>
	<option value="1"'.(($fspec==1)?' selected="selected"':'').'>neznámá</option>
	<option value="2"'.(($fspec==2)?' selected="selected"':'').'>bílý mág</option>
	<option value="3"'.(($fspec==3)?' selected="selected"':'').'>černý mág</option>
	<option value="4"'.(($fspec==4)?' selected="selected"':'').'>léčitel</option>
	<option value="5"'.(($fspec==5)?' selected="selected"':'').'>obrateň</option>
	<option value="6"'.(($fspec==6)?' selected="selected"':'').'>upír</option>
	<option value="7"'.(($fspec==7)?' selected="selected"':'').'>vlkodlak</option>
	<option value="8"'.(($fspec==8)?' selected="selected"':'').'>vědma</option>
	<option value="9"'.(($fspec==9)?' selected="selected"':'').'>zaříkávač</option>
	</select>
	 Kategorie: 
	<select name="fpow">
	<option value="0"'.(($fpow==0)?' selected="selected"':'').'>vše</option>
	<option value="1"'.(($fpow==1)?' selected="selected"':'').'>neznámá</option>
	<option value="2"'.(($fpow==2)?' selected="selected"':'').'>první</option>
	<option value="3"'.(($fpow==3)?' selected="selected"':'').'>druhá</option>
	<option value="4"'.(($fpow==4)?' selected="selected"':'').'>třetí</option>
	<option value="5"'.(($fpow==5)?' selected="selected"':'').'>čtvrtá</option>
	<option value="6"'.(($fpow==6)?' selected="selected"':'').'>pátá</option>
	<option value="7"'.(($fpow==7)?' selected="selected"':'').'>šestá</option>
	<option value="8"'.(($fpow==8)?' selected="selected"':'').'>sedmá</option>
	<option value="9"'.(($fpow==9)?' selected="selected"':'').'>mimo kategorie</option>
	</select></p>
	
	<table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="sportraits" value="1"'.(($sportraits)?' checked="checked"':'').'> Zobrazit portréty.</td>
	<td class="filter"><input type="checkbox" name="fdead" value="1"'.(($fdead==1)?' checked="checked"':'').'> Zobrazit i mrtvé.</td>
	</tr>
	<td class="filter"><input type="checkbox" name="ssymbols" value="1"'.(($ssymbols)?' checked="checked"':'').'> Zobrazit symboly.</td>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.(($farchiv==1)?' checked="checked"':'').'> Zobrazit i archiv.</td>
	</tr>';
	if ($usrinfo['right_power']) {
		echo '<td class="filter"><input type="checkbox" name="sec" value="sec" class="checkbox"'.(($f_sec==1)?' checked="checked"':'').' /> Jen tajné.</td></tr></table>';
	}  else {
		echo '</table>';
	}
	echo '
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis osob
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id' FROM ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.deleted=0".$fsql_sec.$fsql_dead.$fsql_archiv.$fsql_fspec.$fsql_fside.$fsql_fpow." ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id' FROM ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0".$fsql_sec.$fsql_dead.$fsql_archiv.$fsql_fspec.$fsql_fside.$fsql_fpow." ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
'.(($sportraits)?'<th>Portrét</th>':'').
(($ssymbols)?'<th>Symbol</th>':'').'
	  <th>Jméno</th>
	  <th>Telefon</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.((searchRecord(1,$rec['id']))?' unread_record':(($even%2==0)?'even':'odd')).'">
'.(($sportraits)?'<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>':'').'
'.(($ssymbols)?'<td><img src="getportrait.php?srid='.$rec['id'].'" alt="symbol chybí" /></td>':'').'
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a></span>':'<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a>').'</td>
	<td>'.$rec['phone'].'</td>
'.(($usrinfo['right_text'])?'	<td><a href="editperson.php?rid='.$rec['id'].'">upravit</a> | <a href="procperson.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat osobu &quot;".implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name'])))."&quot;?');".'">smazat</a></td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=5">přidat poznámku</a>').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
	pageEnd ();
?>