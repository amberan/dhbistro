<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Případy';

	auditTrail(3, 1, 0);
	mainMenu ();
        $customFilter = custom_Filter(3);
	sparklets ('<strong>případy</strong>','<a href="newcase.php">přidat případ</a>');
	// zpracovani filtru
	if (!isset($customFilter['sort'])) {
	    $filterSort = 4;
	} else {
	    $filterSort = $customFilter['sort'];
	}
	if (!isset($customFilter['stat'])) {
	    $filterStat = 0;
	} else {
	    $filterStat = 1;
	}
	if (!isset($customFilter['sec'])) {
	    $filterSec = 0;
	} else {
	    $filterSec = 1;
	}
        if (!isset($customFilter['new'])) {
            $f_new = 0;
        } else {
            $f_new = 1;
        }
	switch ($filterSort) {
	  case 1: $filterSqlSort = ' '.DB_PREFIX.'case.title ASC '; break;
	  case 2: $filterSqlSort = ' '.DB_PREFIX.'case.title DESC '; break;
	  case 3: $filterSqlSort = ' '.DB_PREFIX.'case.datum ASC '; break;
	  case 4: $filterSqlSort = ' '.DB_PREFIX.'case.datum DESC '; break;
	  default: $filterSqlSort = ' '.DB_PREFIX.'case.datum DESC ';
	}
	switch ($filterSec) {
		case 0: $fsql_sec = ''; break;
		case 1: $fsql_sec = ' AND '.DB_PREFIX.'case.secret>0 '; break;
		default: $fsql_sec = '';
	}
	switch ($filterStat) {
		case 0: $fsql_stat = ' AND '.DB_PREFIX.'case.status=0 '; break;
		case 1: $fsql_stat = ''; break;
		default: $fsql_stat = ' AND '.DB_PREFIX.'case.status=0 ';
	}
	//
	function filter ()
	{
	    global $filterSort, $filterSec, $filterStat, $f_new, $usrinfo;
	    echo '<div id="filter-wrapper"><form action="cases.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny případy a seřadit je podle <select name="sort">
	<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>názvu vzestupně</option>
	<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>názvu sestupně</option>
	<option value="3"'.(($filterSort == 3) ? ' selected="selected"' : '').'>data vzestupně</option>
	<option value="4"'.(($filterSort == 4) ? ' selected="selected"' : '').'>data sestupně</option>
</select>.<br />
<input type="checkbox" name="stat" value="stat" class="checkbox"'.(($filterStat == 1) ? ' checked="checked"' : '').' /> I uzavřené. <br />
<input type="checkbox" name="new" value="new" class="checkbox"'.(($f_new == 1) ? ' checked="checked"' : '').' /> Jen nové.';
	    if ($usrinfo['right_power']) {
	        echo '<br /> <input type="checkbox" name="sec" value="sec" class="checkbox"'.(($filterSec == 1) ? ' checked="checked"' : '').' /> Jen tajné.</p>';
	    } else {
	        echo '</p>';
	    }
	    echo '
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	/* stary vypis pripadu
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.datum AS 'datum' FROM ".DB_PREFIX."case WHERE ".DB_PREFIX."case.deleted=0".$fsql_sec.$fsql_stat." ORDER BY ".$filterSqlSort;
	} else {
	  $sql="SELECT ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.datum AS 'datum' FROM ".DB_PREFIX."case WHERE ".DB_PREFIX."case.deleted=0".$fsql_sec.$fsql_stat." AND ".DB_PREFIX."case.secret=0 ORDER BY ".$filterSqlSort;
	} Alternativni vypis osob*/
    $sql = "SELECT ".DB_PREFIX."case.datum as date_changed, ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.datum AS 'datum' FROM ".DB_PREFIX."case WHERE ".DB_PREFIX."case.deleted=0".$fsql_sec.$fsql_stat." AND ".DB_PREFIX."case.secret<=".$usrinfo['right_power']." ORDER BY ".$filterSqlSort;
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název</th>
	  <th>Stav</th>
	  <th>Změněno</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
	    //TODO case nema timestamp pro vytvoreni
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        if ($f_new == 0 || ($f_new == 1 && searchRecord(3,$rec['id']))) {
	            echo '<tr class="'.((searchRecord(3,$rec['id'])) ? ' unread_record' : (($even % 2 == 0) ? 'even' : 'odd')).(($rec['status']) ? ' solved' : '').'">
                        <td>'.(($rec['secret']) ? '<span class="secret"><a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></span>' : '<a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a>').'</td>
						<td>'.(($rec['status']) ? 'uzavřený' : 'otevřený').'</td>
						<td>'.webdate($rec['date_changed']).'</td>
                        '.(($usrinfo['right_text']) ? '	<td><a href="editcase.php?rid='.$rec['id'].'">upravit</a> | <a href="proccase.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat případ &quot;".StripSlashes($rec['title'])."&quot;?');".'">smazat</a></td>' : '<td><a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku</a></td>').'
                        </tr>';
	            $even++;
	        }
	    }
	    echo '</tbody>
</table>
</div>
';
	} else {
	    echo '<div id="obsah"><p>Žádné případy neodpovídají výběru.</p></div>';
	}
	latteDrawTemplate("footer");
?>
