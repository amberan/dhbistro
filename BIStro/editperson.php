<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava osoby');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>úprava osoba</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."persons WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>
<div id="obsah">
	<p>Portréty nahrávejte pokud možno ve velikosti 100x130 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen portréty, o rozmazané postavy nebude nouze v přílohách.</p>
	<form action="procperson.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset><legend><h2>Základní údaje</h2></legend>
			<div id="info">
				<h3><label for="name">Jméno:</label></h3>
				<input type="text" name="name" id="name" value="<?php echo StripSlashes($rec['name']); ?>" />
				<div class="clear">&nbsp;</div>
				<h3><label for="surname">Příjmení:</label></h3>
				<input type="text" name="surname" id="surname" value="<?php echo StripSlashes($rec['surname']); ?>" />
				<div class="clear">&nbsp;</div>
				<h3><label for="side">Strana:</label></h3>
					<select name="side" id="side">
						<option value="0"<?php if ($rec['side']==0) { echo ' selected="selected"'; } ?>>neznámá</option>
						<option value="1"<?php if ($rec['side']==1) { echo ' selected="selected"'; } ?>>světlo</option>
						<option value="2"<?php if ($rec['side']==2) { echo ' selected="selected"'; } ?>>tma</option>
						<option value="3"<?php if ($rec['side']==3) { echo ' selected="selected"'; } ?>>člověk</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="power">Síla:</label></h3>
					<select name="power" id="power">
						<option value="0"<?php if ($rec['power']==0) { echo ' selected="selected"'; } ?>>neznámá</option>
						<option value="1"<?php if ($rec['power']==1) { echo ' selected="selected"'; } ?>>1. kategorie</option>
						<option value="2"<?php if ($rec['power']==2) { echo ' selected="selected"'; } ?>>2. kategorie</option>
						<option value="3"<?php if ($rec['power']==3) { echo ' selected="selected"'; } ?>>3. kategorie</option>
						<option value="4"<?php if ($rec['power']==4) { echo ' selected="selected"'; } ?>>4. kategorie</option>
						<option value="5"<?php if ($rec['power']==5) { echo ' selected="selected"'; } ?>>5. kategorie</option>
						<option value="6"<?php if ($rec['power']==6) { echo ' selected="selected"'; } ?>>6. kategorie</option>
						<option value="7"<?php if ($rec['power']==7) { echo ' selected="selected"'; } ?>>7. kategorie</option>
						<option value="8"<?php if ($rec['power']==8) { echo ' selected="selected"'; } ?>>mimo kategorie</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="spec">Specializace:</label></h3>
					<select name="spec" id="spec">
						<option value="0"<?php if ($rec['spec']==0) { echo ' selected="selected"'; } ?>>neznámá</option>
						<option value="1"<?php if ($rec['spec']==1) { echo ' selected="selected"'; } ?>>bílý mág</option>
						<option value="2"<?php if ($rec['spec']==2) { echo ' selected="selected"'; } ?>>černý mág</option>
						<option value="3"<?php if ($rec['spec']==3) { echo ' selected="selected"'; } ?>>léčitel</option>
						<option value="4"<?php if ($rec['spec']==4) { echo ' selected="selected"'; } ?>>obrateň</option>
						<option value="5"<?php if ($rec['spec']==5) { echo ' selected="selected"'; } ?>>upír</option>
						<option value="6"<?php if ($rec['spec']==6) { echo ' selected="selected"'; } ?>>vlkodlak</option>
						<option value="7"<?php if ($rec['spec']==7) { echo ' selected="selected"'; } ?>>vědma</option>
						<option value="8"<?php if ($rec['spec']==8) { echo ' selected="selected"'; } ?>>zaříkávač</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="phone">Telefon:</label></h3>
				<input type="text" name="phone" id="phone" value="<?php echo StripSlashes($rec['phone']); ?>" />
				<div class="clear">&nbsp;</div>
				<h3><label for="portrait">Nový portrét:</label></h3>
				<input type="file" name="portrait" id="portrait" />
				<div class="clear">&nbsp;</div>
				<h3><label for="secret">Přísně tajné:</label></h3>
					<input type="radio" name="secret" id="secret" value="0" <?php if ($rec['secret']==0) { echo 'checked'; } ?>/>ne<br/>
					<h3><label>&nbsp;</label></h3><input type="radio" name="secret" id="secret" value="1"<?php if ($rec['secret']==1) { echo 'checked'; } ?>>ano
				<div class="clear">&nbsp;</div>
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- náseduje popis osoby -->
		<fieldset><legend><h2>Popis osoby</h2></legend>
			<div class="field-text">
				<textarea cols="80" rows="7" name="contents" id="contents"><?php echo StripSlashes($rec['contents']); ?></textarea>
			</div>
			<!-- end of .field-text -->
		</fieldset>
		<input type="hidden" name="personid" value="<?php echo $rec['id']; ?>" />
		<input type="submit" name="editperson" id="submitbutton" value="Uložit" />
	</form>

	
<form action="procperson.php" method="post" class="otherform">
	<p>
		Osobě můžete přiřadit skupiny, do kterých patří. Opačnou akci lze provést u skupiny, kde přiřazujete pro změnu osoby dané skupině.
		Akce jsou si rovnocenné a je tedy nutná pouze jedna z nich.
	</p>
<?php
	$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."groups LEFT JOIN ".DB_PREFIX."g2p ON ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."groups.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." WHERE ".DB_PREFIX."groups.deleted=0 ORDER BY ".DB_PREFIX."groups.title ASC";
	if ($usrinfo['right_power']) {
		$res=MySQL_Query ($sql);
		while ($rec=MySQL_Fetch_Assoc($res)) {
			echo '<div>
			<input type="checkbox" name="group[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' />
			<label>'.StripSlashes ($rec['title']).'</label>
		</div>';
		}
	} else {
		$res=MySQL_Query ($sql);
		while ($rec=MySQL_Fetch_Assoc($res)) {
			echo '<div>'.
			(($rec['secret'])?'<input type="checkbox" name="group[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' />
			<label>'.$rec['title'].'</label>':(($rec['iduser'])?'<input type="hidden" name="group[]" value="'.$rec['id'].'" />':'')).'
		</div>';
		}
	}
?>
	<div>
		<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="submit" value="Uložit změny" name="setgroups" class="submitbutton" />
	</div>
</form>
<hr />
<form action="procperson.php" method="post" enctype="multipart/form-data" class="otherform">
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
		<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editperson.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" name="uploadfile" value="Nahrát soubor k osobě" class="submitbutton" /> 
	</div>
</form>
<ul>
<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 ORDER BY ".DB_PREFIX."data.originalname ASC";
	} else {
	  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="getfile.php?idfile='.$rec['id'].'">'.StripSlashes($rec['title']).'</a> &mdash; <a href="procperson.php?deletefile='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'" onclick="'."return confirm('Opravdu odebrat osobu &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'">smazat soubor</a></li>';
	}
?>
</ul>
<hr />

	<p>K osobě si můžete připsat kolik chcete poznámek.</p>
	<!-- následuje seznam poznámek -->
	<?php // generování poznámek
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
			$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		$res=MySQL_Query ($sql);
		$i=0;
		while ($rec=MySQL_Fetch_Assoc($res)) { 
			$i++;
			if($i==1){ ?>
	<fieldset><legend><strong>Poznámky</strong></legend>
	<div id="poznamky"><?php
			}
			if($i>1){?>
		<hr /><?php
			} ?>
		<div class="poznamka">
			<h4><?php echo(StripSlashes($rec['title'])).' - '.(StripSlashes($rec['user']));?><?php
			if ($rec['secret']==0) echo ' (veřejná)';
			if ($rec['secret']==1) echo ' (tajná)';
			if ($rec['secret']==2) echo ' (soukromá)';
			?></h4>
			<div><?php echo(StripSlashes($rec['note'])); ?></div>
			<span class="poznamka-edit-buttons"><?php
			if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=1" title="upravit"><span class="button-text">upravit</span></a> ';
			if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>'; ?>
			</span>
		</div>
		<!-- end of .poznamka -->
	<?php }
		if($i<>0){ ?>
	</div>
	<!-- end of #poznamky -->
	</fieldset>
	<?php }
	// konec poznámek ?>


<form action="procnote.php" method="post" class="otherform">
	<ul>
	</ul>
	<p>Nová poznámka:</p>
	<div>
		<label for="notetitle">Nadpis:</label>
		<input type="text" name="title" id="notetitle" />
	</div>
	<div>
	  <label for="nsecret">Utajení:</label>
	  	<?php if ($rec['secret']!=1) { ?><input type="radio" name="secret" id="nsecret" value="0" checked/>veřejná<br/><?php }; ?>
		<input type="radio" name="secret" id="nsecret" value="1" <?php if ($rec['secret']==1){ echo 'checked';}; ?>/>tajná<br/>
		<input type="radio" name="secret" id="nsecret" value="2" />soukromá
	</div>
	<div>
		<label for="notebody">Tělo poznámka:</label>
		<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
	</div>
	<div>
		<input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'editperson.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="hidden" name="tableid" value="1" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form>
</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Osoba neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>