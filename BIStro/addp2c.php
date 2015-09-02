<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava případu');
	mainMenu (5);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>úprava případu</strong> &raquo; <strong>přidání osob</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."cases WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>

<div id="obsah">
	<script type="text/javascript">
	<!--
	window.onload=function(){
		FixitRight('button-floating-uloz', 'in-form-table');
	};
	-->
	</script>
<p>
K případu můžete přiřadit osoby, kterých se týká nebo kterých by se týkat mohl.
</p>

<?php
	// zpracovani filtru
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	if (!isset($_POST['sportraits'])) {
		$sportraits=false;
	} else {
		$sportraits=$_POST['sportraits'];
	}
	if (!isset($_POST['ssymbols'])) {
		$ssymbols=false;
	} else {
		$ssymbols=$_POST['ssymbols'];
	}
	if (!isset($_POST['fdead'])) {
		$fdead=0;
	} else {
		$fdead=1;
	}
	if (!isset($_POST['farchiv'])) {
		$farchiv=0;
	} else {
		$farchiv=1;
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC ';
	}
	switch ($fdead) {
		case 0: $fsql_dead=' AND '.DB_PREFIX.'persons.dead=0 '; break;
		case 1: $fsql_dead=''; break;
		default: $fsql_dead=' AND '.DB_PREFIX.'persons.dead=0 ';
	}
	switch ($farchiv) {
		case 0: $fsql_archiv=' AND '.DB_PREFIX.'persons.archiv=0 '; break;
		case 1: $fsql_archiv=''; break;
		default: $fsql_archiv=' AND '.DB_PREFIX.'persons.archiv=0 ';
	}
	// formular filtru
	function filter () {
		global $f_sort, $sportraits, $ssymbols, $farchiv, $fdead;
	  echo '<form action="addp2c.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>příjmení a jména sestupně</option>
	</select>.</p>
	<table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="sportraits" value="1"'.(($sportraits)?' checked="checked"':'').'> Zobrazit portréty.</td>
	<td class="filter"><input type="checkbox" name="fdead" value="1"'.(($fdead==1)?' checked="checked"':'').'> Zobrazit i mrtvé.</td>
	</tr>
	<td class="filter"><input type="checkbox" name="ssymbols" value="1"'.(($ssymbols)?' checked="checked"':'').'> Zobrazit symboly.</td>
	<td class="filter"><input type="checkbox" name="farchiv" value="1"'.(($farchiv==1)?' checked="checked"':'').'> Zobrazit i archiv.</td>
	</tr>
	</table>
	<div id="filtersubmit"><input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" /><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form action="addpersons.php" method="post" class="otherform">';
	}
	filter();
	// vypis osob
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.symbol AS 'symbol', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."persons LEFT JOIN ".DB_PREFIX."c2p ON ".DB_PREFIX."c2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." WHERE ".DB_PREFIX."persons.deleted=0 ".$fsql_dead.$fsql_archiv." ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.symbol AS 'symbol', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."persons LEFT JOIN ".DB_PREFIX."c2p ON ".DB_PREFIX."c2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." WHERE ".DB_PREFIX."persons.deleted=0 ".$fsql_dead.$fsql_archiv." AND ".DB_PREFIX."persons.secret=0 ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
?>
<div id="in-form-table">
<?php 
	if (MySQL_Num_Rows($res)) {
	  echo '<table>
<thead>
	<tr>
	<th>#</th>
'.(($sportraits)?'<th>Portrét</th>':'').(($ssymbols)?'<th>Symbol</th>':'').'
	  <th>Jméno</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td><input type="checkbox" name="person[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' /></td>
'.(($sportraits)?'<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>':'').(($ssymbols)?'<td><img src="getportrait.php?nrid='.$rec['symbol'].'" alt="symbol chybí" /></td>':'').'
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a></span>':'<a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a>').'</td>
	</tr>';
			$even++;
		}
	  echo '</tbody>
</table>';
	}
?>
<input type="hidden" name="fdead" value="<?php echo $fdead; ?>" />
<input type="hidden" name="farchiv" value="<?php echo $farchiv; ?>" />
<input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
<input id="button-floating-uloz" type="submit" value="Uložit změny" name="addtocase" class="submitbutton" title="Uložit změny"/>
</div>
<!-- end of #obsah -->
</form>

</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Případ neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>