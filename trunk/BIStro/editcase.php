<?php
	require_once ('./inc/func_main.php');
	auditTrail(3, 1, $_REQUEST['rid']);
	pageStart ('Úprava případu');
	mainMenu (3);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>úprava případu</strong>','<a href="symbols.php">přiřadit symboly</a>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."cases WHERE id=".$_REQUEST['rid']);
		if ($rec_c=MySQL_Fetch_Assoc($res)) {
?>
<div id="obsah">
	<script type="text/javascript">
	<!--
	window.onload=function(){
		FixitRight('submitbutton', 'ramecek');
	};
	-->
	</script>
	<fieldset id="ramecek"><legend><h1>Úprava případu: <?php echo(StripSlashes($rec_c['title'])); ?></h1></legend>
		<form action="proccase.php" method="post" id="inputform">
			<div id="info">
				<h3><label for="title">Název:</label></h3>
		  		<input type="text" name="title" id="title" value="<?php echo StripSlashes($rec_c['title']); ?>" />
				<div class="clear">&nbsp;</div>
				
				<h3><label for="secret">Přísně&nbsp;tajné:</label></h3>
				<input type="radio" name="secret" value="0" <?php if ($rec_c['secret']==0) { ?>checked="checked"<?php } ?>/>ne<br/>
				<h3><label>&nbsp;</label></h3><input type="radio" name="secret" value="1"<?php if ($rec_c['secret']==1) { ?>checked="checked"<?php } ?>>ano
				<div class="clear">&nbsp;</div>
	
				<h3><label for="status">Stav:</label></h3>
				<select name="status" id="status">
					<option value="0"<?php if ($rec_c['status']==0) { echo ' selected="selected"'; } ?>>otevřený</option>
					<option value="1"<?php if ($rec_c['status']==1) { echo ' selected="selected"'; } ?>>uzavřený</option>
				</select>
				<div class="clear">&nbsp;</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
				}
?>				
			</div>
			<!-- end of #info -->
			<fieldset><legend><h2>Obsah:</h2></legend>
				<textarea cols="80" rows="30" name="contents" id="contents"><?php echo StripSlashes($rec_c['contents']); ?></textarea>
			</fieldset>
			<div>
			  <input type="hidden" name="caseid" value="<?php echo $rec_c['id']; ?>" />
			  <input type="submit" name="editcase" id="submitbutton" value="Uložit změny" title="Uložit změny" />
			</div>
		</form>
	</fieldset>
	
	<fieldset><legend><h2>Řešitelé: </h2></legend>
		<form action="adds2c.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Upravit řešitele" name="setsolver" class="submitbutton editbutton" title="Upravit řešitele" />
		</form>
		<p><?php
			$sql="SELECT ".DB_PREFIX."users.id AS 'id', ".DB_PREFIX."users.login AS 'login' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."users WHERE ".DB_PREFIX."users.id=".DB_PREFIX."c2s.idsolver AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."users.login ASC";
			$pers=MySQL_Query ($sql);
			$solvers=Array();
			while ($perc=MySQL_Fetch_Assoc($pers)) {
				$solvers[]=$perc['login'];
			}
			echo ((implode($solvers, '; ')<>"")?implode($solvers, '; '):'<em>Případ nemá přiřazené řešitele.</em>');
		?></p>		
	</fieldset>

	<fieldset><legend><h2>Osoby přiřazené k případu: </h2></legend>
		<form action="addp2c.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton editbutton" title="Upravit osoby přiřazené" />
		</form>
		<p><?php
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."c2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."c2p.idperson AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."c2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."c2p.idperson AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
			}
			$pers=MySQL_Query ($sql);
			$persons=Array();
			while ($perc=MySQL_Fetch_Assoc($pers)) {
				$persons[]='<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
			}
			echo ((implode($persons, '; ')<>"")?implode($persons, '; '):'<em>Nejsou připojeny žádné osoby.</em>');
		?></p>		
	</fieldset>
	
	
	<fieldset><legend><h2>Hlášení přiřazená k případu: </h2></legend>
		<form action="addc2ar.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Změnit přiřazení hlášení" name="setreport" class="submitbutton editbutton" title="Změnit přiřazení hlášení" />
		</form>
		<ul>
		<?php
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.task AS 'task', ".DB_PREFIX."users.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."reports, ".DB_PREFIX."users WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."users.id=".DB_PREFIX."reports.iduser ORDER BY ".DB_PREFIX."reports.label ASC";
		} else {
			$sql="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.task AS 'task', ".DB_PREFIX."users.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."reports, ".DB_PREFIX."users WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."users.id=".DB_PREFIX."reports.iduser AND ".DB_PREFIX."reports.secret=0 ORDER BY ".DB_PREFIX."reports.label ASC";
		}
		$pers=MySQL_Query ($sql);
		$reports=Array();
		while ($perc=MySQL_Fetch_Assoc($pers)) {
			$reports[]='<li><a href="readactrep.php?rid='.$perc['id'].'">'.$perc['label'].'</a> - '.$perc['task'].' - <b>'.$perc['user'].'</b>';
		}
		echo ((implode($reports, '; ')<>"")?implode($reports, '; '):'<em>Nejsou připojena žádná hlášení.</em>');
		?>
		</ul>
	</fieldset>

	<!-- následuje seznam přiložených souborů -->
	<fieldset><legend><h3>Přiložené soubory</h3></legend>
		<strong><em>K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 ORDER BY ".DB_PREFIX."data.originalname ASC";
			} else {
			  $sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
			}
			$res=MySQL_Query ($sql);
			$i=0;
			while ($rec_f=MySQL_Fetch_Assoc($res)) { 
				$i++; 
				if($i==1){ ?>
		<ul id="prilozenadata">
				<?php } ?>
			<li class="soubor"><a href="getfile.php?idfile=<?php echo($rec_f['id']); ?>" title=""><?php echo(StripSlashes($rec_f['title'])); ?></a><?php if($rec_f['secret']==1){ ?> (TAJNÝ)<?php }; ?><span class="poznamka-edit-buttons"><?php
				if (($rec_f['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" title="smazat" href="proccase.php?deletefile='.$rec_f['id'].'&amp;caseid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editcase.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.StripSlashes($rec_f['title']).'&quot; náležící k případu?\')"><span class="button-text">smazat soubor</span></a>'; ?>
				</span></li><?php 
			}
			if($i<>0){ ?>
		</ul>
		<!-- end of #prilozenadata -->
		<?php 
			}else{?><br />
		<em>bez přiložených souborů</em><?php
			}
		// konec seznamu přiložených souborů ?>
	</fieldset>

	<div id="new-file" class="otherform-wrap">
		<fieldset><legend><strong>Nový soubor</strong></legend>
		<form action="proccase.php" method="post" enctype="multipart/form-data" class="otherform">
			<div>
				<strong><label for="attachment">Soubor:</label></strong>
				<input type="file" name="attachment" id="attachment" />
			</div>
			<div>
				<strong><label for="usecret">Přísně tajné:</label></strong>
			  	<?php if ($rec_c['secret']!=1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_c['secret']==1){ ?>checked="checked"<?php }; ?>/>ano
			</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<div>
				<strong><label for="fnotnew">Není nové</label></strong>
					<input type="checkbox" name="fnotnew"/><br/>
				</div>';
				}
?>			
			<div>
				<input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editcase.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor k osobě" class="submitbutton" title="Uložit"/> 
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-file .otherform-wrap -->
	
	<fieldset><legend><h2>Aktuálně připojené poznámky:</h2></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=3" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		<ul>
		<?php
		if ($usrinfo['right_power']) {
			$sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=3 AND ".DB_PREFIX."notes.deleted=0 ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=3 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		$res_n=MySQL_Query ($sql_n);
		while ($rec_n=MySQL_Fetch_Assoc($res_n)) { ?>
			<li><a href="readnote.php?rid=<?php echo $rec_n['id']; ?>&amp;idtable=3"><?php echo StripSlashes($rec_n['title']); ?></a> - <?php echo StripSlashes($rec_n['user']); 
			if ($rec_n['secret']==0){ ?> (veřejná)<?php }
			if ($rec_n['secret']==1){ ?> (tajná)<?php }
			if ($rec_n['secret']==2){ ?> (soukromá)<?php }
			?><span class="poznamka-edit-buttons"><?php
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo ' <a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=3" title="upravit"><span class="button-text">upravit poznámku</span></a>';
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