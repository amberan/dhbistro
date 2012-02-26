<?php
require_once ('./inc/func_main.php');
$reportarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT * FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid'])); // načte data z DB
$type=intval($reportarray['type']); // určuje typ hlášení
	$typestring=(($type==1)?'výjezd':(($type==2)?'výslech':'?')); //odvozuje slovní typ hlášení
$author=$reportarray['iduser']; // určuje autora hlášení

// následuje generování hlavičky
pageStart ('Úprava hlášení'.(($type==1)?' z výjezdu':(($type==2)?' z výslechu':'')));
mainMenu (3);
sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>úprava hlášení'.(($type==1)?' z výjezdu':(($type==2)?' z výslechu':'')).'</strong>');

// kalendář
function date_picker($name, $startyear=NULL, $endyear=NULL) {
	global $aday;
	global $amonth;
	global $ayear;
	if($startyear==NULL) $startyear = date("Y")-10;
	if($endyear==NULL) $endyear=date("Y")+5;

	$months=array('','Leden','Únor','Březen','Duben','Květen',
			'Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec');

	// roletka dnů
	$html="<select class=\"day\" name=\"".$name."day\">";
	for($i=1;$i<=31;$i++)
	{
		$html.="<option ".(($i==$aday)?' selected':'')." value='$i'>$i</option>";
	}
	$html.="</select> ";

	// roletka měsíců
	$html.="<select class=\"month\" name=\"".$name."month\">";

	for($i=1;$i<=12;$i++)
	{
		$html.="<option ".(($i==$amonth)?' selected':'')." value='$i'>$months[$i]</option>";
	}
	$html.="</select> ";

	// roletka let
	$html.="<select class=\"year\" name=\"".$name."year\">";

	for($i=$startyear;$i<=$endyear;$i++)
	{
		$html.="<option ".(($i==$ayear)?' selected':'')." value='$i'>$i</option>";
	}
	$html.="</select> ";

		return $html;
}

if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || ($usrinfo['id']==$author && $reportarray['status']<1))) {
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
		".DB_PREFIX."reports.type AS 'type',
		".DB_PREFIX."reports.adatum AS 'adatum',
		".DB_PREFIX."reports.start AS 'start',
		".DB_PREFIX."reports.end AS 'end',
		".DB_PREFIX."reports.energy AS 'energy',
		".DB_PREFIX."reports.inputs AS 'inputs'
		FROM ".DB_PREFIX."reports, ".DB_PREFIX."users
		WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.id=".$_REQUEST['rid'];
	$res=MySQL_Query ($sql);
	if ($rec=MySQL_Fetch_Assoc($res)) {
	$aday=(Date ('j',$rec['adatum']));
	$amonth=(Date ('n',$rec['adatum']));
	$ayear=(Date ('Y',$rec['adatum']));
	?>
<form action="procactrep.php" method="post" id="inputform">
<div>
<label for="label">Označení<?php echo((($type==1)?' výjezdu':(($type==2)?' výslechu':' hlášení')));?>:</label>
<input type="text" name="label" id="label" value="<?php echo StripSlashes($rec['label']); ?>" />
</div>
<div>
<label for="task"><?php echo((($type==1)?'Úkol':(($type==2)?'Předmět výslechu':'Úkol')));?>:</label>
<input type="text" name="task" id="task" value="<?php echo StripSlashes($rec['task']); ?>" />
</div>
<div>
<label for="adatum"><?php if($type==='1'){ ?>Datum akce<?php }else if($type==='2'){ ?>Datum výslechu<?php }; ?>:</label>
<?php echo date_picker("adatum")?>
</div>
<div>
<label for="start">Začátek:</label>
<input type="start" name="start" id="start" value="<?php echo StripSlashes($rec['start']); ?>" />
</div>
<div>
<label for="end">Konec:</label>
<input type="end" name="end" id="end" value="<?php echo StripSlashes($rec['end']); ?>" />
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
<div>
<label for="energy">Energetická náročnost:</label>
</div>
<div>
<textarea cols="80" rows="7" name="energy" id="energy"><?php echo StripSlashes($rec['energy']); ?></textarea>
</div>
<div>
<div>
<label for="details">Počáteční vstupy:</label>
</div>
<div>
<textarea cols="80" rows="7" name="inputs" id="inputs"><?php echo StripSlashes($rec['inputs']); ?></textarea>
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
<?php if ($usrinfo['right_text']) echo '<input type="submit" value="Přiřadit k případu" name="setperson" class="submitbutton" />'; ?>
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
<hr />
<form action="procnote.php" method="post" class="otherform">
	<p>K hlášení si můžete připsat kolik chcete poznámek.</p>
	<p>Aktuálně připojené poznámky:</p>
	<ul>
	<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=4 AND ".DB_PREFIX."notes.deleted=0 ORDER BY ".DB_PREFIX."notes.datum DESC";
	} else {
	  $sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=4 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="readnote.php?rid='.$rec['id'].'&amp;idtable=4">'.StripSlashes($rec['title']).'</a>';
		if ($rec['secret']==0) echo ' (veřejná)';
		if ($rec['secret']==1) echo ' (tajná)';
		if ($rec['secret']==2) echo ' (soukromá)';		
		if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo ' - <a href="editnote.php?rid='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=4">upravit poznámku</a> ';
		if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo ' - <a href="procnote.php?deletenote='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editactrep.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k hlášení?');".'">smazat poznámku</a></li>';
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
		<input type="hidden" name="backurl" value="<?php echo 'editactrep.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="hidden" name="tableid" value="4" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form>
<?php
} else {
echo '<div id="obsah"><p>Hlášení neexistuje.</p></div>';
}
} else {
echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
}
pageEnd ();
?>