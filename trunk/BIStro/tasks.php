<?php
	require_once ('./inc/func_main.php');
	
	// vlozeni noveho ukolu
	if (isset($_POST['inserttask']) && !empty($_POST['task'])) {
		pageStart ('Přidán úkol');
		mainMenu (3);
		$sql_t="INSERT INTO ".DB_PREFIX."tasks VALUES('','".mysql_real_escape_string(safeInput($_POST['task']))."','".$_POST['target']."','0','".Time()."','".$usrinfo['id']."','','')";
		MySQL_Query ($sql_t);
// Ukládání do novinek zakomentováno, protože nevím, jestli se použije. Kdyžtak SMAZAT.
//		$gidarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT id FROM ".DB_PREFIX."groups WHERE UCASE(title)=UCASE('".mysql_real_escape_string(safeInput($_POST['title']))."')"));
//		$gid=$gidarray['id'];
//		if (!isset($_POST['notnew'])) {
//			unreadRecords (2,$gid);
//		}
		sparklets ('<a href="users.php">uživatelé</a> &raquo; <strong>úkoly</strong>');
		echo '<div id="obsah"><p>Úkol přidán.</p></div>';
	} else if (isset($_POST['inserttask'])) {
			pageStart ('Přidání úkolu neúspěšné');
			mainMenu (3);
			sparklets ('<a href="users.php">uživatelé</a> &raquo; <strong>úkoly</strong>');
			echo '<div id="obsah"><p>Chyba při vytváření, ujistěte se, že jste vše provedli správně a máte potřebná práva.</p></div>';
			pageEnd ();
		
	} else {
	
	pageStart ('Úkoly');
	mainMenu (2);
	sparklets ('<a href="users.php">uživatelé</a> &raquo; <strong>úkoly</strong>');
	
	}

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
	  case 1: $fsql_cat=' WHERE '.DB_PREFIX.'tasks.status<2 '; break;
	  case 2: $fsql_cat=' WHERE '.DB_PREFIX.'tasks.status=1 '; break;
	  default: $fsql_cat='';
	}
	switch ($f_sort) {
	  case 1: $fsql_sort=' '.DB_PREFIX.'users.login ASC '; break;
	  case 2: $fsql_sort=' '.DB_PREFIX.'users.login DESC '; break;
	  default: $fsql_sort=' '.DB_PREFIX.'users.login ASC ';
	}
?>	
	<!-- Přidání úkolu -->
	<div id="filter-wrapper"><form action="tasks.php" method="post" id="filter">
	<fieldset>
	<legend>Přidej úkol</legend>
	<p><label for="task">Zadání:</label>
	<input type="text" name="task" id="task" width=500px />
<?php 	
	$sql="SELECT id, login FROM ".DB_PREFIX."users WHERE deleted=0 ORDER BY login ASC";
		$res_n=MySQL_Query ($sql);
		echo '<label for="target">Uživatel:</label>
		<select name="target" id="target">';
		while ($rec_n=MySQL_Fetch_Assoc($res_n)) {
			echo '<option value="'.$rec_n['id'].'"'.(($rec_n['id']==$usrinfo['id'])?' selected="selected"':'').'>'.$rec_n['login'].'</option>';
		};
		echo '</select>';

?>		
	</p>
	<div id="filtersubmit"><input type="submit" name="inserttask" value="Zadat" /></div>
	</fieldset>
	</form></div><!-- end of #filter-wrapper -->
	
<?php 
	// filtr
	function filter () {
	  global $f_cat;
		global $f_sort;
	  echo '<div id="filter-wrapper"><form action="tasks.php" method="post" id="filter">
	<fieldset>
	  <legend>Filtr</legend>
	  <p>Vypsat <select name="kategorie">
	<option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechny</option>
	<option value="1"'.(($f_cat==1)?' selected="selected"':'').'>neuzavřené</option>
	<option value="2"'.(($f_cat==2)?' selected="selected"':'').'>dokončené</option>
</select> úkoly a seřadit je podle <select name="sort">
	<option value="1"'.(($f_sort==1)?' selected="selected"':'').'>data zadání vzestupně</option>
	<option value="2"'.(($f_sort==2)?' selected="selected"':'').'>data zadání sestupně</option>
</select>.</p>
	  <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
	</fieldset>
</form></div><!-- end of #filter-wrapper -->';
	}
	filter();
	// vypis uživatelů
	$sql="SELECT * FROM ".DB_PREFIX."tasks".$fsql_cat;//" ORDER BY ".$fsql_sort;
	echo $sql;
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr>
	  <th>Úkol</th>
	  <th>Uživatel</th>
	  <th>Stav</th>
	  <th>Zadáno</th>
	  <th>Zadavatel</th>
	  <th>Upraveno</th>
	  <th>Upravil</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.(($even%2==0)?'even':'odd').'">
	<td>'.StripSlashes($rec['task']).'</td>
	<td>'.getAuthor($rec['iduser'],0).'</td>
	<td>'.$rec['status'].'</td>
	<td>'.(($rec['created'])?Date ('d. m. Y (H:i:s)',$rec['created']):'nikdy').'</td>
	<td>'.getAuthor($rec['created_by'],0).'</td>
	<td>'.(($rec['modified'])?Date ('d. m. Y (H:i:s)',$rec['created']):'nikdy').'</td>
	<td>'.(($rec['modified_by'])?getAuthor($rec['modified_by']):'nikdo').'</td>
	<td>dokončit | schválit | zrušit</td>
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>
';
	} else {
	  echo '<div id="obsah"><p>Žádné úkoly neodpovídají výběru.</p></div>';
	}
?>
<?php
	pageEnd ();
?>
