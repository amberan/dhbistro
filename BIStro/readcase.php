<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."cases WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
			pageStart (StripSlashes($rec['title']));
			mainMenu (4);
			if ($_REQUEST['hidenotes']==0) {
				$hidenotes='&amp;hidenotes=1">skrýt poznámky</a>';
				$backurl='readcase.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
			} else {
				$hidenotes='&amp;hidenotes=0">zobrazit poznámky</a>';
				$backurl='readcase.php?rid='.$_REQUEST['rid'].'&hidenotes=1';
			}
			sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>'.StripSlashes($rec['title']).'</strong>','<a href="readcase.php?rid='.$_REQUEST['rid'].$hidenotes);
?>
<div id="obsah">
	<h1><?php echo StripSlashes($rec['title']); ?></h1>
	<fieldset><legend><h2>Obecné informace</h2></legend>
		<div id="info">
			<?php if ($rec['secret']==1) echo '<h2>TAJNÉ</h2>'?>
			<h3>Osoby spojené s případem: </h3><p><?php
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."persons, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.deleted=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."persons, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
			}
			$res=MySQL_Query ($sql);
			if (MySQL_Num_Rows($res)) {
				$groups=Array();
				while ($rec_p=MySQL_Fetch_Assoc($res)) {
					$groups[]='<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
				}
				echo implode ($groups,', ');
			} else {?>
				<em>K případu nejsou připojeny žádné osoby.</em><?php
			} ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Hlášení přiřazená k případu:</h3>
				<?php
				$sql="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.task AS 'task', ".DB_PREFIX."reports.type AS 'type', ".DB_PREFIX."reports.adatum AS 'adatum', ".DB_PREFIX."users.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."reports, ".DB_PREFIX."users WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."users.id=".DB_PREFIX."reports.iduser ORDER BY ".DB_PREFIX."reports.label ASC";
				$pers=MySQL_Query ($sql);
				$i=0;
				while ($perc=MySQL_Fetch_Assoc($pers)) {
					$i++;
					if($i==1){?>
				<ul id="pripady"><?php 
						} ?>
					<li><a href="readactrep.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a> <span class="top">[ <strong><?php echo((($perc['type']==1)?'Výjezd':(($perc['type']==2)?'Výslech':'Hlášení')));?></strong> | <strong>Ze dne:</strong> <?php echo(Date ('d.m.Y',$perc['adatum'])); ?> | <strong>Vyhotovil:</strong> <?php echo $perc['user']; ?> ]</span> - <?php echo $perc['task']; ?></li><?php 
				}
					if($i<>0){ ?>
				</ul>
				<!-- end of #pripady --><?php 
					}else { ?>
				<p><em>K případu není přiřazeno žádné hlášení.</em></p><?php 
					} ?>
				<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
	
	<fieldset><legend><h2>Popis</h2></legend>
		<div class="field-text"><?php echo StripSlashes($rec['contents']); ?></div>
	</fieldset>
	
<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 ORDER BY ".DB_PREFIX."data.originalname ASC";
		} else {
		  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
		}
		$res_f=MySQL_Query ($sql);
		$i=0;
		while ($rec_f=MySQL_Fetch_Assoc($res_f)) { 
			$i++; 
			if($i==1){ ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
			<?php } ?>
		<li><a href="getfile.php?idfile=<?php echo($rec_f['id']); ?>" title=""><?php echo(StripSlashes($rec_f['title'])); ?></a></li>
	<?php 
		}
		if($i<>0){ ?>
	</ul>
	<!-- end of #prilozenadata -->
	</fieldset>
	<?php 
		}
	// konec seznamu přiložených souborů ?>
<?php //skryti poznamek 
if ($_REQUEST['hidenotes']==1) goto hidenotes; ?>
<!-- následuje seznam poznámek -->
	<?php // generování poznámek
		if ($usrinfo['right_power']) {
			$sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=3 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=3 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		$res_n=MySQL_Query ($sql_n);
		$i=0;
		while ($rec_n=MySQL_Fetch_Assoc($res_n)) { 
			$i++;
			if($i==1){ ?>
	<fieldset><legend><strong>Poznámky</strong></legend>
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
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=3" title="upravit"><span class="button-text">upravit</span></a> ';
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';?>
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
<?php hidenotes: ?>	
</div>
<!-- end of #obsah -->
<?php
		} else {
		  pageStart ('Případ neexistuje');
			mainMenu (4);
			sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>případ neexistuje</strong>');
		  echo '<div id="obsah"><p>Případ neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>