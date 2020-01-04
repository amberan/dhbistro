<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Audit';
  
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latte->render($config['folder_templates'].'header.latte', $latteParameters);

        if (!$usrinfo['right_aud']) {
            unauthorizedAccess(11, 1, 0, 0);
        }
	
	function operationType ($type)
	{
	    global $database;
	    $sql_ga = "SELECT ".DB_PREFIX."operation_type.name as 'name' FROM ".DB_PREFIX."operation_type WHERE ".DB_PREFIX."operation_type.id='".$type."'";
	    $res_ga = mysqli_query ($database,$sql_ga);
	    if (mysqli_num_rows ($res_ga)) {
	        while ($rec_ga = mysqli_fetch_assoc ($res_ga)) {
	            $name = StripSlashes ($rec_ga['name']);

	            return $name;
	        }
	    } else {
	        $name = 'neznámý typ';

	        return $name;
	    }
	}
	
	function recordType ($type)
	{
	    global $database;
	    $sql_ga = "SELECT ".DB_PREFIX."record_type.name as 'name' FROM ".DB_PREFIX."record_type WHERE ".DB_PREFIX."record_type.id='".$type."'";
	    $res_ga = mysqli_query ($database,$sql_ga);
	    if (mysqli_num_rows ($res_ga)) {
	        while ($rec_ga = mysqli_fetch_assoc ($res_ga)) {
	            $name = StripSlashes ($rec_ga['name']);
	            //					if ($name=='zlobody') {
	            //						$name=$GLOBALS['point'].'y';
	            //					}
	            return $name;
	        }
	    } else {
	        $name = 'neznámý typ';

	        return $name;
	    }
	}
	
	function getRecord ($type, $idrecord)
	{
	    global $database;
	    if ($idrecord > 0) {
	        switch ($type) {
				case 1: $sql_type = "SELECT ".DB_PREFIX."person.name as 'name', ".DB_PREFIX."person.surname as 'surname' FROM ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id='".$idrecord."'";
						$res_type = mysqli_query ($database,$sql_type);
						if (mysqli_num_rows ($res_type)) {
						    while ($rec_type = mysqli_fetch_assoc ($res_type)) {
						        $name = StripSlashes ($rec_type['surname']).', '.StripSlashes ($rec_type['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 2: $sql_type = "SELECT ".DB_PREFIX."group.title as 'name' FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.id='".$idrecord."'";
						$res_type = mysqli_query ($database,$sql_type);
						if (mysqli_num_rows ($res_type)) {
						    while ($rec_type = mysqli_fetch_assoc ($res_type)) {
						        $name = StripSlashes ($rec_type['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 3: $sql_type = "SELECT ".DB_PREFIX."case.title as 'name' FROM ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id='".$idrecord."'";
						$res_type = mysqli_query ($database,$sql_type);
						if (mysqli_num_rows ($res_type)) {
						    while ($rec_type = mysqli_fetch_assoc ($res_type)) {
						        $name = StripSlashes ($rec_type['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 4: $sql_type = "SELECT ".DB_PREFIX."report.label as 'name' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.id='".$idrecord."'";
						$res_type = mysqli_query ($database,$sql_type);
						if (mysqli_num_rows ($res_type)) {
						    while ($rec_type = mysqli_fetch_assoc ($res_type)) {
						        $name = StripSlashes ($rec_type['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 7: $name = $idrecord; break;
				case 8: $sql_type = "SELECT ".DB_PREFIX."user.login as 'name' FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.id='".$idrecord."'";
						$res_type = mysqli_query ($database,$sql_type);
						if (mysqli_num_rows ($res_type)) {
						    while ($rec_type = mysqli_fetch_assoc ($res_type)) {
						        $name = StripSlashes ($rec_type['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 10: $name = $idrecord; break;
			}

	        return $name;
	    } else {
	        $name = 'globální operace';

	        return $name;
	    }
	}
	
	function linkType ($type, $recid)
	{
	    if ($recid > 0) {
	        switch ($type) {
				case 1: $link = 'readperson.php?rid='.$recid.'&hidenotes=0'; break;
				case 2: $link = 'readgroup.php?rid='.$recid.'&hidenotes=0'; break;
				case 3: $link = 'readcase.php?rid='.$recid.'&hidenotes=0'; break;
				case 4: $link = 'readactrep.php?rid='.$recid.'&hidenotes=0&truenames=0'; break;
				case 7: $link = 'readsymbol.php?rid='.$recid; break;
				case 8: $link = 'edituser.php?rid='.$recid; break;
				case 10: $link = 'tasks.php'; break;
			}

	        return $link;
	    }
	}
	
	auditTrail(11, 1, 0);
	mainMenu (2);
        $custom_Filter = custom_Filter(11);
	sparklets ('<strong>audit</strong>');
	
	// zpracovani filtru
	if (!isset($custom_Filter['kategorie'])) {
	    $f_cat = 0;
	} else {
	    $f_cat = $custom_Filter['kategorie'];
	}
	if (!isset($custom_Filter['sort'])) {
	    $f_sort = 2;
	} else {
	    $f_sort = $custom_Filter['sort'];
	}
	if (!isset($custom_Filter['user'])) {
	    $f_user = 0;
	} else {
	    $f_user = $custom_Filter['user'];
	}
	if (!isset($custom_Filter['typ'])) {
	    $f_type = 1;
	} else {
	    $f_type = $custom_Filter['typ'];
	}
	if (!isset($custom_Filter['org'])) {
	    $f_org = 0;
	} else {
	    $f_org = 1;
	}
	if (!isset($custom_Filter['my'])) {
	    $f_my = 0;
	} else {
	    $f_my = 1;
	}
	if (!isset($custom_Filter['glob'])) {
	    $f_glob = 0;
	} else {
	    $f_glob = 1;
	}
	if (!isset($custom_Filter['count'])) {
	    $f_count = '10';
	} else {
	    $f_count = $custom_Filter['count'];
	}
	switch ($f_cat) {
	  case 0: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) '; break;
	  case 1: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type<>11 '; break;
	  case 2: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=11 '; break;
	  case 3: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=1 '; break;
	  case 4: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=2 '; break;
	  case 5: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=3 '; break;
	  case 6: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=4 '; break;
	  default: $fsql_cat = ' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) ';
	}
	switch ($f_type) {
		case 0: $fsql_type = ' '; break;
		case 1: $fsql_type = ' AND '.DB_PREFIX.'audit_trail.operation_type<>1 '; break;
		case 2: $fsql_type = ' WHERE '.DB_PREFIX.'audit_trail.operation_type NOT IN (4,5,6,7,8,9) '; break;
		default: $fsql_type = ' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) ';
	}
	if ($f_user == 0) {
	    $fsql_user = ' ';
	} else {
	    $fsql_user = ' AND '.DB_PREFIX.'audit_trail.iduser='.$f_user;
	}
	switch ($f_sort) {
	  case 1: $fsql_sort = ' '.DB_PREFIX.'audit_trail.time ASC '; break;
	  case 2: $fsql_sort = ' '.DB_PREFIX.'audit_trail.time DESC '; break;
	  default: $fsql_sort = ' '.DB_PREFIX.'audit_trail.time ASC ';
	}
	if ($f_org == 0) {
	    $fsql_org = ' AND '.DB_PREFIX.'audit_trail.org=0';
	} else {
	    $fsql_org = ' ';
	}
	if ($f_my == 0) {
	    $fsql_my = ' AND '.DB_PREFIX.'audit_trail.iduser<>'.$usrinfo['id'];
	} else {
	    $fsql_my = ' ';
	}
	if ($f_glob == 0) {
	    $fsql_glob = ' AND '.DB_PREFIX.'audit_trail.idrecord<>0';
	} else {
	    $fsql_glob = ' ';
	}
	if ($f_count <> 0) {
	    $fsql_count = ' LIMIT '.$f_count;
	} else {
	    $fsql_count = ' ';
	}
?>

<?php
	// filtr
	function filter ()
	{
	    global $f_cat,$f_sort,$f_user,$f_type,$usrinfo,$f_org,$f_my,$f_glob,$f_count;
	    echo '<div id="filter-wrapper"><form action="audit.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.(($f_cat == 0) ? ' selected="selected"' : '').'>všechny auditní záznamy</option>
	<option value="1"'.(($f_cat == 1) ? ' selected="selected"' : '').'>i s aktualitami</option>
	<option value="2"'.(($f_cat == 2) ? ' selected="selected"' : '').'>prohlížení auditních záznamů</option>
	<option value="3"'.(($f_cat == 3) ? ' selected="selected"' : '').'>manipulaci s osobami</option>
	<option value="4"'.(($f_cat == 4) ? ' selected="selected"' : '').'>manipulaci se skupinami</option>
	<option value="5"'.(($f_cat == 5) ? ' selected="selected"' : '').'>manipulaci s případy</option>
	<option value="6"'.(($f_cat == 6) ? ' selected="selected"' : '').'>manipulaci s hlášeními</option>	  			  		
	</select> 
	<select name="typ">
	<option value="0"'.(($f_type == 0) ? ' selected="selected"' : '').'>všech typů</option>
	<option value="1"'.(($f_type == 1) ? ' selected="selected"' : '').'>jen zásahy</option>
	<option value="2"'.(($f_type == 2) ? ' selected="selected"' : '').'>bez souborů a poznámek</option>
	</select>
	provedené uživatelem 
		<select name="user" id="user">
	  	<option value=0 '.(($f_user == 0) ? ' selected="selected"' : '').'>všemi</option>';
 	
	    $sql_u = "SELECT id, login FROM ".DB_PREFIX."user WHERE deleted=0 ORDER BY login ASC";
	    $res_u = mysqli_query ($database,$sql_u);
	    while ($rec_u = mysqli_fetch_assoc ($res_u)) {
	        echo '<option value="'.$rec_u['id'].'"'.(($rec_u['id'] == $f_user) ? ' selected="selected"' : '').'>'.$rec_u['login'].'</option>';
	    };
	    echo '</select>';


	  		
	    echo 'a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort == 1) ? ' selected="selected"' : '').'>času vzestupně</option>
	<option value="2"'.(($f_sort == 2) ? ' selected="selected"' : '').'>času sestupně</option>
	</select>.</p>';
	    if ($usrinfo['right_org'] == 1) {
	        echo '					
		<label for="org">Zobrazit i zásahy organizátorů</label>
		<input type="checkbox" name="org" '.(($f_org == 1) ? ' checked="checked"' : '').'/><br/>
		<div class="clear">&nbsp;</div>';
	    }
	    echo '<label for="my">Zobrazit i moje zásahy</label>
	<input type="checkbox" name="my" '.(($f_my == 1) ? ' checked="checked"' : '').'/><br/>
	<div class="clear">&nbsp;</div>
	<label for="my">Zobrazit i globální operace</label>
	<input type="checkbox" name="glob" '.(($f_glob == 1) ? ' checked="checked"' : '').'/><br/>
	<div class="clear">&nbsp;</div>
	Zobrazit <input type="text" name="count" size=5 value="'.$f_count.'"> posledních záznamů. (Pro všechny záznamy ponechte pole prázdné).<br/>
	<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis uživatelů
	$sql = "SELECT * FROM ".DB_PREFIX."audit_trail".$fsql_cat.$fsql_type.$fsql_org.$fsql_my.$fsql_glob.$fsql_user." ORDER BY ".$fsql_sort.$fsql_count;
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Uživatel</th>
	  <th>Čas</th>
	  <th>IP</th>						
	  <th>Typ operace</th>
	  <th>Typ záznamu</th>
	  <th>Záznam</th>
	</tr>
</thead>
<tbody>
';
	    $even = 0;
	    while ($rec = mysqli_fetch_assoc ($res)) {
	        echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'">
	<td>'.getAuthor($rec['iduser'],0).'</td>
	<td>'.(($rec['time']) ? Date ('d. m. Y (H:i:s)',$rec['time']) : 'nikdy').'</td>
	<td>'.$rec['ip'].'</td>
	<td>'.operationType($rec['operation_type']).'</td>
	<td>'.recordType($rec['record_type']).'</td>
	<td>';
	        if ($rec['idrecord'] > 0) {
	            echo '<a href="'.linkType($rec['record_type'], $rec['idrecord']).'">'.getRecord($rec['record_type'], $rec['idrecord']).'</a>';
	        } else {
	            echo getRecord($rec['record_type'], $rec['idrecord']);
	        }
	        echo '</td></tr>';
	        $even++;
	    }
	    echo '</tbody>
</table>
</div>
';
	} else {
	    echo '<div id="obsah"><p>Žádné záznamy neodpovídají výběru.</p></div>';
	}
?>
<?php
	$latte->render($config['folder_templates'].'footer.latte', $latteParameters);
?>
