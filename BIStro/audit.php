<?php
	require_once ('./inc/func_main.php');
	
	function operationType ($type) {
			$sql_ga="SELECT ".DB_PREFIX."operation_type.name as 'name' FROM ".DB_PREFIX."operation_type WHERE ".DB_PREFIX."operation_type.id='".$type."'";
			$res_ga=MySQL_Query ($sql_ga);
			if (MySQL_Num_Rows($res_ga)) {
				while ($rec_ga=MySQL_Fetch_Assoc($res_ga)) {
					$name=StripSlashes ($rec_ga['name']);
					return $name;
				}
			} else {
				$name='neznámý typ';
				return $name;
			}
	}
	
	function recordType ($type) {
		$sql_ga="SELECT ".DB_PREFIX."record_type.name as 'name' FROM ".DB_PREFIX."record_type WHERE ".DB_PREFIX."record_type.id='".$type."'";
		$res_ga=MySQL_Query ($sql_ga);
		if (MySQL_Num_Rows($res_ga)) {
			while ($rec_ga=MySQL_Fetch_Assoc($res_ga)) {
				$name=StripSlashes ($rec_ga['name']);
				return $name;
			}
		} else {
			$name='neznámý typ';
			return $name;
		}
	}
	
	function getRecord ($type, $idrecord) {
		if ($idrecord>0) {
			switch ($type) {
				case 1: $sql_type="SELECT ".DB_PREFIX."persons.name as 'name', ".DB_PREFIX."persons.surname as 'surname' FROM ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id='".$idrecord."'";
						$res_type=MySQL_Query ($sql_type);
						if (MySQL_Num_Rows($res_type)) {
							while ($rec_type=MySQL_Fetch_Assoc($res_type)) {
								$name=StripSlashes ($rec_type['surname']).', '.StripSlashes ($rec_type['name']);
							}
						} else {
							$name='neznámý';
						}
						break;
				case 2: $sql_type="SELECT ".DB_PREFIX."groups.title as 'name' FROM ".DB_PREFIX."groups WHERE ".DB_PREFIX."groups.id='".$idrecord."'";
						$res_type=MySQL_Query ($sql_type);
						if (MySQL_Num_Rows($res_type)) {
							while ($rec_type=MySQL_Fetch_Assoc($res_type)) {
								$name=StripSlashes ($rec_type['name']);
							}
						} else {
							$name='neznámý';
						}
						break;
				case 3: $sql_type="SELECT ".DB_PREFIX."cases.title as 'name' FROM ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id='".$idrecord."'";
						$res_type=MySQL_Query ($sql_type);
						if (MySQL_Num_Rows($res_type)) {
							while ($rec_type=MySQL_Fetch_Assoc($res_type)) {
								$name=StripSlashes ($rec_type['name']);
							}
						} else {
							$name='neznámý';
						}
						break;
				case 4: $sql_type="SELECT ".DB_PREFIX."reports.label as 'name' FROM ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.id='".$idrecord."'";
						$res_type=MySQL_Query ($sql_type);
						if (MySQL_Num_Rows($res_type)) {
							while ($rec_type=MySQL_Fetch_Assoc($res_type)) {
								$name=StripSlashes ($rec_type['name']);
							}
						} else {
							$name='neznámý';
						}
						break;
				case 7: $name=$idrecord; break;
				case 8: $sql_type="SELECT ".DB_PREFIX."users.login as 'name' FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.id='".$idrecord."'";
						$res_type=MySQL_Query ($sql_type);
						if (MySQL_Num_Rows($res_type)) {
							while ($rec_type=MySQL_Fetch_Assoc($res_type)) {
								$name=StripSlashes ($rec_type['name']);
							}
						} else {
							$name='neznámý';
						}
						break;
				case 10: $name=$idrecord; break;
			}
			return $name;
		} else {
			$name='globální operace';
			return $name;
		}
	}
	
	function linkType ($type, $recid) {
		if ($recid>0) { 
			switch ($type) {
				case 1: $link='readperson.php?rid='.$recid.'&hidenotes=0'; break;
				case 2: $link='readgroup.php?rid='.$recid.'&hidenotes=0'; break;
				case 3: $link='readcase.php?rid='.$recid.'&hidenotes=0'; break;
				case 4: $link='readactrep.php?rid='.$recid.'&hidenotes=0&truenames=0'; break;
				case 7: $link='editsymbol.php?rid='.$recid; break;
				case 8: $link='edituser.php?rid='.$recid; break;
				case 10: $link='tasks.php'; break;
			}
			return $link;
		}
	}
	
	auditTrail(11, 1, 0);
	pageStart ('Audit');
	mainMenu (2);
	sparklets ('<strong>audit</strong>');
	
	// zpracovani filtru
	if (!isset($_REQUEST['kategorie'])) {
	  $f_cat=0;
	} else {
	  $f_cat=$_REQUEST['kategorie'];
	}
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=2;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	if (!isset($_REQUEST['user'])) {
		$f_user=0;
	} else {
		$f_user=$_REQUEST['user'];
	}
	if (!isset($_REQUEST['typ'])) {
		$f_type=0;
	} else {
		$f_type=$_REQUEST['typ'];
	}
	switch ($f_cat) {
	  case 0: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) '; break;
	  case 1: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type<>11 '; break;
	  case 2: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type=11 '; break;
	  case 3: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type=1 '; break;
	  case 4: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type=2 '; break;
	  case 5: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type=3 '; break;
	  case 6: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type=4 '; break;
	  default: $fsql_cat=' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) ';
	}
	switch ($f_type) {
		case 0: $fsql_type=' '; break;
		case 1: $fsql_type=' AND '.DB_PREFIX.'audit_trail.operation_type<>1 '; break;
		case 2: $fsql_type=' WHERE '.DB_PREFIX.'audit_trail.operation_type NOT IN (4,5,6,7,8,9) '; break;
		default: $fsql_type=' WHERE '.DB_PREFIX.'audit_trail.record_type NOT IN (5,11) ';
	}
	if ($f_user==0) {
		$fsql_user=' ';
	} else {
		$fsql_user=' AND '.DB_PREFIX.'audit_trail.iduser='.$f_user;
	} 
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'audit_trail.time ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'audit_trail.time DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'audit_trail.time ASC ';
	}
?>	
	
<?php 
	// filtr
	function filter () {
	  global $f_cat,$f_sort,$f_user,$f_type;
	  echo '<div id="filter-wrapper"><form action="audit.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechny auditní záznamy</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>i s aktualitami</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>prohlížení auditních záznamů</option>
	<option value="3"'.(($f_cat==3)?' selected="selected"':'').'>manipulaci s osobami</option>
	<option value="4"'.(($f_cat==4)?' selected="selected"':'').'>manipulaci se skupinami</option>
	<option value="5"'.(($f_cat==5)?' selected="selected"':'').'>manipulaci s případy</option>
	<option value="6"'.(($f_cat==6)?' selected="selected"':'').'>manipulaci s hlášeními</option>	  			  		
	</select> 
	<select name="typ">
	<option value="0"'.(($f_type==0)?' selected="selected"':'').'>všech typů</option>
	<option value="1"'.(($f_type==1)?' selected="selected"':'').'>jen zásahy</option>
	<option value="2"'.(($f_type==2)?' selected="selected"':'').'>bez souborů a poznámek</option>
	</select>
	provedené uživatelem 
		<select name="user" id="user">
	  	<option value=0 '.(($f_user==0)?' selected="selected"':'').'>všemi</option>';
 	
		$sql_u="SELECT id, login FROM ".DB_PREFIX."users WHERE deleted=0 ORDER BY login ASC";
		$res_u=MySQL_Query ($sql_u);
		while ($rec_u=MySQL_Fetch_Assoc($res_u)) {
			echo '<option value="'.$rec_u['id'].'"'.(($rec_u['id']==$f_user)?' selected="selected"':'').'>'.$rec_u['login'].'</option>';
		};
		echo '</select>';


	  		
	echo 'a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>času vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>času sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis uživatelů
	$sql="SELECT * FROM ".DB_PREFIX."audit_trail".$fsql_cat.$fsql_type.$fsql_user." ORDER BY ".$fsql_sort;
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
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
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td>'.getAuthor($rec['iduser'],0).'</td>
	<td>'.(($rec['time'])?Date ('d. m. Y (H:i:s)',$rec['time']):'nikdy').'</td>
	<td>'.$rec['ip'].'</td>
	<td>'.operationType($rec['operation_type']).'</td>
	<td>'.recordType($rec['record_type']).'</td>
	<td>';
	if ($rec['idrecord']>0) {
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
	pageEnd ();
?>
