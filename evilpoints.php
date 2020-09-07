<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = $text['point'].'y';

if (isset($_POST['addpoints'])) {
    auditTrail(9, 2, 0);
} else {
    auditTrail(9, 1, 0);
}
	mainMenu ();
        $customFilter = custom_Filter(9);
	sparklets ('<strong>'.$text['point'].'y</strong>',(($usrinfo['right_power']) ? 'aktuální stav' : ''));
	//Přidání zlobodů
	if (isset($_POST['addpoints'])) {
	    if (is_numeric($_POST['plus'])) {
	        $ep_result = $_POST['oldpoints'] + $_POST['plus'];
	        mysqli_query ($database,"UPDATE ".DB_PREFIX."user SET zlobody=".$ep_result." WHERE id=".$_POST['usrid']."");
	        echo '<div id="obsah"><p>Zlobody přidány.</p></div>';
	    } else {
	        echo '<div id="obsah"><p>Přidané '.$text['point'].'y musí být číselné.</p></div>';
	    }
	}
	
	// zpracovani filtru
	if (!isset($customFilter['sort'])) {
	    $filterSort = 1;
	} else {
	    $filterSort = $customFilter['sort'];
	}
	switch ($filterSort) {
	  case 1: $filterSqlSort = ' '.DB_PREFIX.'user.login ASC '; break;
	  case 2: $filterSqlSort = ' '.DB_PREFIX.'user.login DESC '; break;
	  case 3: $filterSqlSort = ' '.DB_PREFIX.'user.zlobody ASC '; break;
	  case 4: $filterSqlSort = ' '.DB_PREFIX.'user.zlobody DESC '; break;
	  default: $filterSqlSort = ' '.DB_PREFIX.'user.login ASC ';
	}
	// Filtr
	function filter ()
	{
	    global $text,$filterSort;
	    echo '<div id="filter-wrapper"><form action="evilpoints.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny uživatele a seřadit je podle <select name="sort">
	<option value="1"'.(($filterSort == 1) ? ' selected="selected"' : '').'>jména vzestupně</option>
	<option value="2"'.(($filterSort == 2) ? ' selected="selected"' : '').'>jména sestupně</option>
	<option value="3"'.(($filterSort == 3) ? ' selected="selected"' : '').'>'.$text['point'].'ů vzestupně</option>
	<option value="4"'.(($filterSort == 4) ? ' selected="selected"' : '').'>'.$text['point'].'ů sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis uživatelů
	$sql = "SELECT * FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.userDeleted=0 ORDER BY ".$filterSqlSort;
	$res = mysqli_query ($database,$sql);
	if (!is_bool($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Kódové označení</th>
	  <th>Aktuální počet '.$text['point'].'ů</th>
	  '.(($usrinfo['right_power']) ? '	  <th>Akce</th>' : '').'
	</tr>
</thead>
<tbody>
';
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td>'.StripSlashes($rec['login']).'</td>
	<td>'.($rec['zlobody']).'</td>
	'.(($usrinfo['right_power']) ? '<td>
			<form action="evilpoints.php" method="post" id="inputform" class="evilform">
			<input class="plus" type="text" name="plus" id="plus" />
			<input type="hidden" name="usrid" value="'.($rec['id']).'" />
			<input type="hidden" name="oldpoints" value="'.($rec['zlobody']).'" />
			<input type="submit" name="addpoints" value="Přidat" />
			</form>
	</td>' : '').'
</tr>';
	        $even++;
	    }
	    echo '</tbody>
</table>
</div>
';
	} else {
	    echo '<div id="obsah"><p>Žádní uživatelé neodpovídají výběru.</p></div>';
	}
?>
<?php
	latteDrawTemplate("footer");
?>
