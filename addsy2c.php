<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Přiřazení symbolu';
  
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);


	mainMenu (5);
        $custom_Filter = custom_Filter(21);
	sparklets ('<a href="./symbols.php">nepřiřazené symboly</a> &raquo; <strong>přiřazení symbolu k případu</strong>');
	$sql="SELECT created_by FROM ".DB_PREFIX."symbol WHERE id=".$_REQUEST['rid'];
	$autharray=mysqli_fetch_assoc (mysqli_query ($database,$sql));
	$author=$autharray['created_by'];
	if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || $usrinfo['id']==$author)) {
	  $res=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."report WHERE id=".$_REQUEST['rid']);
		if ($rec=mysqli_fetch_assoc ($res)) {
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
Symbol můžete přiřadit k případu (či případům), u kterých se vyskytoval.
</p>

<?php
// zpracovani filtru
if (!isset($custom_Filter['sort'])) {
	$f_sort=1;
} else {
	$f_sort=$custom_Filter['sort'];
}
switch ($f_sort) {
	case 1: $fsql_sort=' '.DB_PREFIX.'case.title ASC '; break;
	case 2: $fsql_sort=' '.DB_PREFIX.'case.title DESC '; break;
	default: $fsql_sort=' '.DB_PREFIX.'case.title ASC ';
}
//
function filter () {
	global $database,$f_sort;
	echo '<form action="addsy2c.php" method="post" id="filter">
	<fieldset>
	<legend>Filtr</legend>
	<p>Vypsat všechny případy a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>názvu vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>názvu sestupně</option>
	</select>.</p>
	<input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" />
	<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
	</form>';
}
filter(); ?>
<form action="addsymbols.php" method="post" class="otherform">
<?php // vypis pripadu
if ($usrinfo['right_power']) {
	$sql="SELECT ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."symbol2all.iduser FROM ".DB_PREFIX."case LEFT JOIN ".DB_PREFIX."symbol2all ON ".DB_PREFIX."symbol2all.idrecord=".DB_PREFIX."case.id AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." WHERE ".DB_PREFIX."case.deleted=0 ORDER BY ".$fsql_sort;
} else {
	$sql="SELECT ".DB_PREFIX."case.status AS 'status', ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."symbol2all.iduser FROM ".DB_PREFIX."case LEFT JOIN ".DB_PREFIX."symbol2all ON ".DB_PREFIX."symbol2all.idrecord=".DB_PREFIX."case.id AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." WHERE ".DB_PREFIX."case.deleted=0 AND ".DB_PREFIX."case.secret=0 ORDER BY ".$fsql_sort;
}
$res=mysqli_query ($database,$sql);
?>
<div id="in-form-table">
<?php
if (mysqli_num_rows ($res)) {
	echo '<div id="">
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
	while ($rec=mysqli_fetch_assoc ($res)) {
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
	echo '<div id=""><p>Žádné případy neodpovídají výběru.</p></div>';
}
?>

<input type="hidden" name="symbolid" value="<?php echo $_REQUEST['rid']; ?>" />
<input id="button-floating-uloz" type="submit" value="Uložit změny" name="addsymbol2c" class="submitbutton" title="Uložit změny" />
</div>
</form>

</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Symbol neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>