<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava hlášení');
	mainMenu (5);
	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>úprava hlášení</strong>');
	$autharray=MySQL_Fetch_Assoc(MySQL_Query("SELECT iduser FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid']));
	$author=$autharray['iduser'];
	if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || $usrinfo['id']==$author)) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>

<div id="obsah">
<p>
Hlášení můžete přiřadit k případu (či případům), kterého se týká.
</p>
</div>

<?php
// zpracovani filtru
if (!isset($_REQUEST['sort'])) {
	$f_sort=1;
} else {
	$f_sort=$_REQUEST['sort'];
}
switch ($f_sort) {
	case 1: $fsql_sort=' '.DB_PREFIX.'cases.title ASC '; break;
	case 2: $fsql_sort=' '.DB_PREFIX.'cases.title DESC '; break;
	default: $fsql_sort=' '.DB_PREFIX.'cases.title ASC ';
}
//
function filter () {
	global $f_sort;
	echo '<form action="addar2c.php" method="post" id="filter">
	<fieldset>
	<legend>Filtr</legend>
	<p>Vypsat všechny případy a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>názvu vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>názvu sestupně</option>
	</select>.</p>
	<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
	</form><form action="addreports.php" method="post" class="otherform">';
}
filter();
// vypis pripadu
if ($usrinfo['right_power']) {
	$sql="SELECT ".DB_PREFIX."cases.status AS 'status', ".DB_PREFIX."cases.secret AS 'secret', ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."ar2c.iduser FROM ".DB_PREFIX."cases LEFT JOIN ".DB_PREFIX."ar2c ON ".DB_PREFIX."ar2c.idcase=".DB_PREFIX."cases.id AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." WHERE ".DB_PREFIX."cases.deleted=0 ORDER BY ".$fsql_sort;
} else {
	$sql="SELECT ".DB_PREFIX."cases.status AS 'status', ".DB_PREFIX."cases.secret AS 'secret', ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."ar2c.iduser FROM ".DB_PREFIX."cases LEFT JOIN ".DB_PREFIX."ar2c ON ".DB_PREFIX."ar2c.idcase=".DB_PREFIX."cases.id AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." WHERE ".DB_PREFIX."cases.deleted=0 AND ".DB_PREFIX."cases.secret=0 ORDER BY ".$fsql_sort;
}
$res=MySQL_Query ($sql);
if (MySQL_Num_Rows($res)) {
	echo '<div id="obsah">
	<table>
	<thead>
	<tr>
	<th>#</th>
	<th>Název</th>
	<th>Stav</th>
	</tr>
	</thead>
	<tbody>
	';
	$even=0;
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<tr class="'.(($even%2==0)?'even':'odd').(($rec['status'])?' solved':'').'">
		<td><input type="checkbox" name="case[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' /></td><td>'.(($rec['secret'])?'<span class="secret"><a href="readcase.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a></span>':'<a href="readcase.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a>').'</td>
		<td>'.(($rec['status'])?'uzavřen':'&mdash;').'</td>
		</tr>';
		$even++;
	}
	echo '</tbody>
	</table>
	</div>
	';
} else {
	echo '<div id="obsah"><p>Žádné případy neodpovídají výběru.</p></div>';
}
pageEnd ();
?>

<div>
<input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
<input type="submit" value="Uložit změny" name="addtoareport" class="submitbutton" />
</div>
</form>

<?php
		} else {
		  echo '<div id="obsah"><p>Hlášení neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>