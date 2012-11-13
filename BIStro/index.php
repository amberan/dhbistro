<?php
	require_once ('./inc/func_main.php');
	pageStart ('Aktuality');
	mainMenu (1);
	deleteUnread (5,0);
	sparklets ('<strong>aktuality</strong>',(($usrinfo['right_power'])?'<a href="dashboard.php">zobrazit nástěnku</a>; <a href="newnews.php">přidat aktualitu</a>':'<a href="dashboard.php">zobrazit nástěnku</a>'));
	// zpracovani filtru
	if (!isset($_REQUEST['kategorie'])) {
	  $f_cat=0;
	} else {
	  $f_cat=$_REQUEST['kategorie'];
	}
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	switch ($f_cat) {
	  case 0: $fsql_cat=''; break;
	  case 1: $fsql_cat=' AND '.DB_PREFIX.'news.kategorie=1 '; break;
	  case 2: $fsql_cat=' AND '.DB_PREFIX.'news.kategorie=2 '; break;
	  default: $fsql_cat='';
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'news.datum DESC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'news.datum ASC '; break;
	  case 3: $fsql_sort=' '.DB_PREFIX.'users.login ASC '; break;
	  case 4: $fsql_sort=' '.DB_PREFIX.'users.login DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'news.datum DESC ';
	}
	//
	function filter () {
	  global $f_cat;
		global $f_sort;
	  echo '<form action="index.php" method="post" id="filter">
<!-- FILTR DOCASNE ZRUSEN, ABY SE OTESTOVALO, JESTLI JE VUBEC POTREBA
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechny</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>herní</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>systémové</option>
</select> aktuality a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>data sestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>data vzestupně</option>
	<option value="3"'.(($f_sort==3)?' selected="selected"':'').'>jména autora vzestupně</option>
	<option value="4"'.(($f_sort==4)?' selected="selected"':'').'>jména autora sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
-->
</form>';
	}
// dashboard
?>
<div id="dashboard">
<fieldset><legend><h2>Osobní nástěnka</h2></legend>
	<h3>Rozpracovaná nedokončená hlášení: <?php
				$sql_r="SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id' FROM ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.iduser=".$usrinfo['id']." AND ".DB_PREFIX."reports.status=0 AND ".DB_PREFIX."reports.deleted=0 ORDER BY ".DB_PREFIX."reports.label ASC";
				$res_r=MySQL_Query ($sql_r);
				$rec_count = MySQL_Num_Rows($res_r);
				if (MySQL_Num_Rows($res_r)) {
					$reports=Array();
					while ($rec_r=MySQL_Fetch_Assoc($res_r)) {
						$reports[]='<a href="./readactrep.php?rid='.$rec_r['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($rec_r['label']).'</a>';
					}
					echo $rec_count.'</h3><p>'.implode ($reports,'<br />');
				} else {
					echo $rec_count.'</h3><p>Nemáte žádná nedokončená hlášení.';
				} ?></p>
	<div class="clear">&nbsp;</div>
				<h3>Přiřazené neuzavřené případy: <?php
			$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." ORDER BY ".DB_PREFIX."cases.title ASC";
			$pers=MySQL_Query ($sql);
			$rec_count = MySQL_Num_Rows($pers);
			$cases=Array();
			while ($perc=MySQL_Fetch_Assoc($pers)) {
				$cases[]='<a href="./readcase.php?rid='.$perc['id'].'&hidenotes=0">'.StripSlashes ($perc['title']).'</a>';
			}
			echo $rec_count.'</h3><p>'.((implode($cases, '<br />')<>"")?implode($cases, '<br />'):'<em>Nemáte žádný přiřazený neuzavřený případ.</em>');
			?></p>
	<div class="clear">&nbsp;</div>
</fieldset>
</div>

<?php 	
	filter();
	// vypis aktualit
	$sql="SELECT
	        ".DB_PREFIX."news.datum AS 'datum',
	        ".DB_PREFIX."news.nadpis AS 'nadpis',
	        ".DB_PREFIX."news.obsah AS 'obsah',
	        ".DB_PREFIX."users.login AS 'autor',
	        ".DB_PREFIX."news.kategorie AS 'kategorie'
				FROM ".DB_PREFIX."news, ".DB_PREFIX."users
				WHERE ".DB_PREFIX."news.iduser=".DB_PREFIX."users.id ".$fsql_cat."
				ORDER BY ".$fsql_sort."LIMIT 10";
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
	  echo '<div class="news_div '.(($rec['kategorie']==1)?'game_news':'system_news').'">
	<div class="news_head"><h2>'.StripSlashes($rec['nadpis']).'</h2>
	<p><span>['.Date ('d. m. Y - H:i:s',$rec['datum']).']</span> <strong>'.$rec['autor'].'</strong></p></div>
	<div>'.StripSlashes($rec['obsah']).'</div>
</div>';
	}
?>
<?php
	pageEnd ();
?>