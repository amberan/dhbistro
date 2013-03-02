<?php
	require_once ('./inc/func_main.php');
	pageStart ('Přiřazení k hlášení');
	mainMenu (5);
	sparklets ('<a href="./symbols.php">symboly</a> &raquo; <strong>úprava symbolu</strong> &raquo; <strong>přidání k hlášení</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."symbols WHERE id=".$_REQUEST['rid']);
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
K případu můžete přiřadit hlášení, která se ho týkají.
</p>

<?php
		// zpracovani filtru
	if (!isset($_REQUEST['type'])) {
	  $f_cat=0;
	} else {
	  $f_cat=$_REQUEST['type'];
	}
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=6;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	if (!isset($_REQUEST['status'])) {
		$f_stat=0;
	} else {
		$f_stat=$_REQUEST['status'];
	}
	switch ($f_cat) {
	  case 0: $fsql_cat=''; break;
	  case 1: $fsql_cat=' AND '.DB_PREFIX.'reports.type=1 '; break;
	  case 2: $fsql_cat=' AND '.DB_PREFIX.'reports.type=2 '; break;
	  default: $fsql_cat='';
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'reports.datum DESC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'reports.datum ASC '; break;
	  case 3: $fsql_sort=' '.DB_PREFIX.'users.login ASC '; break;
	  case 4: $fsql_sort=' '.DB_PREFIX.'users.login DESC '; break;
	  case 5: $fsql_sort=' '.DB_PREFIX.'reports.adatum ASC '; break;
	  case 6: $fsql_sort=' '.DB_PREFIX.'reports.adatum DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'reports.adatum DESC ';
	}
	switch ($f_stat) {
		case 0: $fsql_stat=''; break;
		case 1: $fsql_stat=' AND '.DB_PREFIX.'reports.status=0 '; break;
		case 2: $fsql_stat=' AND '.DB_PREFIX.'reports.status=1 '; break;
		case 3: $fsql_stat=' AND '.DB_PREFIX.'reports.status=2 '; break;
		default: $fsql_stat='';
	}
	//
	function filter () {
	  global $f_cat;
		global $f_sort;
		  global $f_stat;
	  echo '<form action="addsy2ar.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
<!--
	  <p>Vypsat <select name="status">
	<option value="0"'.(($f_stat==0)?' selected="selected"':'').'>všechna</option>
	<option value="1"'.(($f_stat==1)?' selected="selected"':'').'>rozpracovaná</option>
	<option value="2"'.(($f_stat==2)?' selected="selected"':'').'>dokončená</option>
	<option value="3"'.(($f_stat==3)?' selected="selected"':'').'>analyzovaná</option>
</select> hlášení <select name="type">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechna</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>z výjezdu</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>z výslechu</option>
</select> a 
-->
seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>data hlášení sestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>data hlášení vzestupně</option>
	<option value="3"'.(($f_sort==3)?' selected="selected"':'').'>jména autora vzestupně</option>
	<option value="4"'.(($f_sort==4)?' selected="selected"':'').'>jména autora sestupně</option>
	<option value="5"'.(($f_sort==5)?' selected="selected"':'').'>data výjezdu vzestupně</option>
	<option value="6"'.((!$f_sort==6)?'':' selected="selected"').'>data výjezdu sestupně</option>
</select>.</p>
	  <div id="filtersubmit">
	  <input type="hidden" name="rid" value="'.$_REQUEST['rid'].'" />
	  <input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form><form action="addsymbols.php" method="post" class="otherform">';
	}
	filter();
	// vypis hlášení
	if ($usrinfo['right_power']) {
		$sql="SELECT
			".DB_PREFIX."reports.id AS 'id',
	        ".DB_PREFIX."reports.datum AS 'datum',
	        ".DB_PREFIX."reports.label AS 'label',
	        ".DB_PREFIX."reports.task AS 'task',
	        ".DB_PREFIX."users.login AS 'autor',
	        ".DB_PREFIX."reports.type AS 'type',
	        ".DB_PREFIX."symbol2all.iduser 
	        	FROM ".DB_PREFIX."users, ".DB_PREFIX."reports LEFT JOIN ".DB_PREFIX."symbol2all 
	        	ON ".DB_PREFIX."symbol2all.idrecord=".DB_PREFIX."reports.id AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4
				WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0".$fsql_cat.$fsql_stat."
				ORDER BY ".$fsql_sort;
	} else {
		$sql="SELECT
			".DB_PREFIX."reports.id AS 'id',
	        ".DB_PREFIX."reports.datum AS 'datum',
	        ".DB_PREFIX."reports.label AS 'label',
	        ".DB_PREFIX."reports.task AS 'task',
	        ".DB_PREFIX."users.login AS 'autor',
	        ".DB_PREFIX."reports.iduser AS 'iduser',
	        ".DB_PREFIX."reports.type AS 'type',
	        ".DB_PREFIX."symbol2all.iduser 
	        	FROM ".DB_PREFIX."users, ".DB_PREFIX."reports LEFT JOIN ".DB_PREFIX."symbol2all 
	        	ON ".DB_PREFIX."symbol2all.idrecord=".DB_PREFIX."reports.id AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4
				WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0 AND ".DB_PREFIX."reports.secret=0".$fsql_cat.$fsql_stat."
				ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
	  echo '<div class="news_div '.(($rec['type']==1)?'game_news':'system_news').'">
	<div class="news_head"><input type="checkbox" name="report[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' /><strong><a href="readactrep.php?rid='.$rec['id'].'">'.StripSlashes($rec['label']).'</a></strong></span>';
	  
	  echo '<p><span>['.Date ('d. m. Y - H:i:s',$rec['datum']).']</span> '.$rec['autor'].'<br /> <strong>Úkol: </strong>'
	.StripSlashes($rec['task']).'</p></div>
</div>';
	}
?>

<div>
<input type="hidden" name="symbolid" value="<?php echo $_REQUEST['rid']; ?>" />
<input type="submit" value="Uložit změny" name="addsymbol2ar" class="submitbutton" />
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