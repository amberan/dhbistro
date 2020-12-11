<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Audit';
        if (!$user['aclAudit']) {
            unauthorizedAccess(11, 1, 0, 0);
        }
	
	function operationType ($type)
	{
	    global $database;
	    $sqlGa = "SELECT ".DB_PREFIX."operation_type.name as 'name' FROM ".DB_PREFIX."operation_type WHERE ".DB_PREFIX."operation_type.id='".$type."'";
	    $resGa = mysqli_query ($database,$sqlGa);
	    if (mysqli_num_rows ($resGa)) {
	        while ($recGa = mysqli_fetch_assoc ($resGa)) {
	            $name = StripSlashes ($recGa['name']);

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
	    $sqlGa = "SELECT ".DB_PREFIX."record_type.name as 'name' FROM ".DB_PREFIX."record_type WHERE ".DB_PREFIX."record_type.id='".$type."'";
	    $resGa = mysqli_query ($database,$sqlGa);
	    if (mysqli_num_rows ($resGa)) {
	        while ($recGa = mysqli_fetch_assoc ($resGa)) {
	            $name = StripSlashes ($recGa['name']);
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
				case 1: $sqlType = "SELECT ".DB_PREFIX."person.name as 'name', ".DB_PREFIX."person.surname as 'surname' FROM ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id='".$idrecord."'";
						$resType = mysqli_query ($database,$sqlType);
						if (mysqli_num_rows ($resType)) {
						    while ($recType = mysqli_fetch_assoc ($resType)) {
						        $name = StripSlashes ($recType['surname']).', '.StripSlashes ($recType['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 2: $sqlType = "SELECT ".DB_PREFIX."group.title as 'name' FROM ".DB_PREFIX."group WHERE ".DB_PREFIX."group.id='".$idrecord."'";
						$resType = mysqli_query ($database,$sqlType);
						if (mysqli_num_rows ($resType)) {
						    while ($recType = mysqli_fetch_assoc ($resType)) {
						        $name = StripSlashes ($recType['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 3: $sqlType = "SELECT ".DB_PREFIX."case.title as 'name' FROM ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id='".$idrecord."'";
						$resType = mysqli_query ($database,$sqlType);
						if (mysqli_num_rows ($resType)) {
						    while ($recType = mysqli_fetch_assoc ($resType)) {
						        $name = StripSlashes ($recType['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 4: $sqlType = "SELECT ".DB_PREFIX."report.label as 'name' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.id='".$idrecord."'";
						$resType = mysqli_query ($database,$sqlType);
						if (mysqli_num_rows ($resType)) {
						    while ($recType = mysqli_fetch_assoc ($resType)) {
						        $name = StripSlashes ($recType['name']);
						    }
						} else {
						    $name = 'neznámý';
						}
						break;
				case 7: $name = $idrecord; break;
				case 8: $sqlType = "SELECT ".DB_PREFIX."user.userName as 'name' FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.userId='".$idrecord."'";
						$resType = mysqli_query ($database,$sqlType);
						if (mysqli_num_rows ($resType)) {
						    while ($recType = mysqli_fetch_assoc ($resType)) {
						        $name = StripSlashes ($recType['name']);
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
	mainMenu ();
        $customFilter = custom_Filter(11);
	sparklets ('<strong>audit</strong>');
	
	// zpracovani filtru
	if (!isset($customFilter['kategorie'])) {
	    $filterCat = 0;
	} else {
	    $filterCat = $customFilter['kategorie'];
	}
	if (!isset($customFilter['user'])) {
	    $filterUser = 0;
	} else {
	    $filterUser = $customFilter['user'];
	}
	if (!isset($customFilter['typ'])) {
	    $filterType = 1;
	} else {
	    $filterType = $customFilter['typ'];
	}
	if (!isset($customFilter['org'])) {
	    $filterOrg = 0;
	} else {
	    $filterOrg = 1;
	}
	if (!isset($customFilter['my'])) {
	    $filterMine = 0;
	} else {
	    $filterMine = 1;
	}
	if (!isset($customFilter['glob'])) {
	    $filterGlob = 0;
	} else {
	    $filterGlob = 1;
	}
	if (!isset($customFilter['count'])) {
	    $filterCount = '10';
	} else {
	    $filterCount = $customFilter['count'];
	}
	switch ($filterCat) {
	  case 0: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) '; break;
	  case 1: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type<>11 '; break;
	  case 2: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=11 '; break;
	  case 3: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=1 '; break;
	  case 4: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=2 '; break;
	  case 5: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=3 '; break;
	  case 6: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type=4 '; break;
	  default: $filterSqlCat = ' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) ';
	}
	switch ($filterType) {
		case 0: $filterSqlType = ' '; break;
		case 1: $filterSqlType = ' AND '.DB_PREFIX.'audit_trail.operation_type<>1 '; break;
		case 2: $filterSqlType = ' WHERE '.DB_PREFIX.'audit_trail.operation_type NOT IN (4,5,6,7,8,9) '; break;
		default: $filterSqlType = ' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) ';
	}
	if ($filterUser == 0) {
	    $filterSqlUser = ' ';
	} else {
	    $filterSqlUser = ' AND '.DB_PREFIX.'audit_trail.iduser='.$filterUser;
	}
	if ($filterOrg == 0) {
	    $filterSqlOrg = ' AND '.DB_PREFIX.'audit_trail.org=0';
	} else {
	    $filterSqlOrg = ' ';
	}
	if ($filterMine == 0) {
	    $filterSqlMine = ' AND '.DB_PREFIX.'audit_trail.iduser<>'.$user['userId'];
	} else {
	    $filterSqlMine = ' ';
	}
	if ($filterGlob == 0) {
	    $filterSqlGlob = ' AND '.DB_PREFIX.'audit_trail.idrecord<>0';
	} else {
	    $filterSqlGlob = ' ';
	}
	if ($filterCount <> 0) {
	    $filterSqlCount = ' LIMIT '.$filterCount;
	} else {
	    $filterSqlCount = ' ';
	}
?>

<?php
	// filtr
	function filter ()
	{
	    global $database,$filterCat,$filterUser,$filterType,$usrinfo,$filterOrg,$filterMine,$filterGlob,$filterCount;
	    echo '<div id="filter-wrapper"><form action="audit.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.(($filterCat == 0) ? ' selected="selected"' : '').'>všechny auditní záznamy</option>
	<option value="1"'.(($filterCat == 1) ? ' selected="selected"' : '').'>i s aktualitami</option>
	<option value="2"'.(($filterCat == 2) ? ' selected="selected"' : '').'>prohlížení auditních záznamů</option>
	<option value="3"'.(($filterCat == 3) ? ' selected="selected"' : '').'>manipulaci s osobami</option>
	<option value="4"'.(($filterCat == 4) ? ' selected="selected"' : '').'>manipulaci se skupinami</option>
	<option value="5"'.(($filterCat == 5) ? ' selected="selected"' : '').'>manipulaci s případy</option>
	<option value="6"'.(($filterCat == 6) ? ' selected="selected"' : '').'>manipulaci s hlášeními</option>	  			  		
	</select> 
	<select name="typ">
	<option value="0"'.(($filterType == 0) ? ' selected="selected"' : '').'>všech typů</option>
	<option value="1"'.(($filterType == 1) ? ' selected="selected"' : '').'>jen zásahy</option>
	<option value="2"'.(($filterType == 2) ? ' selected="selected"' : '').'>bez souborů a poznámek</option>
	</select>
	provedené uživatelem 
		<select name="user" id="user">
	  	<option value=0 '.(($filterUser == 0) ? ' selected="selected"' : '').'>všemi</option>';
 	
	    $sqlU = "SELECT userId, userName FROM ".DB_PREFIX."user WHERE userDeleted=0 ORDER BY username ASC";
	    $resU = mysqli_query ($database,$sqlU);
	    while ($recU = mysqli_fetch_assoc ($resU)) {
	        echo '<option value="'.$recU['userId'].'"'.(($recU['userId'] == $filterUser) ? ' selected="selected"' : '').'>'.$recU['userName'].'</option>';
	    };
	    echo '</select></p>';
	    if ($user['aclGamemaster'] == 1) {
	        echo '					
		<label for="org">Zobrazit i zásahy organizátorů</label>
		<input type="checkbox" name="org" '.(($filterOrg == 1) ? ' checked="checked"' : '').'/><br/>
		<div class="clear">&nbsp;</div>';
	    }
	    echo '<label for="my">Zobrazit i moje zásahy</label>
	<input type="checkbox" name="my" '.(($filterMine == 1) ? ' checked="checked"' : '').'/><br/>
	<div class="clear">&nbsp;</div>
	<label for="my">Zobrazit i globální operace</label>
	<input type="checkbox" name="glob" '.(($filterGlob == 1) ? ' checked="checked"' : '').'/><br/>
	<div class="clear">&nbsp;</div>
	Zobrazit <input type="text" name="count" size=5 value="'.$filterCount.'"> posledních záznamů. (Pro všechny záznamy ponechte pole prázdné).<br/>
	<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
    filter();
if (isset($_GET['sort'])) {
    sortingSet('audit',$_GET['sort'],'audit_trail');
}
	// vypis uživatelů
    $sql = "SELECT * FROM ".DB_PREFIX."audit_trail".$filterSqlCat.$filterSqlType.$filterSqlOrg.$filterSqlMine.$filterSqlGlob.$filterSqlUser.sortingGet('audit','audit_trail').$filterSqlCount;
    //////" ORDER BY ".$filterSqlSort.$filterSqlCount;
	$res = mysqli_query ($database,$sql);
	if (mysqli_num_rows ($res)) {
	    echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Uživatel <a href="audit.php?sort=iduser">&#8661;</a></th>
	  <th>Čas <a href="audit.php?sort=time">&#8661;</a></th>
	  <th>IP</th>						
	  <th>Typ operace</th>
	  <th>Typ záznamu <a href="audit.php?sort=record_type">&#8661;</a></th>
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
	latteDrawTemplate("footer");
?>
