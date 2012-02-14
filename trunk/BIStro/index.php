<?php
	require_once ('./inc/func_main.php');
	pageStart ('Aktuality');
	mainMenu (1);
	sparklets ('<strong>aktuality</strong>',(($usrinfo['right_power'])?'<a href="newnews.php">přidat aktualitu</a>':''));
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
</form>';
	}
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
				ORDER BY ".$fsql_sort;
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