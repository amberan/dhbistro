<?php
	require_once ('./inc/func_main.php');
	pageStart ('Hlášení');
	mainMenu (5);
	sparklets ('<strong>hlášení</strong>','<a href="newactrep.php?type=1">nové hlášení z výjezdu</a>; <a href="newactrep.php?type=2">nové hlášení z výslechu</a>');
// zpracovani filtru
	if (!isset($_REQUEST['type'])) {
	  $f_cat=0;
	} else {
	  $f_cat=$_REQUEST['type'];
	}
	if (!isset($_REQUEST['sort'])) {
	  $f_sort=1;
	} else {
	  $f_sort=$_REQUEST['sort'];
	}
	if (!isset($_REQUEST['status'])) {
		$f_stat=0;
	} else {
		$f_stat=$_REQUEST['status'];
	}
	if (!isset($_REQUEST['my'])) {
		$f_my=0;
	} else {
		$f_my=1;
	}
	if (!isset($_REQUEST['conn'])) {
		$f_conn=0;
	} else {
		$f_conn=1;
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
	  default: $fsql_sort=' '.DB_PREFIX.'reports.datum DESC ';
	}
	switch ($f_stat) {
		case 0: $fsql_stat=''; break;
		case 1: $fsql_stat=' AND '.DB_PREFIX.'reports.status=0 '; break;
		case 2: $fsql_stat=' AND '.DB_PREFIX.'reports.status=1 '; break;
		case 3: $fsql_stat=' AND '.DB_PREFIX.'reports.status=2 '; break;
		default: $fsql_stat='';
	}
	switch ($f_my) {
		case 0: $fsql_my=''; break;
		case 1: $fsql_my=' AND '.DB_PREFIX.'reports.iduser='.$usrinfo['id'].' '; break;
		default: $fsql_my='';
	}
	switch ($f_conn) {
		case 0: $fsql_conn=''; break;
		case 1: $fsql_conn=' AND '.DB_PREFIX.'ar2c.iduser IS NULL '; break;
		default: $fsql_conn='';
	}
	//
	function filter () {
	  global $f_cat;
	  global $f_sort;
	  global $f_stat;
	  global $f_my;
	  global $f_conn;
	  echo '<form action="reports.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="status">
	<option value="0"'.(($f_stat==0)?' selected="selected"':'').'>všechna</option>
	<option value="1"'.(($f_stat==1)?' selected="selected"':'').'>rozpracovaná</option>
	<option value="2"'.(($f_stat==2)?' selected="selected"':'').'>dokončená</option>
	<option value="3"'.(($f_stat==3)?' selected="selected"':'').'>analyzovaná</option>
</select> hlášení <select name="type">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechna</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>z výjezdu</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>z výslechu</option>
</select> a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>data sestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>data vzestupně</option>
	<option value="3"'.(($f_sort==3)?' selected="selected"':'').'>jména autora vzestupně</option>
	<option value="4"'.(($f_sort==4)?' selected="selected"':'').'>jména autora sestupně</option>
</select>.</br>
	<input type="checkbox" name="my" value="my" class="checkbox"'.(($f_my==1)?' checked="checked"':'').' /> Jen moje.<br />
	<input type="checkbox" name="conn" value="conn" class="checkbox"'.(($f_conn==1)?' checked="checked"':'').' /> Jen nepřiřazené.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form>';
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
	        ".DB_PREFIX."reports.status AS 'status',
	        ".DB_PREFIX."ar2c.iduser 
	        	FROM ".DB_PREFIX."users, ".DB_PREFIX."reports LEFT JOIN ".DB_PREFIX."ar2c 
	        	ON ".DB_PREFIX."ar2c.idreport=".DB_PREFIX."reports.id
				WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0".$fsql_cat.$fsql_stat.$fsql_my.$fsql_conn."
				ORDER BY ".$fsql_sort;
	} else {
		$sql="SELECT
			".DB_PREFIX."reports.id AS 'id',
	        ".DB_PREFIX."reports.datum AS 'datum',
	        ".DB_PREFIX."reports.label AS 'label',
	        ".DB_PREFIX."reports.task AS 'task',
	        ".DB_PREFIX."reports.status AS 'status',
	        ".DB_PREFIX."users.login AS 'autor',
	        ".DB_PREFIX."reports.iduser AS 'riduser',
	        ".DB_PREFIX."reports.type AS 'type',
	        ".DB_PREFIX."ar2c.iduser 
	        	FROM ".DB_PREFIX."users, ".DB_PREFIX."reports LEFT JOIN ".DB_PREFIX."ar2c 
	        	ON ".DB_PREFIX."ar2c.idreport=".DB_PREFIX."reports.id
				WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0 AND ".DB_PREFIX."reports.secret=0".$fsql_cat.$fsql_stat.$fsql_my.$fsql_conn."
				ORDER BY ".$fsql_sort;
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
	  echo '<div class="news_div '.(($rec['type']==1)?'game_news':'system_news').'">
	<div class="news_head"><strong><a href="readactrep.php?rid='.$rec['id'].'">'.StripSlashes($rec['label']).'</a></strong>';
	  if (($usrinfo['right_text']) || ($usrinfo['id']==$rec['riduser'] && $rec['status']<1)) {
	   	echo '	 | <td><a href="editactrep.php?rid='.$rec['id'].'">upravit</a> | <a href="procactrep.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat hlášení &quot;".StripSlashes($rec['label'])."&quot;?');".'">smazat</a></td>';
	  } else {
	  	echo '   | <td><a href="newnote.php?rid='.$rec['id'].'&idtable=4">přidat poznámku</a></td>';
	  	}
	  echo '</span>
	<p><span>['.Date ('d. m. Y - H:i:s',$rec['datum']).']</span> '.$rec['autor'].'<br /> <strong>Úkol: </strong>'
	.StripSlashes($rec['task']).'&nbsp; <strong>Stav:</strong> ';
	  if(($rec['status'])=='0') echo 'Rozpracované';
	  if(($rec['status'])=='1') echo 'Dokončené';
	  if(($rec['status'])=='2') echo 'Analyzované';
	  echo '</p></div></div>';
	}
	pageEnd ();
?>