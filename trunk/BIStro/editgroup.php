<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava skupiny');
	mainMenu (3);
	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>úprava skupiny</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."groups WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>
<form action="procgroup.php" method="post" id="inputform">
	<div>
	  <label for="title">Název:</label>
	  <input type="text" name="title" id="title" value="<?php echo StripSlashes($rec['title']); ?>" />
	</div>
	<div>
	  <label for="secret">Přísně tajné:</label>
		<select name="secret" id="secret">
		  <option value="0"<?php if ($rec['secret']==0) { echo ' selected="selected"'; } ?>>ne</option>
			<option value="1"<?php if ($rec['secret']==1) { echo ' selected="selected"'; } ?>>ano</option>
		</select>
	</div>
	<div>
	  <label for="contents">Obsah:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="contents" id="contents"><?php echo StripSlashes($rec['contents']); ?></textarea>
	</div>
	<div>
	  <input type="hidden" name="groupid" value="<?php echo $rec['id']; ?>" />
	  <input type="submit" name="editgroup" id="submitbutton" value="Uložit změny" />
	</div>
</form>
<hr />
<form action="addp2g.php" method="post" class="otherform">
	<p>
		Toto jsou osoby aktuálně přiřazené do skupiny.
	</p>
	<ul>
	<?php
		$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."g2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		$pers=MySQL_Query ($sql);
		while ($perc=MySQL_Fetch_Assoc($pers)) {
			echo '<li><a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
		}
	?>
	</ul>
	<div>
		<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton" />
	</div>
</form>
<hr />
<form action="procgroup.php" method="post" enctype="multipart/form-data" class="otherform">
	<p>K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</p>
	<div>
		<label for="attachment">Soubor:</label>
		<input type="file" name="attachment" id="attachment" />
	</div>
	<div>
		<label for="usecret">Přísně tajné:</label>
		<select name="secret" id="usecret">
			<option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
		<input type="hidden" name="groupid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editgroup.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" name="uploadfile" value="Nahrát soubor ke skupině" class="submitbutton" /> 
	</div>
</form>
<ul>
<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=2 ORDER BY ".DB_PREFIX."data.originalname ASC";
	} else {
	  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=2 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="getfile.php?idfile='.$rec['id'].'">'.StripSlashes($rec['title']).'</a> &mdash; <a href="procgroup.php?deletefile='.$rec['id'].'&amp;groupid='.$_REQUEST['rid'].'" onclick="'."return confirm('Opravdu odebrat soubor &quot;".StripSlashes($rec['title'])."&quot; náležící ke skupině?');".'">smazat soubor</a></li>';
	}
?>
</ul>
<hr />
<form action="procnote.php" method="post" class="otherform">
	<p>K případu si můžete připsat kolik chcete poznámek.</p>
	<p>Aktuálně připojené poznámky:</p>
	<ul>
	<?php
	if ($usrinfo['right_power']) {
		$sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND ".DB_PREFIX."notes.deleted=0 ORDER BY ".DB_PREFIX."notes.datum DESC";
	} else {
	  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	}
	$res_n=MySQL_Query ($sql_n);
	while ($rec_n=MySQL_Fetch_Assoc($res_n)) {
		echo '<li><a href="readnote.php?rid='.$rec_n['id'].'&amp;idtable=2">'.StripSlashes($rec_n['title']).'</a> -'.(StripSlashes($rec_n['user']));
		if ($rec_n['secret']==0) echo ' (veřejná)';
		if ($rec_n['secret']==1) echo ' (tajná)';
		if ($rec_n['secret']==2) echo ' (soukromá)';		
		if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo ' - <a href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=2">upravit poznámku</a> ';
		if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo ' - <a href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k hlášení?');".'">smazat poznámku</a></li>';
	}
	?>
	</ul>
	<p>Nová poznámka:</p>
	<div>
		<label for="notetitle">Nadpis:</label>
		<input type="text" name="title" id="notetitle" />
	</div>
	<div>
	  <label for="nsecret">Utajení:</label>
		<select name="secret" id="nsecret">
		  <option value="0">veřejná</option>
		  <option value="1">tajná</option>
		  <option value="2">soukromá</option>
		</select>
	</div>
	<div>
		<label for="notebody">Tělo poznámka:</label>
		<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
	</div>
	<div>
		<input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editgroup.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="hidden" name="tableid" value="2" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form>
<?php
		} else {
		  echo '<div id="obsah"><p>Skupina neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>