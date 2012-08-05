<?php
	require_once ('./inc/func_main.php');
	// následuje načtení dat reportu a jejich uložení do vybranných proměných 
	$reportarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT * FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid'])); // načte data z DB
	$type=intval($reportarray['type']); // určuje typ hlášení
		$typestring=(($type==1)?'výjezd':(($type==2)?'výslech':'?')); //odvozuje slovní typ hlášení
	$author=$reportarray['iduser']; // určuje autora hlášení
	$label=((isset($reportarray['label']))?$reportarray['label']:''); // nadpis hlášení, ke kterému je přiřazováno
	
	// následuje generování hlavičky
	pageStart ('Úprava hlášení'.(($label!='')?': '.$label.' ('.$typestring.')':'')); // specifikace TITLE
	mainMenu (5);
	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>úprava hlášení</strong>'.(($label!='')?' - "'.$label.' ('.$typestring.')"':''));
	// *** původní načítání autora ---
	//$autharray=MySQL_Fetch_Assoc(MySQL_Query("SELECT iduser FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid']));
	//$author=$autharray['iduser'];
	// --- původní načítání autora ***
	if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || $usrinfo['id']==$author)) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>

<div id="obsah">
<p>
K hlášení můžete přiřadit osoby, kterých se týká nebo kterých by se týkat mohl.
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
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'persons.surname, '.DB_PREFIX.'persons.name ASC ';
	}
	// formular filtru
	function filter () {
		global $f_sort, $sportraits;
	  echo '<form action="addp2ar.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat osoby a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>příjmení a jména vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>příjmení a jména sestupně</option>
</select>.</p>
		<p><input type="checkbox" name="sportraits" value="1"'.(($sportraits)?' checked="checked"':'').'> zobrazit portréty</p>
	  <div id="filtersubmit"><input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" /><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form action="addpersons.php" method="post" class="otherform">';
	}
	filter();
	// vypis osob
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.role AS 'role', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."persons LEFT JOIN ".DB_PREFIX."ar2p ON ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." WHERE ".DB_PREFIX."persons.deleted=0 ORDER BY ".$fsql_sort;
	} else {
	  $sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.role AS 'role', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."persons LEFT JOIN ".DB_PREFIX."ar2p ON ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." WHERE ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="">
<table>
<thead>
	<tr>
	<th>#</th>
	<th>Úloha</th>
'.(($sportraits)?'<th>Portrét</th>':'').'
	  <th>Jméno</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'"><td><input type="checkbox" name="person[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' /></td>
	<td><select type="role" name="role[]">
			<option value="0">osoba přítomná</option>
			<option value="4"'.(($rec['role']==4)?' selected="selected"':'').'>velitel akce</option>'
			.(($type==1)?'
			<option value="3"'.(($rec['role']==3)?' selected="selected"':'').'>zatčený</option>':'')
			.(($type==2)?'
			<option value="1"'.(($rec['role']==1)?' selected="selected"':'').'>vyslýchaný</option>
			<option value="2"'.(($rec['role']==2)?' selected="selected"':'').'>vyslýchající</option>':'').'
		</select></td>
'.(($sportraits)?'<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>':'').'
	<td>'.(($rec['secret'])?'<span class="secret"><a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a></span>':'<a href="readperson.php?rid='.$rec['id'].'">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a>').'</td>
	</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
?>

<div>
<input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
<input type="submit" value="Uložit změny" name="addtoareport" class="submitbutton" />
</div>
</form>

</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Hlášení neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>