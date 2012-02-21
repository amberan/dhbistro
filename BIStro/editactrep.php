<?php
require_once ('./inc/func_main.php');
pageStart ('Úprava hlášení z výjezdu');
mainMenu (3);
sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>úprava hlášení z výjezdu</strong>');
$autharray=MySQL_Fetch_Assoc(MySQL_Query("SELECT ".DB_PREFIX."reports.iduser AS 'iduser', ".DB_PREFIX."reports.status AS 'status' FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid']));
$author=$autharray['iduser'];
if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || ($usrinfo['id']==$author && $autharray['status']<1))) {
	$sql="SELECT
		".DB_PREFIX."reports.id AS 'id',
		".DB_PREFIX."reports.datum AS 'datum',
		".DB_PREFIX."reports.label AS 'label',
		".DB_PREFIX."reports.task AS 'task',
		".DB_PREFIX."reports.summary AS 'summary',
		".DB_PREFIX."reports.impacts AS 'impacts',
		".DB_PREFIX."reports.details AS 'details',
		".DB_PREFIX."reports.secret AS 'secret',
		".DB_PREFIX."reports.status AS 'status',
		".DB_PREFIX."users.login AS 'autor',
		".DB_PREFIX."reports.type AS 'type'
		FROM ".DB_PREFIX."reports, ".DB_PREFIX."users
		WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.id=".$_REQUEST['rid'];
	$res=MySQL_Query ($sql);
	if ($rec=MySQL_Fetch_Assoc($res)) {
		?>
<form action="procactrep.php" method="post" id="inputform">
<div>
<label for="label">Označení výjezdu:</label>
<input type="text" name="label" id="label" value="<?php echo StripSlashes($rec['label']); ?>" />
</div>
<div>
<label for="task">Úkol:</label>
<input type="text" name="task" id="task" value="<?php echo StripSlashes($rec['task']); ?>" />
</div>
<div>
<label for="secret">Přísně tajné:</label>
<select name="secret" id="secret">
<option value="0"<?php if ($rec['secret']==0) { echo ' selected="selected"'; } ?>>ne</option>
<option value="1"<?php if ($rec['secret']==1) { echo ' selected="selected"'; } ?>>ano</option>
</select>
</div>
<div>
<label for="status">Stav:</label>
<select name="status" id="status">
<option value="0"<?php if ($rec['status']==0) { echo ' selected="selected"'; } ?>>rozpracované</option>
<option value="1"<?php if ($rec['status']==1) { echo ' selected="selected"'; } ?>>dokončené</option>
<?php if ($usrinfo['right_text']) {
	echo '<option value="2"'; 
	if ($rec['status']==2) { echo ' selected="selected"'; } 
	echo '>analyzované</option>';
	}
?>
</select>
</div>
<div>
<label for="summary">Shrnutí:</label>
</div>
<div>
<textarea cols="80" rows="7" name="summary" id="summary"><?php echo StripSlashes($rec['summary']); ?></textarea>
</div>
<div>
<label for="impacts">Možné dopady:</label>
</div>
<div>
<textarea cols="80" rows="7" name="impacts" id="impacts"><?php echo StripSlashes($rec['impacts']); ?></textarea>
</div>
<div>
<label for="details">Podrobný popis průběhu:</label>
</div>
<div>
<textarea cols="80" rows="7" name="details" id="details"><?php echo StripSlashes($rec['details']); ?></textarea>
</div>
<div>
<input type="hidden" name="reportid" value="<?php echo $rec['id']; ?>" />
<input type="submit" name="editactrep" id="submitbutton" value="Uložit změny" />
</div>
</form>
<hr />
<form action="addp2ar.php" method="post" class="otherform">
<p>
Toto jsou osoby aktuálně přiřazené k hlášení.
</p>
<ul>
<?php
$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."ar2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."ar2p.idperson AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
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
<form action="addar2c.php" method="post" class="otherform">
<p>
Případy, ke kterým je hlášení přiřazeno.
</p>
<ul>
<?php
$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."cases.title ASC";
$pers=MySQL_Query ($sql);
while ($perc=MySQL_Fetch_Assoc($pers)) {
echo '<li><a href="readcase.php?rid='.$perc['id'].'">'.$perc['title'].'</a>';
}
?>
</ul>
<div>
<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
<input type="submit" value="Přiřadit k případu" name="setperson" class="submitbutton" />
</div>
</form>

<hr />
<form action="procactrep.php" method="post" enctype="multipart/form-data" class="otherform">
<p>K hlášení je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</p>
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
<input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
<input type="hidden" name="backurl" value="<?php echo 'editactrep.php?rid='.$_REQUEST['rid']; ?>" />
<input type="submit" name="uploadfile" value="Nahrát soubor k případu" class="submitbutton" />
</div>
</form>
<ul>
<?php
if ($usrinfo['right_power']) {
$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=4 ORDER BY ".DB_PREFIX."data.originalname ASC";
} else {
$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=4 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
}
$res=MySQL_Query ($sql);
while ($rec=MySQL_Fetch_Assoc($res)) {
echo '<li><a href="getfile.php?idfile='.$rec['id'].'">'.StripSlashes($rec['title']).'</a> &mdash; <a href="procactrep.php?deletefile='.$rec['id'].'&amp;reportid='.$_REQUEST['rid'].'" onclick="'."return confirm('Opravdu odebrat soubor &quot;".StripSlashes($rec['title'])."&quot; náležící k hlášení?');".'">smazat soubor</a></li>';
}
?>
</ul>
<?php
} else {
echo '<div id="obsah"><p>Hlášení neexistuje.</p></div>';
}
} else {
echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
}
pageEnd ();
?>