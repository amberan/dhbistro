<?php
	require_once ('./inc/func_main.php');
	auditTrail(4, 1, 0);
	pageStart ('Hlášení');
	mainMenu (5);
        $custom_Filter = custom_Filter(4);
        if ($_SERVER["SERVER_NAME"] == 'enigma.dhbistro.cz') {
            sparklets ('<strong>'.$hlaseniM.'</strong>','<a href="newactrep.php?type=1">nová zakázka na amulet</a>; <a href="newactrep.php?type=2">nová zakázka na informace</a>');
        } else {
            sparklets ('<strong>'.$hlaseniM.'</strong>','<a href="newactrep.php?type=1">nové hlášení z výjezdu</a>; <a href="newactrep.php?type=2">nové hlášení z výslechu</a>');
        }
// zpracovani filtru
	if (!isset($custom_Filter['type'])) {
	  $f_cat=0;
	} else {
	  $f_cat=$custom_Filter['type'];
	}
	if (!isset($custom_Filter['sort'])) {
	  $f_sort=6;
	} else {
	  $f_sort=$custom_Filter['sort'];
	}
	if (!isset($custom_Filter['status'])) {
		$f_stat=2;
	} else {
		$f_stat=$custom_Filter['status'];
	}
	if (!isset($custom_Filter['my'])) {
		$f_my=0;
	} else {
		$f_my=1;
	}
	if (!isset($custom_Filter['conn'])) {
		$f_conn=0;
	} else {
		$f_conn=1;
	}
	if (!isset($custom_Filter['sec'])) {
		$f_sec=0;
	} else {
		$f_sec=1;
	}
        if (!isset($custom_Filter['archiv'])) {
		$f_archiv=0;
	} else {
		$f_archiv=1;
	}
        if (!isset($custom_Filter['new'])) {
		$f_new=0;
	} else {
		$f_new=1;
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
		default: $fsql_stat=' AND '.DB_PREFIX.'reports.status=1 ';
	}
	switch ($f_my) {
		case 0: $fsql_my=''; break;
		case 1: $fsql_my=' AND '.DB_PREFIX.'reports.iduser='.$usrinfo['id'].' '; break;
		default: $fsql_my='';
	}
	switch ($f_conn) {
		case 0: $fsql_conn=''; $fsql_conn2=''; break;
		case 1: $fsql_conn=' AND '.DB_PREFIX.'ar2c.idreport IS NULL '; $fsql_conn2=' LEFT JOIN '.DB_PREFIX.'ar2c ON '.DB_PREFIX.'reports.id='.DB_PREFIX.'ar2c.idreport '; break;
		default: $fsql_conn=''; $fsql_conn2='';
	}
	switch ($f_sec) {
		case 0: $fsql_sec=''; break;
		case 1: $fsql_sec=' AND '.DB_PREFIX.'reports.secret=1 '; break;
		default: $fsql_sec='';
	}
        switch ($f_archiv) {
		case 0: $fsql_archiv=' AND '.DB_PREFIX.'reports.status<>3 '; break;
		case 1: $fsql_archiv=''; break;
		default: $fsql_archiv='';
	}
	// filtr samotny
	function filter () {
	  global $f_cat, $f_sort, $f_stat, $f_my, $f_conn, $fsql_conn2, $f_sec, $f_new, $f_archiv, $usrinfo, $hlaseniM;
          echo '<div id="filter-wrapper"><form action="reports.php" method="get" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="status">
	<option value="0"'.(($f_stat==0)?' selected="selected"':'').'>všechna</option>
	<option value="1"'.(($f_stat==1)?' selected="selected"':'').'>rozpracovaná</option>
	<option value="2"'.(($f_stat==2)?' selected="selected"':'').'>dokončená</option>
	<option value="3"'.(($f_stat==3)?' selected="selected"':'').'>analyzovaná</option>
</select> '.$hlaseniM.' <select name="type">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechna</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>z výjezdu</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>z výslechu</option>
</select> a seřadit je podle <select name="sort" >
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>data '.$hlaseniM.' sestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>data '.$hlaseniM.' vzestupně</option>
	<option value="3"'.(($f_sort==3)?' selected="selected"':'').'>jména autora vzestupně</option>
	<option value="4"'.(($f_sort==4)?' selected="selected"':'').'>jména autora sestupně</option>
	<option value="5"'.(($f_sort==5)?' selected="selected"':'').'>data výjezdu vzestupně</option>
	<option value="6"'.((!$f_sort==6)?'':' selected="selected"').'>data výjezdu sestupně</option>
</select>.</p>
        <table class="filter">
	<tr class="filter">
	<td class="filter"><input type="checkbox" name="my" value="my" class="checkbox"'.(($f_my==1)?' checked="checked"':'').' /> Jen moje.</td>
        <td class="filter"><input type="checkbox" name="new" value="new" class="checkbox"'.(($f_new==1)?' checked="checked"':'').' /> Jen nové.</td>
	<td class="filter"><input type="checkbox" name="conn" value="conn" class="checkbox"'.(($f_conn==1)?' checked="checked"':'').' /> Jen nepřiřazené.</td>
        </tr>
        <tr class="filter">
        <td class="filter"><input type="checkbox" name="archiv" value="archiv" class="checkbox"'.(($f_archiv==1)?' checked="checked"':'').' /> I archiv.</td>';
        if ($usrinfo['right_power']) {
		echo '<td class="filter"><input type="checkbox" name="sec" value="sec" class="checkbox"'.(($f_sec==1)?' checked="checked"':'').' /> Jen tajné.</td></tr></table>';
	}  else {
		echo '</tr></table>';
	}
        echo '<div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	/* Stary vypis hlášení
	if ($usrinfo['right_power']) {
		$sql="SELECT
			".DB_PREFIX."reports.id AS 'id',
	        ".DB_PREFIX."reports.datum AS 'datum',
	        ".DB_PREFIX."reports.adatum AS 'adatum',
	        ".DB_PREFIX."reports.label AS 'label',
	        ".DB_PREFIX."reports.task AS 'task',
	        ".DB_PREFIX."users.login AS 'autor',
	        ".DB_PREFIX."reports.type AS 'type',
	        ".DB_PREFIX."reports.status AS 'status' 
	        	FROM ".DB_PREFIX."users, ".DB_PREFIX."reports".$fsql_conn2."
				WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0".$fsql_cat.$fsql_stat.$fsql_my.$fsql_conn.$fsql_sec.$fsql_archiv."
				ORDER BY ".$fsql_sort;
	} else {
		$sql="SELECT
			".DB_PREFIX."reports.id AS 'id',
	        ".DB_PREFIX."reports.datum AS 'datum',
	        ".DB_PREFIX."reports.adatum AS 'adatum',
	        ".DB_PREFIX."reports.label AS 'label',
	        ".DB_PREFIX."reports.task AS 'task',
	        ".DB_PREFIX."reports.status AS 'status',
	        ".DB_PREFIX."users.login AS 'autor',
	        ".DB_PREFIX."reports.iduser AS 'riduser',
	        ".DB_PREFIX."reports.type AS 'type' 
	        	FROM ".DB_PREFIX."users, ".DB_PREFIX."reports".$fsql_conn2." 
				WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0 AND ".DB_PREFIX."reports.secret=0".$fsql_cat.$fsql_stat.$fsql_my.$fsql_conn.$fsql_archiv."
				ORDER BY ".$fsql_sort;
	} Alternativni vypis hlaseni */
    $sql="SELECT
                ".DB_PREFIX."reports.id AS 'id',
                ".DB_PREFIX."reports.datum AS 'datum',
                ".DB_PREFIX."reports.adatum AS 'adatum',
                ".DB_PREFIX."reports.label AS 'label',
                ".DB_PREFIX."reports.task AS 'task',
                ".DB_PREFIX."reports.status AS 'status',
                ".DB_PREFIX."users.login AS 'autor',
                ".DB_PREFIX."reports.iduser AS 'riduser',
                ".DB_PREFIX."reports.type AS 'type' 
                    FROM ".DB_PREFIX."users, ".DB_PREFIX."reports".$fsql_conn2." 
                    WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.deleted=0 AND ".DB_PREFIX."reports.secret<=".$usrinfo['right_power'].$fsql_cat.$fsql_stat.$fsql_my.$fsql_conn.$fsql_archiv."
                    ORDER BY ".$fsql_sort;
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
            if ($f_new==0 || ($f_new==1 && searchRecord(4,$rec['id']))) {
                echo '<div class="news_div '.(($rec['type']==1)?'game_news':'system_news').((searchRecord(4,$rec['id']))?' unread_record':'').'">
                <div class="news_head"><strong><a href="readactrep.php?rid='.$rec['id'].'&amp;hidenotes=0&amp;truenames=0">'.StripSlashes($rec['label']).'</a></strong>';
                  if (($usrinfo['right_text']) || ($usrinfo['id']==$rec['riduser'] && $rec['status']<1)) {
                        echo '	 | <td><a href="editactrep.php?rid='.$rec['id'].'">upravit</a> | <a href="procactrep.php?delete='.$rec['id'].'&amp;table=4" onclick="'."return confirm('Opravdu smazat &quot;".$hlaseniM."&quot; &quot;".StripSlashes($rec['label'])."&quot;?');".'">smazat</a></td>';
                  } else {
                        echo '   | <td><a href="newnote.php?rid='.$rec['id'].'&idtable=8">přidat poznámku</a></td>';
                        }
                  echo '</span>
                <p><span>['.Date ('d. m. Y - H:i:s',$rec['datum']).']</span> '.$rec['autor'].', Datum akce: <span>['.Date ('d. m. Y - H:i:s',$rec['adatum']).']</span><br /> <strong>Úkol: </strong>'
                .StripSlashes($rec['task']).'&nbsp; <strong>Stav:</strong> ';
                  if(($rec['status'])=='0') echo 'Rozpracované';
                  if(($rec['status'])=='1') echo 'Dokončené';
                  if(($rec['status'])=='2') echo 'Analyzované';
                  if(($rec['status'])=='3') echo 'Archivované';
                  echo '</p></div></div>';
          }
	}
	pageEnd ();
?>