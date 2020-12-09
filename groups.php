<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Skupiny';
  
auditTrail(3, 1, 0);
mainMenu ();

$customFilter = custom_Filter(2);
sparklets ('<strong>skupiny</strong>','<a href="newgroup.php">přidat skupinu</a>');
	// zpracovani filtru
	if (!isset($customFilter['sort'])) {
	    $filterSort = 1;
	} else {
	    $filterSort = $customFilter['sort'];
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
        if (!isset($customFilter['arch'])) {
            $fArch = 0;
        } else {
            $fArch = 1;
        }
	switch ($filterSort) {
	  case 1: $filterSqlSort = ' '.DB_PREFIX.'group.title ASC '; break;
	  case 2: $filterSqlSort = ' '.DB_PREFIX.'group.title DESC '; break;
	  default: $filterSqlSort = ' '.DB_PREFIX.'group.title ASC ';
	}
	switch ($filterSec) {
		case 0: $fsql_sec = ''; break;
		case 1: $fsql_sec = ' AND '.DB_PREFIX.'group.secret>0 '; break;
		default: $fsql_sec = '';
	}
        switch ($fArch) {
		case 0: $fsql_arch = ' AND '.DB_PREFIX.'group.archived=0 '; break;
		case 1: $fsql_arch = ''; break;
		default: $fsql_arch = ' AND '.DB_PREFIX.'group.archived=0 ';
	}
	//
	function filter ()
	{
	    global $filterSort, $filterSec, $fNew, $fArch, $usrinfo;
	    echo '<div id="filter-wrapper"><form action="groups.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
        <br /> <input type="checkbox" name="new" value="new" class="checkbox"'.(($fNew == 1) ? ' checked="checked"' : '').' /> Jen nové.
        <br /> <input type="checkbox" name="arch" value="arch" class="checkbox"'.(($fArch == 1) ? ' checked="checked"' : '').' /> I archiv.';
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
	/* Stary vypis skupin
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.archived AS 'archived' FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.deleted=0".$fsql_sec.$fsql_arch." ORDER BY ".$filterSqlSort;
	} else {
	  $sql="SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.archived AS 'archived' FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.deleted=0".$fsql_sec.$fsql_arch." AND ".DB_PREFIX."group.secret=0 ORDER BY ".$filterSqlSort;
	} Alternativni vypis skupin*/

if (isset($_GET['sort'])) {
    sortingSet('group',$_GET['sort'],'group');
}

    $sql = "SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."group.archived AS 'archived' FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.deleted=0".$fsql_sec.$fsql_arch." AND ".DB_PREFIX."group.secret<=".$usrinfo['right_power'].sortingGet('group');
    /////" ORDER BY ".$filterSqlSort;
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Název <a href="groups.php?sort=title">&#8661;</a></th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        if ($fNew == 0 || ($fNew == 1 && searchRecord(2,$rec['id']))) {
	            echo '<tr class="'.((searchRecord(2,$rec['id'])) ? ' unread_record' : (($even % 2 == 0) ? 'even' : 'odd')).'">
                        <td>'.(($rec['secret']) ? '<span class="secret"><a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></span>' : '<a href="readgroup.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a>').'</td>
                        '.(($usrinfo['right_text']) ? '	<td><a href="editgroup.php?rid='.$rec['id'].'">upravit</a> | '.(($rec['archived'] == 0) ? '<a href="procgroup.php?archive='.$rec['id'].'" onclick="'."return confirm('Opravdu archivovat skupinu &quot;".StripSlashes($rec['title'])."&quot;?');".'">archivovat</a>' : '<a href="procgroup.php?dearchive='.$rec['id'].'" onclick="'."return confirm('Opravdu vyjmout z archivu skupinu &quot;".StripSlashes($rec['title'])."&quot;?');".'">z archivu</a>').' | <a href="procgroup.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat skupinu &quot;".StripSlashes($rec['title'])."&quot;?');".'">smazat</a></td>' : '<td><a href="newnote.php?rid='.$rec['id'].'&idtable=6">přidat poznámku</a></td>').'
                        </tr>';
	            $even++;
	        }
	    }
	    echo '</tbody>
</table>
</div>
';
	} else {
	    echo '<div id="obsah"><p>Žádné skupiny neodpovídají výběru.</p></div>';
	}
latteDrawTemplate("footer");
?>
