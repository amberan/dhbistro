<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava skupiny');
	mainMenu (3);
	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>úprava skupiny</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."groups WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
?>
<div id="obsah">
	<form action="procgroup.php" method="post" id="inputform">
			<h1><label for="title">Název:</label></h1>
			<input type="text" name="title" id="title" value="<?php echo StripSlashes($rec['title']); ?>" />
			<input type="hidden" name="secret" value="<?php echo (($rec['secret']==0)?'0':'1'); ?>" />
			<input type="hidden" name="contents" value="<?php echo StripSlashes($rec['contents']); ?>" />
			<input type="hidden" name="groupid" value="<?php echo $rec['id']; ?>" />
			<input type="submit" name="editgroup" id="submitbutton" value="Uložit změny" />
	</form>
	<fieldset><legend><h2>Obecné informace</h2></legend>
		<div id="info">	
			<form action="procgroup.php" method="post" id="inputform">
				<input type="hidden" name="title" value="<?php echo StripSlashes($rec['title']); ?>" />
				<h3><label for="secret">Přísně tajné: </label></h3>
				<select name="secret" id="secret">
					<option value="0"<?php if ($rec['secret']==0) { echo ' selected="selected"'; } ?>>ne</option>
					<option value="1"<?php if ($rec['secret']==1) { echo ' selected="selected"'; } ?>>ano</option>
				</select>
				<input type="hidden" name="contents" value="<?php echo StripSlashes($rec['contents']); ?>" />
				<input type="hidden" name="groupid" value="<?php echo $rec['id']; ?>" />
				<input type="submit" name="editgroup" id="submitbutton" value="Uložit změny" />
			</form>
	
			<form action="addp2g.php" method="post" class="otherform">
				<h3>Členové: </h3><p><?php
					$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."g2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
					$pers=MySQL_Query ($sql);
					$persons=Array();
					while ($perc=MySQL_Fetch_Assoc($pers)) {
						$persons[]='<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
					}
					echo ((implode($persons, '; ')<>"")?implode($persons, '; '):'<em>Nejsou připojeny žádné osoby.</em>');
				?></p>
				<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton" />
			</form>
		</div>
	</fieldset>

	<fieldset><legend><h2>Popis:</h2></legend>
		<form action="procgroup.php" method="post" id="inputform">
			<textarea cols="80" rows="7" name="contents" id="contents"><?php echo StripSlashes($rec['contents']); ?></textarea>
			<div>
				<input type="hidden" name="title" value="<?php echo StripSlashes($rec['title']); ?>" />
				<input type="hidden" name="secret" value="<?php echo (($rec['secret']==0)?'0':'1'); ?>" />
				<input type="hidden" name="groupid" value="<?php echo $rec['id']; ?>" />
				<input type="submit" name="editgroup" id="submitbutton" value="Uložit změny" />
			</div>
		</form>
	</fieldset>	
	
	<fieldset><legend><h2>Aktuálně připojené soubory:</h2></legend>
		<form action="procgroup.php" method="post" enctype="multipart/form-data" class="otherform">
			<div><span>
				<label for="attachment">Soubor:</label>
				<input type="file" name="attachment" id="attachment" />
			</span>
			<span>&nbsp;</span>
			<span>
				<label for="usecret">Přísně tajné:</label>
				<select name="secret" id="usecret">
					<option value="0">ne</option>
					<option value="1">ano</option>
				</select>
			</span>
			<span>&nbsp;</span>
			<span>
				<input type="hidden" name="groupid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editgroup.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor ke skupině" class="submitbutton" /> 
			</span></div>
			<em style="font-size:smaller;">K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em>
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
	</fieldset>

	<fieldset><legend><h2>Aktuálně připojené poznámky:</h2></legend>
		<form action="procnote.php" method="post" class="otherform">
			<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=2" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		</form>
		<ul>
		<?php
		if ($usrinfo['right_power']) {
			$sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND ".DB_PREFIX."notes.deleted=0 ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		$res_n=MySQL_Query ($sql_n);
		while ($rec_n=MySQL_Fetch_Assoc($res_n)) { ?>
			<li><a href="readnote.php?rid=<?php echo $rec_n['id']; ?>&amp;idtable=2"><?php echo StripSlashes($rec_n['title']); ?></a> - <?php echo StripSlashes($rec_n['user']); 
			if ($rec_n['secret']==0){ ?> (veřejná)<?php }
			if ($rec_n['secret']==1){ ?> (tajná)<?php }
			if ($rec_n['secret']==2){ ?> (soukromá)<?php }
			?><span class="poznamka-edit-buttons"><?php
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo ' <a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=2" title="upravit"><span class="button-text">upravit poznámku</span></a>';
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo ' <a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k hlášení?');".'" title="smazat"><span class="button-text">smazat poznámku</span></a>';
			?></span></li><?php
		}
		?>
		</ul>
	</fieldset>
</div>
<!-- end of #obsah -->
<?php
		} else {
		  echo '<div id="obsah"><p>Skupina neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>