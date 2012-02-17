<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava případu');
	mainMenu (3);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>úprava případu</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."cases WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>
<form action="proccase.php" method="post" id="inputform">
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
	  <label for="status">Stav:</label>
		<select name="status" id="status">
		  <option value="0"<?php if ($rec['status']==0) { echo ' selected="selected"'; } ?>>otevřený</option>
			<option value="1"<?php if ($rec['status']==1) { echo ' selected="selected"'; } ?>>uzavřený</option>
		</select>
	</div>
	<div>
	  <label for="contents">Obsah:</label>
	</div>
	<div>
	  <textarea cols="80" rows="7" name="contents" id="contents"><?php echo StripSlashes($rec['contents']); ?></textarea>
	</div>
	<div>
	  <input type="hidden" name="caseid" value="<?php echo $rec['id']; ?>" />
	  <input type="submit" name="editcase" id="submitbutton" value="Uložit změny" />
	</div>
</form>
<hr />
<form action="addp2c.php" method="post" class="otherform">
	<p>
		Případu můžete přiřadit osoby, které do něj patří. Opačnou akci lze provést u osoby, kde přiřazujete pro osobě případy, do kterých patří.
		Akce jsou si rovnocenné a je tedy nutná pouze jedna z nich.
	</p>
	<div>
		<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="submit" value="Přidat osobu" name="setperson" class="submitbutton" />
	</div>
</form>
<ul>
<?php
	$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."c2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."c2p.idperson AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
	$pers=MySQL_Query ($sql);
	while ($perc=MySQL_Fetch_Assoc($pers)) {
		echo '<li><a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a> &mdash; <a href="proccase.php?delperson='.$perc['id'].'&amp;caseid='.$_REQUEST['caseid'].'" onclick="'."return confirm('Opravdu odebrat osobu &quot;".implode(', ',Array(StripSlashes($perc['surname']),StripSlashes($perc['name'])))."&quot; ze skupiny?');".'">odebrat ze skupiny</a></li>';
	}
?>
</ul>
<hr />
<form action="proccase.php" method="post" enctype="multipart/form-data" class="otherform">
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
		<input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editcase.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" name="uploadfile" value="Nahrát soubor k případu" class="submitbutton" /> 
	</div>
</form>
<ul>
<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 ORDER BY ".DB_PREFIX."data.originalname ASC";
	} else {
	  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="getfile.php?idfile='.$rec['id'].'">'.StripSlashes($rec['title']).'</a> &mdash; <a href="proccase.php?deletefile='.$rec['id'].'&amp;caseid='.$_REQUEST['rid'].'" onclick="'."return confirm('Opravdu odebrat soubor &quot;".StripSlashes($rec['title'])."&quot; náležící k případu?');".'">smazat soubor</a></li>';
	}
?>
</ul>
<?php
		} else {
		  echo '<div id="obsah"><p>Skupina neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>