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
            $fNew = 0;
        } else {
            $fNew = 1;
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
	    global $filterSec, $filterStat, $fNew, $usrinfo;
	    echo '<div id="filter-wrapper"><form action="cases.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
<input type="checkbox" name="stat" value="stat" class="checkbox"'.(($filterStat == 1) ? ' checked="checked"' : '').' /> I uzavřené. <br />
<input type="checkbox" name="new" value="new" class="checkbox"'.(($fNew == 1) ? ' checked="checked"' : '').' /> Jen nové.';
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
    if (isset($_GET['sort'])) {
        sortingSet('case',$_GET['sort'],'case');
    }
    $sql = "SELECT ".DB_PREFIX."case.datum as date_changed, ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.datum AS 'datum' FROM ".DB_PREFIX."case WHERE ".DB_PREFIX."case.deleted=0".$fsql_sec.$fsql_stat." AND ".DB_PREFIX."case.secret<=".$usrinfo['right_power'].sortingGet('case');
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název <a href="cases.php?sort=title">&#8661;</a></th>
	  <th>Stav</th>
	  <th>Změněno  <a href="cases.php?sort=datum">&#8661;</a></th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
	    //TODO case nema timestamp pro vytvoreni
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        if ($fNew == 0 || ($fNew == 1 && searchRecord(3,$rec['id']))) {
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
