<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Osoby';
  
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latte->render($config['folder_templates'].'header.latte', $latteParameters);

	auditTrail(1, 1, 0);
	mainMenu ();
        $custom_Filter = custom_Filter(1);
	sparklets ('<strong>osoby</strong>','<a href="newperson.php">přidat osobu</a>; <a href="symbols.php" '.((searchTable(7)) ? ' class="unread"' : '').'>nepřiřazené symboly</a>; <a href="symbol_search.php">vyhledat symbol</a>');
	// zpracovani filtru
	if (!isset($custom_Filter['sort'])) {
	    $f_sort = 1;
	} else {
	    $f_sort = $custom_Filter['sort'];
	}
	if (!isset($custom_Filter['sportraits'])) {
	    $sportraits = false;
	} else {
	    $sportraits = $custom_Filter['sportraits'];
	}
	if (!isset($custom_Filter['ssymbols'])) {
	    $ssymbols = false;
	} else {
	    $ssymbols = $custom_Filter['ssymbols'];
	}
	if (!isset($custom_Filter['fdead'])) {
	    $fdead = 0;
	} else {
	    $fdead = 1;
	}
	if (!isset($custom_Filter['farchiv'])) {
	    $farchiv = 0;
	} else {
	    $farchiv = 1;
	}
	if (!isset($custom_Filter['sec'])) {
	    $f_sec = 0;
	} else {
	    $f_sec = 1;
	}
        if (!isset($custom_Filter['new'])) {
            $f_new = 0;
        } else {
            $f_new = 1;
        }
	if (!isset($custom_Filter['fspec'])) {
	    $fspec = 0;
	} else {
	    $fspec = $custom_Filter['fspec'];
	}
	if (!isset($custom_Filter['fside'])) {
	    $fside = 0;
	} else {
	    $fside = $custom_Filter['fside'];
	}
	if (!isset($custom_Filter['fpow'])) {
	    $fpow = 0;
	} else {
	    $fpow = $custom_Filter['fpow'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort = ' '.DB_PREFIX.'person.surname ASC, '.DB_PREFIX.'person.name ASC '; break;
	  case 2: $fsql_sort = ' '.DB_PREFIX.'person.surname DESC, '.DB_PREFIX.'person.name DESC '; break;
	  default: $fsql_sort = ' '.DB_PREFIX.'person.surname ASC, '.DB_PREFIX.'person.name ASC ';
	}
	switch ($f_sec) {
		case 0: $fsql_sec = ''; break;
		case 1: $fsql_sec = ' AND '.DB_PREFIX.'person.secret=1 '; break;
		default: $fsql_sec = '';
	}
	switch ($fdead) {
		case 0: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 '; break;
		case 1: $fsql_dead = ''; break;
		default: $fsql_dead = ' AND '.DB_PREFIX.'person.dead=0 ';
	}
	switch ($farchiv) {
		case 0: $fsql_archiv = ' AND '.DB_PREFIX.'person.archiv=0 '; break;
		case 1: $fsql_archiv = ''; break;
		default: $fsql_archiv = ' AND '.DB_PREFIX.'person.archiv=0 ';
	}
		switch ($fspec) {
		case 0: $fsql_fspec = ''; break;
		case 1: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=0 '; break;
		case 2: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=1 '; break;
		case 3: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=2 '; break;
		case 4: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=3 '; break;
		case 5: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=4 '; break;
		case 6: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=5 '; break;
		case 7: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=6 '; break;
		case 8: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=7 '; break;
		case 9: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=8 '; break;
		case 10: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=9 '; break;
		case 11: $fsql_fspec = ' AND '.DB_PREFIX.'person.spec=10 '; break;
		default: $fsql_fspec = '';
	}
	switch ($fside) {
		case 0: $fsql_fside = ''; break;
		case 1: $fsql_fside = ' AND '.DB_PREFIX.'person.side=0 '; break;
		case 2: $fsql_fside = ' AND '.DB_PREFIX.'person.side=1 '; break;
		case 3: $fsql_fside = ' AND '.DB_PREFIX.'person.side=2 '; break;
		case 4: $fsql_fside = ' AND '.DB_PREFIX.'person.side=3 '; break;
		default: $fsql_fside = '';
	}
	switch ($fpow) {
		case 0: $fsql_fpow = ''; break;
		case 1: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=0 '; break;
		case 2: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=1 '; break;
		case 3: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=2 '; break;
		case 4: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=3 '; break;
		case 5: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=4 '; break;
		case 6: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=5 '; break;
		case 7: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=6 '; break;
		case 8: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=7 '; break;
		case 9: $fsql_fpow = ' AND '.DB_PREFIX.'person.power=8 '; break;
		default: $fsql_fpow = '';
	}
	// formular filtru
	function filter ()
	{
	    global $f_sort, $sportraits, $ssymbols, $f_sec, $f_new, $fdead, $farchiv, $usrinfo, $fspec, $fside, $fpow;
	    echo '<div id="filter-wrapper"><form action="persons.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort == 1) ? ' selected="selected"' : '').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($f_sort == 2) ? ' selected="selected"' : '').'>příjmení a jména sestupně</option>
	</select>.</p>
	<p> Strana: 
	<select name="fside">
	<option value="0"'.(($fside == 0) ? ' selected="selected"' : '').'>vše</option>
	<option value="1"'.(($fside == 1) ? ' selected="selected"' : '').'>neznámá</option>
	<option value="2"'.(($fside == 2) ? ' selected="selected"' : '').'>světlo</option>
	<option value="3"'.(($fside == 3) ? ' selected="selected"' : '').'>tma</option>
	<option value="4"'.(($fside == 4) ? ' selected="selected"' : '').'>člověk</option>
	</select>
	 Specializace: 
	<select name="fspec">
	<option value="0"'.(($fspec == 0) ? ' selected="selected"' : '').'>vše</option>
	<option value="1"'.(($fspec == 1) ? ' selected="selected"' : '').'>neznámá</option>
	<option value="2"'.(($fspec == 2) ? ' selected="selected"' : '').'>bílý mág</option>
	<option value="3"'.(($fspec == 3) ? ' selected="selected"' : '').'>černý mág</option>
	<option value="4"'.(($fspec == 4) ? ' selected="selected"' : '').'>léčitel</option>
	<option value="5"'.(($fspec == 5) ? ' selected="selected"' : '').'>obrateň</option>
	<option value="6"'.(($fspec == 6) ? ' selected="selected"' : '').'>upír</option>
	<option value="7"'.(($fspec == 7) ? ' selected="selected"' : '').'>vlkodlak</option>
	<option value="8"'.(($fspec == 8) ? ' selected="selected"' : '').'>vědma</option>
	<option value="9"'.(($fspec == 9) ? ' selected="selected"' : '').'>zaříkávač</option>
	<option value="10"'.(($fspec == 10) ? ' selected="selected"' : '').'>vykladač</option>
	<option value="11"'.(($fspec == 11) ? ' selected="selected"' : '').'>jasnovidec</option>
	</select>
	 Kategorie: 
	<select name="fpow">
	<option value="0"'.(($fpow == 0) ? ' selected="selected"' : '').'>vše</option>
	<option value="1"'.(($fpow == 1) ? ' selected="selected"' : '').'>neznámá</option>
	<option value="2"'.(($fpow == 2) ? ' selected="selected"' : '').'>první</option>
	<option value="3"'.(($fpow == 3) ? ' selected="selected"' : '').'>druhá</option>
	<option value="4"'.(($fpow == 4) ? ' selected="selected"' : '').'>třetí</option>
	<option value="5"'.(($fpow == 5) ? ' selected="selected"' : '').'>čtvrtá</option>
	<option value="6"'.(($fpow == 6) ? ' selected="selected"' : '').'>pátá</option>
	<option value="7"'.(($fpow == 7) ? ' selected="selected"' : '').'>šestá</option>
	<option value="8"'.(($fpow == 8) ? ' selected="selected"' : '').'>sedmá</option>
	<option value="9"'.(($fpow == 9) ? ' selected="selected"' : '').'>mimo kategorie</option>
	</select></p>
	
	<table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="sportraits" value="1"'.(($sportraits) ? ' checked="checked"' : '').'> Zobrazit portréty.</td>
	<td class="filter"><input type="checkbox" name="fdead" value="1"'.(($fdead == 1) ? ' checked="checked"' : '').'> Zobrazit i mrtvé.</td>
        <td class="filter"><input type="checkbox" name="new" value="1"'.(($f_new == 1) ? ' checked="checked"' : '').'> Zobrazit jen nové.</td>
	</tr>
        <tr class="filter">
	<td class="filter"><input type="checkbox" name="ssymbols" value="1"'.(($ssymbols) ? ' checked="checked"' : '').'> Zobrazit symboly.</td>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.(($farchiv == 1) ? ' checked="checked"' : '').'> Zobrazit i archiv.</td>';
	    if ($usrinfo['right_power']) {
	        echo '<td class="filter"><input type="checkbox" name="sec" value="sec" class="checkbox"'.(($f_sec == 1) ? ' checked="checked"' : '').' /> Jen tajné.</td></tr></table>';
	    } else {
	        echo '</tr></table>';
	    }
	    echo '
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	/* Stary vypis osob
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.archiv AS 'archiv', ".DB_PREFIX."person.dead AS 'dead', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol' FROM ".DB_PREFIX."person WHERE ".DB_PREFIX."person.deleted=0".$fsql_sec.$fsql_dead.$fsql_archiv.$fsql_fspec.$fsql_fside.$fsql_fpow." ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.archiv AS 'archiv', ".DB_PREFIX."person.dead AS 'dead', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol' FROM ".DB_PREFIX."person WHERE ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret=0".$fsql_sec.$fsql_dead.$fsql_archiv.$fsql_fspec.$fsql_fside.$fsql_fpow." ORDER BY ".$fsql_sort;
	}
	Alternativni vypis osob zahrnujici vice stupnu tajne.*/
    $sql = "SELECT  ".DB_PREFIX."person.regdate as date_created, ".DB_PREFIX."person.datum as date_changed, ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.archiv AS 'archiv', ".DB_PREFIX."person.dead AS 'dead', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.symbol AS 'symbol' FROM ".DB_PREFIX."person WHERE ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret<=".$usrinfo['right_power'].$fsql_sec.$fsql_dead.$fsql_archiv.$fsql_fspec.$fsql_fside.$fsql_fpow." ORDER BY ".$fsql_sort;
    //Debugger::log('DEBUG: '.$sql);
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
'.(($sportraits) ? '<th>Portrét</th>' : '').
(($ssymbols) ? '<th>Symbol</th>' : '').'
	  <th>Jméno</th>
	  <th>Telefon</th>
	  <th>Vytvořeno / Změněno</th>
      <th style="min-width:100px">Status</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        if ($f_new == 0 || ($f_new == 1 && searchRecord(1,$rec['id']))) {
	            echo '<tr class="'.((searchRecord(1,$rec['id'])) ? ' unread_record' : (($even % 2 == 0) ? 'even' : 'odd')).'">
                        '.(($sportraits) ? '<td><img src="getportrait.php?rid='.$rec['id'].'" alt="" /></td>' : '').'
                        '.(($ssymbols) ? '<td><img src="getportrait.php?nrid='.$rec['symbol'].'" alt="" /></td>' : '').'
                        <td>'.(($rec['secret']) ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name']))).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name']))).'</a>').'</td>
						<td><a href="tel:'.str_replace(' ', '',$rec['phone']).'">'.$rec['phone'].'</a></td>
						<td>'.webdate($rec['date_created']).' / '.webdate($rec['date_changed']).'</td>
                        <td>'.(($rec['archiv'] == 1) ? 'Archivovaný' : '').''.(($rec['dead'] == 1) ? ' Mrtvý' : '').''.(($rec['secret'] == 1) ? ' Tajný' : '').'</td>
                        '.(($usrinfo['right_text']) ? '	<td><a href="editperson.php?rid='.$rec['id'].'">upravit</a> | <a href="procperson.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat osobu &quot;".implode(', ',Array(StripSlashes($rec['surname']), StripSlashes($rec['name'])))."&quot;?');".'">smazat</a></td>' : '<td><a href="newnote.php?rid='.$rec['id'].'&idtable=5">přidat poznámku</a>').'
                        </tr>';
	            $even++;
	        }
	    }
	    echo '</tbody>
</table>
</div>';
	}
	$latte->render($config['folder_templates'].'footer.latte', $latteParameters);
?>
