<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = $text['point'].'y';
  
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latte->render($config['folder_templates'].'header.latte', $latteParameters);

if (isset($_POST['addpoints'])) {
    auditTrail(9, 2, 0);
} else {
    auditTrail(9, 1, 0);
}
	mainMenu ();
        $custom_Filter = custom_Filter(9);
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
	if (!isset($custom_Filter['sort'])) {
	    $f_sort = 1;
	} else {
	    $f_sort = $custom_Filter['sort'];
	}
	switch ($f_sort) {
	  case 1: $fsql_sort = ' '.DB_PREFIX.'user.login ASC '; break;
	  case 2: $fsql_sort = ' '.DB_PREFIX.'user.login DESC '; break;
	  case 3: $fsql_sort = ' '.DB_PREFIX.'user.zlobody ASC '; break;
	  case 4: $fsql_sort = ' '.DB_PREFIX.'user.zlobody DESC '; break;
	  default: $fsql_sort = ' '.DB_PREFIX.'user.login ASC ';
	}
	// Filtr
	function filter ()
	{
	    global $text,$f_sort;
	    echo '<div id="filter-wrapper"><form action="evilpoints.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat všechny uživatele a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort == 1) ? ' selected="selected"' : '').'>jména vzestupně</option>
	<option value="2"'.(($f_sort == 2) ? ' selected="selected"' : '').'>jména sestupně</option>
	<option value="3"'.(($f_sort == 3) ? ' selected="selected"' : '').'>'.$text['point'].'ů vzestupně</option>
	<option value="4"'.(($f_sort == 4) ? ' selected="selected"' : '').'>'.$text['point'].'ů sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis uživatelů
	$sql = "SELECT * FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.deleted=0 ORDER BY ".$fsql_sort;
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
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
	$latte->render($config['folder_templates'].'footer.latte', $latteParameters);
?>
