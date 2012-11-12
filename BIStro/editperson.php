<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava osoby');
	mainMenu (5);
	sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>úprava osoby</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."persons WHERE id=".$_REQUEST['rid']);
		if ($rec_p=MySQL_Fetch_Assoc($res)) {
?>
<div id="obsah">
<fieldset><legend><h1>Úprava osoby: <?php echo(StripSlashes($rec_p['surname']).', '.StripSlashes($rec_p['name'])); ?></h1></legend>
	<p id="top-text">Portréty nahrávejte pokud možno ve velikosti 100x130 bodů, symboly ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen portréty, o rozmazané postavy nebude nouze v přílohách. Symboly rovněž nahrávejte jasně rozeznatelné.</p>
	<form action="procperson.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset><legend><h2>Základní údaje</h2></legend>
		<?php if($rec_p['portrait']==NULL){ ?><img src="#" alt="portrét chybí" title="portrét chybí" id="portraitimg" class="noname"/>
		<?php }else{ ?><img src="getportrait.php?rid=<?php echo($_REQUEST['rid']); ?>" alt="<?php echo(StripSlashes($rec_p['name']).' '.StripSlashes($rec_p['surname'])); ?>" id="portraitimg" />
		<?php } ?>
		<?php if($rec_p['symbol']==NULL){ ?><img src="#" alt="symbol chybí" title="symbol chybí" id="symbolimg" class="noname"/>
		<?php }else{ ?><img src="getportrait.php?srid=<?php echo($_REQUEST['rid']); ?>" alt="<?php echo(StripSlashes($rec_p['name']).' '.StripSlashes($rec_p['surname'])); ?>" id="symbolimg" />
		<?php } ?>
		<?php if($rec_p['symbol']==NULL){ ?>
		<?php }else{ ?><span class="info-delete-symbol"><a class="delete" title="smazat" href="procperson.php?deletesymbol=<?php echo $rec_p['symbol']; ?>&amp;personid=<?php echo $_REQUEST['rid']; ?>&amp;backurl=<?php echo URLEncode('editperson.php?rid='.$_REQUEST['rid']); ?>" onclick="return confirm('Opravdu odebrat symbol?')"><span class="button-text">smazat soubor</span></a></span>
		<?php } ?>
		
		
				
		
		
			<div id="info">
				<h3><label for="name">Jméno:</label></h3>
				<input type="text" name="name" id="name" value="<?php echo StripSlashes($rec_p['name']); ?>" />
				<div class="clear">&nbsp;</div>
				<h3><label for="surname">Příjmení:</label></h3>
				<input type="text" name="surname" id="surname" value="<?php echo StripSlashes($rec_p['surname']); ?>" />
				<div class="clear">&nbsp;</div>
				<h3><label for="side">Strana:</label></h3>
					<select name="side" id="side">
						<option value="0"<?php if ($rec_p['side']==0) { echo ' selected="selected"'; } ?>>neznámá</option>
						<option value="1"<?php if ($rec_p['side']==1) { echo ' selected="selected"'; } ?>>světlo</option>
						<option value="2"<?php if ($rec_p['side']==2) { echo ' selected="selected"'; } ?>>tma</option>
						<option value="3"<?php if ($rec_p['side']==3) { echo ' selected="selected"'; } ?>>člověk</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="power">Síla:</label></h3>
					<select name="power" id="power">
						<option value="0"<?php if ($rec_p['power']==0) { echo ' selected="selected"'; } ?>>neznámá</option>
						<option value="1"<?php if ($rec_p['power']==1) { echo ' selected="selected"'; } ?>>1. kategorie</option>
						<option value="2"<?php if ($rec_p['power']==2) { echo ' selected="selected"'; } ?>>2. kategorie</option>
						<option value="3"<?php if ($rec_p['power']==3) { echo ' selected="selected"'; } ?>>3. kategorie</option>
						<option value="4"<?php if ($rec_p['power']==4) { echo ' selected="selected"'; } ?>>4. kategorie</option>
						<option value="5"<?php if ($rec_p['power']==5) { echo ' selected="selected"'; } ?>>5. kategorie</option>
						<option value="6"<?php if ($rec_p['power']==6) { echo ' selected="selected"'; } ?>>6. kategorie</option>
						<option value="7"<?php if ($rec_p['power']==7) { echo ' selected="selected"'; } ?>>7. kategorie</option>
						<option value="8"<?php if ($rec_p['power']==8) { echo ' selected="selected"'; } ?>>mimo kategorie</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="spec">Specializace:</label></h3>
					<select name="spec" id="spec">
						<option value="0"<?php if ($rec_p['spec']==0) { echo ' selected="selected"'; } ?>>neznámá</option>
						<option value="1"<?php if ($rec_p['spec']==1) { echo ' selected="selected"'; } ?>>bílý mág</option>
						<option value="2"<?php if ($rec_p['spec']==2) { echo ' selected="selected"'; } ?>>černý mág</option>
						<option value="3"<?php if ($rec_p['spec']==3) { echo ' selected="selected"'; } ?>>léčitel</option>
						<option value="4"<?php if ($rec_p['spec']==4) { echo ' selected="selected"'; } ?>>obrateň</option>
						<option value="5"<?php if ($rec_p['spec']==5) { echo ' selected="selected"'; } ?>>upír</option>
						<option value="6"<?php if ($rec_p['spec']==6) { echo ' selected="selected"'; } ?>>vlkodlak</option>
						<option value="7"<?php if ($rec_p['spec']==7) { echo ' selected="selected"'; } ?>>vědma</option>
						<option value="8"<?php if ($rec_p['spec']==8) { echo ' selected="selected"'; } ?>>zaříkávač</option>
					</select>
				<div class="clear">&nbsp;</div>
				<h3><label for="phone">Telefon:</label></h3>
				<input type="text" name="phone" id="phone" value="<?php echo StripSlashes($rec_p['phone']); ?>" />
				<div class="clear">&nbsp;</div>
				<h3><label for="portrait">Nový&nbsp;portrét:</label></h3>
				<input type="file" name="portrait" id="portrait" />
				<div class="clear">&nbsp;</div>
				<h3><label for="symbol">Nový&nbsp;symbol:</label></h3>
				<input type="file" name="symbol" id="symbol" />
				<div class="clear">&nbsp;</div>
				<h3><label for="secret">Přísně&nbsp;tajné:</label></h3>
					<input type="radio" name="secret" value="0" <?php if ($rec_p['secret']==0) { ?>checked="checked"<?php } ?>/>ne<br/>
					<h3><label>&nbsp;</label></h3><input type="radio" name="secret" value="1"<?php if ($rec_p['secret']==1) { ?>checked="checked"<?php } ?>>ano
				<div class="clear">&nbsp;</div>
				<h3><label for="dead">Mrtvá:</label></h3>
					<input type="checkbox" name="dead" value=1 <?php if ($rec_p['dead']==1) { ?>checked="checked"<?php } ?>/><br/>
				<div class="clear">&nbsp;</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '
				<h3><label for="archiv">Archiv:</label></h3>
					<input type="checkbox" name="archiv" value=1';
				if ($rec_p['archiv']==1) { 
					echo ' checked="checked"';
				}
				echo '/><br/>
				<div class="clear">&nbsp;</div>					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
				}
?>				
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- náseduje popis osoby -->
		<fieldset><legend><h2>Popis osoby</h2></legend>
			<div class="field-text">
				<textarea cols="80" rows="30" name="contents" id="contents"><?php echo StripSlashes($rec_p['contents']); ?></textarea>
			</div>
			<!-- end of .field-text -->
		</fieldset>
		<input type="hidden" name="personid" value="<?php echo $rec_p['id']; ?>" />
		<input type="submit" name="editperson" id="submitbutton" value="Uložit" title="Uložit změny"/>
	</form>

</fieldset>

	<div id="change-groups" class="otherform-wrap">
		<fieldset><legend><h2>Přiřazení skupiny</h2></legend>
		<p>Osobě můžete přiřadit skupiny, do kterých patří. Opačnou akci lze provést u skupiny, kde přiřazujete pro změnu osoby dané skupině. Akce jsou si rovnocenné a je tedy nutná pouze jedna z nich.</p>
		<form action="procperson.php" method="post" class="otherform">
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
					((!$rec['secret'])?'<input type="checkbox" name="group[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser'])?' checked="checked"':'').' />
					<label>'.$rec['title'].'</label>':(($rec['iduser'])?'<input type="hidden" name="group[]" value="'.$rec['id'].'" />':'')).'
				</div>';
				}
			}
		?>
			<div>
				<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="submit" value="Uložit změny" name="setgroups" class="submitbutton"  title="Uložit"/>
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #change-groups .otherform-wrap -->

	<!-- následuje seznam přiložených souborů -->
	<fieldset><legend><h3>Přiložené soubory</h3></legend>
		<strong><em>K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 ORDER BY ".DB_PREFIX."data.originalname ASC";
			} else {
			  $sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
			}
			$res=MySQL_Query ($sql);
			$i=0;
			while ($rec_f=MySQL_Fetch_Assoc($res)) { 
				$i++; 
				if($i==1){ ?>
		<ul id="prilozenadata">
				<?php } ?>
			<li class="soubor"><a href="getfile.php?idfile=<?php echo($rec_f['id']); ?>" title=""><?php echo(StripSlashes($rec_f['title'])); ?></a><?php if($rec_f['secret']==1){ ?> (TAJNÝ)<?php }; ?><span class="poznamka-edit-buttons"><?php
				if (($rec_f['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" title="smazat" href="procperson.php?deletefile='.$rec_f['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editperson.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.StripSlashes($rec_f['title']).'&quot; náležící k osobě?\')"><span class="button-text">smazat soubor</span></a>'; ?>
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
		<form action="procperson.php" method="post" enctype="multipart/form-data" class="otherform">
			<div>
				<strong><label for="attachment">Soubor:</label></strong>
				<input type="file" name="attachment" id="attachment" />
			</div>
			<div>
				<strong><label for="usecret">Přísně tajné:</label></strong>
			  	<?php if ($rec_p['secret']!=1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_p['secret']==1){ ?>checked="checked"<?php }; ?>/>ano
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
				<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editperson.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor k osobě" class="submitbutton" title="Uložit"/> 
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-file .otherform-wrap -->
	
	<fieldset><legend><h3>Poznámky</h3></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=1" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		<!-- následuje seznam poznámek -->
		<?php // generování poznámek
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
			} else {
				$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
			}
			$res=MySQL_Query ($sql);
			$i=0;
			while ($rec_n=MySQL_Fetch_Assoc($res)) { 
				$i++;
				if($i==1){ ?>
		<div id="poznamky"><?php
				}
				if($i>1){?>
			<hr /><?php
				} ?>
			<div class="poznamka">
				<h4><?php echo(StripSlashes($rec_n['title'])).' - '.(StripSlashes($rec_n['user']));?><?php
				if ($rec_n['secret']==0) echo ' (veřejná)';
				if ($rec_n['secret']==1) echo ' (tajná)';
				if ($rec_n['secret']==2) echo ' (soukromá)';
				?></h4>
				<div><?php echo(StripSlashes($rec_n['note'])); ?></div>
				<span class="poznamka-edit-buttons"><?php
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=1" title="upravit"><span class="button-text">upravit</span></a> ';
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>'; ?>
				</span>
			</div>
			<!-- end of .poznamka -->
		<?php }
			if($i<>0){ ?>
		</div>
		<!-- end of #poznamky -->
		<?php 
			}else{?><br />
		<em>bez poznámek</em><?php
			}
		// konec poznámek ?>
	</fieldset>

	<div id="new-note" class="otherform-wrap">
		<fieldset><legend><strong>Nová poznámka</strong></legend>
		<form action="procnote.php" method="post" class="otherform">
			<div>
				<strong><label for="notetitle">Nadpis:</label></strong>
				<input type="text" name="title" id="notetitle" />
			</div>
			<div>
			  <strong><label for="nsecret">Utajení:</label></strong>
			  	<?php if ($rec_p['secret']!=1) { ?>&nbsp;<input type="radio" name="secret" id="nsecret" value="0" checked="checked"/>veřejná&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_p['secret']==1){ ?>checked="checked"<?php }; ?>/>tajná&nbsp;/
				&nbsp;<input type="radio" name="secret" value="2" />soukromá
			</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<div>
				<strong><label for="nnotnew">Není nové</label></strong>
					<input type="checkbox" name="nnotnew"/><br/>
				</div>';
				}
?>
			<div>
				<!--  label for="notebody">Tělo poznámka:</label -->
				<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
			</div>
			<div>
				<input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editperson.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="hidden" name="tableid" value="1" />
				<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" title="Uložit"/>
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-note .otherform-wrap -->
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