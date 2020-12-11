<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Případy';

auditTrail(3, 1, 0);
mainMenu ();
sparklets ('<strong>případy</strong>','<a href="newcase.php">přidat případ</a>');

if (isset($_GET['sort'])) {
    sortingSet('case',$_GET['sort'],'case');
}
if (sizeof($_POST) > 0) {
    filterSet('case',$_POST['filter']);
}
$filter = filterGet('case');
$sqlFilter = DB_PREFIX."case.deleted<=".$user['aclRoot']." AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];

	switch (@$filter['sec']) {
	 	case 'on': $sqlFilter .= ' AND '.DB_PREFIX.'case.secret>0 ';
	}
	switch (@$filter['stat']) {
	 	case 'on': $sqlFilter .= ' AND '.DB_PREFIX.'case.status in (0,1)'; break;
	 	default: $sqlFilter .= ' AND '.DB_PREFIX.'case.status=0 ';
	}

?>
<form action="cases.php" method="POST" id="filter"> 
<input type="hidden" name="placeholder"  />
<input type="checkbox" name="filter[stat]" <?php if (isset($filter['stat']) AND $filter['stat'] == 'on') {
    echo " checked";
} ?>  onchange="this.form.submit()"/> I uzavřené. 
<input type="checkbox" name="filter[new]" <?php if (isset($filter['new']) AND $filter['new'] == 'on') {
    echo " checked";
} ?>  onchange="this.form.submit()"/> Jen nové.
<?php if ($user['aclSecret']) { ?>
    <input type="checkbox" name="filter[sec]" <?php if (isset($filter['sec']) AND $filter['sec'] == 'on') {
    echo " checked";
} ?>  onchange="this.form.submit()"/> Jen tajné.</p>
<?php	    } ?>

</form>

<?php
    $sql = "SELECT ".DB_PREFIX."case.datum as date_changed, ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.datum AS 'datum', ".DB_PREFIX."case.deleted AS 'deleted' 
    FROM ".DB_PREFIX."case 
    WHERE ".$sqlFilter.sortingGet('case');
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
	        if (@$filter['new'] == null || ($filter['new'] == on && searchRecord(3,$rec['id']))) {
	            echo '<tr class="'.((searchRecord(3,$rec['id'])) ? ' unread_record' : (($even % 2 == 0) ? 'even' : 'odd')).(($rec['status']) ? ' solved' : '').'">
                        <td>'.(($rec['secret']) ? '<span class="secret"><a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a></span>' : '<a href="readcase.php?rid='.$rec['id'].'&amp;hidenotes=0">'.StripSlashes($rec['title']).'</a>').'</td>
						<td>'.(($rec['status']) ? 'uzavřený' : 'otevřený').(($rec['deleted']) ? ' smazano' : '').'</td>
                        <td>'.webdate($rec['date_changed']).'</td>
                        <td>'
                        .(($usrinfo['right_text']) ? '	<a href="editcase.php?rid='.$rec['id'].'">upravit</a> ' : '')
                        .'<a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku</a>'
                        .(($user['aclRoot'] AND !$rec['deleted']) ? ' <a href="proccase.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat případ &quot;".StripSlashes($rec['title'])."&quot;?');".'">smazat</a> ' : '')
                        .(($user['aclRoot'] AND $rec['deleted']) ? ' <a href="proccase.php?restore='.$rec['id'].'" onclick="'."return confirm('Opravdu obnovit případ &quot;".StripSlashes($rec['title'])."&quot;?');".'">obnovit</a> ' : '')
                        .'</td></tr>';
	            $even++;
	        }
	    }
	    echo '</tbody>
</table>
</div>
';
	}
    if ($even == 0) {
        echo '<div id="obsah"><p>Žádné případy neodpovídají výběru.</p></div>';
    }
	latteDrawTemplate("footer");
?>
