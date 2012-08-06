<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$check=MySQL_Fetch_Assoc(MySQL_Query("SELECT ".DB_PREFIX."users.idperson AS 'aid'
											FROM ".DB_PREFIX."users, ".DB_PREFIX."reports
											WHERE ".DB_PREFIX."reports.id=".$_REQUEST['rid']."
											AND ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id"));
		if ($check['aid']==0) {
			$connector='';
			$notconnected=1;
		} else {
			$connector=' AND '.DB_PREFIX.'users.idperson='.DB_PREFIX.'persons.id';
			$notconnected=0;
		}
		$sql="SELECT
			".DB_PREFIX."reports.datum AS 'datum',
			".DB_PREFIX."reports.label AS 'label',
			".DB_PREFIX."reports.task AS 'task',
			".DB_PREFIX."reports.summary AS 'summary',
			".DB_PREFIX."reports.impacts AS 'impacts',
			".DB_PREFIX."reports.details AS 'details',
			".DB_PREFIX."users.login AS 'autor',
			".DB_PREFIX."reports.type AS 'type',
			".DB_PREFIX."reports.adatum AS 'adatum',
			".DB_PREFIX."reports.start AS 'start',
			".DB_PREFIX."reports.end AS 'end',
			".DB_PREFIX."reports.energy AS 'energy',
			".DB_PREFIX."reports.inputs AS 'inputs',
			".DB_PREFIX."reports.secret AS 'secret',
			".DB_PREFIX."persons.name AS 'name',
			".DB_PREFIX."persons.surname AS 'surname'
			FROM ".DB_PREFIX."reports, ".DB_PREFIX."users, ".DB_PREFIX."persons
			WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id 
			AND ".DB_PREFIX."reports.id=".$_REQUEST['rid'].$connector;
		$res=MySQL_Query ($sql);
		if ($rec_ar=MySQL_Fetch_Assoc($res)) {
				$typestring=(($rec_ar['type']==1)?'výjezd':(($rec_ar['type']==2)?'výslech':'?')); //odvozuje slovní typ hlášení
			// následuje hlavička
			pageStart (StripSlashes('Hlášení'.(($rec_ar['type']==1)?' z výjezdu':(($rec_ar['type']==2)?' z výslechu':'')).': '.$rec_ar['label']));
			mainMenu (4);
			if (!isset($_REQUEST['hidenotes'])) {
				$hn=0;
			} else {
				$hn=$_REQUEST['hidenotes'];
			}
			if (($usrinfo['right_power']) && ($hn==0) && ($_REQUEST['truenames']==0)) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">skrýt poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=1">zobrazit celá jména</a>';
				$author=$rec_ar['autor'];
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=0';
			} else if (($usrinfo['right_power']) && ($hn==1) && ($_REQUEST['truenames']==0)) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=1">zobrazit celá jména</a>';
				$author=$rec_ar['autor'];
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=0';
			} else if (($usrinfo['right_power']) && ($notconnected==0) && ($hn==1) && ($_REQUEST['truenames']==1)) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=1">zobrazit poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">zobrazit volací znaky</a>';
				$author=$rec_ar['surname'].' '.$rec_ar['name'];
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=1';
			} else if (($usrinfo['right_power']) && ($notconnected==0) && ($hn==0) && ($_REQUEST['truenames']==1)) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=1">skrýt poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit volací znaky</a>';
				$author=$rec_ar['surname'].' '.$rec_ar['name'];
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=1';
			} else if (($usrinfo['right_power']) && ($notconnected==1) && ($hn==1) && ($_REQUEST['truenames']==1)) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=1">zobrazit poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">zobrazit volací znaky</a>';
				$author='NENÍ NAPOJEN';
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=1';
			} else if (($usrinfo['right_power']) && ($notconnected==1) && ($hn==0) && ($_REQUEST['truenames']==1)) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=1">skrýt poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit volací znaky</a>';
				$author='NENÍ NAPOJEN';
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=1';
			} else if ($hn==0) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">skrýt poznámky</a>';
				$author=$rec_ar['autor'];
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=0';
			} else if ($hn==1) {
				$spaction='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit poznámky</a>';
				$author=$rec_ar['autor'];
				$backurl='readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=0';
			}
			if ($usrinfo['right_text']) {
				$editbutton='; <a href="editactrep.php?rid='.$_REQUEST['rid'].'">upravit hlášení</a>';
			} else {
				$editbutton='';
			}
			sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>'.StripSlashes($rec_ar['label']).' ('.$typestring.')</strong>',$spaction.$editbutton);
?>
<div id="obsah">
	<h1><?php echo(StripSlashes($rec_ar['label'])); ?></h1>
	<div id="hlavicka" class="top">
		<span>[ <strong>Hlášení<?php echo((($rec_ar['type']==1)?' z výjezdu':(($rec_ar['type']==2)?' z výslechu':' k akci')));?></strong> | </span>
		<span><strong>Vyhotovil: </strong><?php echo(StripSlashes($author)); ?> | </span>
		<span><strong>Dne: </strong><?php echo(Date ('d. m. Y',$rec_ar['datum'])); ?> ]</span>
	</div>
	<fieldset><legend><h2>Obecné informace</h2></legend>
	<div id="info">
		<?php if ($rec_ar['secret']==1) echo '<h2>TAJNÉ</h2>'?>
		<h3>Datum<?php echo((($rec_ar['type']==1)?' výjezdu':(($rec_ar['type']==2)?' výslechu':' akce'))); ?>:</h3>
		<p><?php echo(Date ('d.m.Y',$rec_ar['adatum'])); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Začátek<?php echo((($rec_ar['type']==1)?' výjezdu':(($rec_ar['type']==2)?' výslechu':' akce'))); ?>:</h3>
		<p><?php echo(StripSlashes($rec_ar['start'])); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Konec<?php echo((($rec_ar['type']==1)?' výjezdu':(($rec_ar['type']==2)?' výslechu':' akce'))); ?>:</h3>
		<p><?php echo(StripSlashes($rec_ar['end'])); ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo((($rec_ar['type']==1)?'Úkol':(($rec_ar['type']==2)?'Předmět výslechu':'Úkol'))); ?>:</h3>
		<p><?php echo(StripSlashes($rec_ar['task'])); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Velitel<?php //echo((($rec_ar['type']==1)?' výjezdu':(($rec_ar['type']==2)?' výslechu':' akce'))); ?>: </h3>
		<p><?php 			
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."persons, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=4 AND ".DB_PREFIX."persons.deleted=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		} else {
			$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."persons, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=4 AND ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		}
		$res=MySQL_Query ($sql);
		if (MySQL_Num_Rows($res)) {
			$groups=Array();
			while ($rec_p=MySQL_Fetch_Assoc($res)) {
				$groups[]='<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
			}
			echo implode ($groups,', ');
		} else { ?>
			<em>Velitel není označen.</em><?php
		} ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Osoby přítomné: </h3>
		<p><?php 			
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."persons, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=0 AND ".DB_PREFIX."persons.deleted=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		} else {
			$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."persons, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=0 AND ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		}
		$res=MySQL_Query ($sql);
		if (MySQL_Num_Rows($res)) {
			$groups=Array();
			while ($rec_p=MySQL_Fetch_Assoc($res)) {
				$groups[]='<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
			}
			echo implode ($groups,', ');
		} else { ?>
			<em>K hlášní nejsou připojeny žádné osoby.</em><?php
		} ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Přiřazené případy:</h3>
		<?php
			$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."cases.title ASC";
			$pers=MySQL_Query ($sql);
			$i=0;
			while ($perc=MySQL_Fetch_Assoc($pers)){
				$i++;
				if($i==1){?>
		<ul id="pripady"><?php 
				} ?>
			<li><a href="readcase.php?rid=<?php echo($perc['id']); ?>" title=""><?php echo($perc['title']); ?></a></li>
		<?php
			}
			if($i<>0){ ?>
		</ul><?php 
			}else { ?>
		<p><em>Hlášení není přiřazeno k žádnému případu.</em></p><?php 
			} ?>
		<!-- end of #pripady -->
		<div class="clear">&nbsp;</div>				
	</div>
	<!-- end of #info -->
	</fieldset>
	
	<fieldset>
		<legend><h2>Shrnutí</h2></legend>
		<div class="field-text"><?php echo(StripSlashes($rec_ar['summary'])); ?></div>
	</fieldset>
	<fieldset>
		<legend><h2>Možné dopady</h2></legend>
		<div class="field-text"><?php echo(StripSlashes($rec_ar['impacts'])); ?></div>
	</fieldset>
	<fieldset>
		<legend><h2>Podrobný průběh</h2></legend>
		<div class="field-text"><?php echo(StripSlashes($rec_ar['details'])); ?></div>
	</fieldset>
	<fieldset>
		<legend><h2>Energetická náročnost</h2></legend>
		<div class="field-text"><?php echo(StripSlashes($rec_ar['energy'])); ?></div>
	</fieldset>
	<fieldset>
		<legend><h2>Počáteční vstupy</h2></legend>
		<div class="field-text"><?php echo(StripSlashes($rec_ar['inputs'])); ?></div>
	</fieldset>

<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=4 ORDER BY ".DB_PREFIX."data.originalname ASC";
		} else {
		  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=4 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
		}
		$res=MySQL_Query ($sql);
		$i=0;
		while ($rec=MySQL_Fetch_Assoc($res)) { 
			$i++; 
			if($i==1){ ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
			<?php } ?>
		<li><a href="getfile.php?idfile=<?php echo($rec['id']); ?>" title=""><?php echo(StripSlashes($rec['title'])); ?></a></li>
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
if ($hn==1) goto hidenotes; ?>
<!-- následuje seznam poznámek -->
	<?php // generování poznámek
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=4 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=4 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
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
			if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=4" title="upravit"><span class="button-text">upravit</span></a> ';
			if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';?>
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
		    pageStart ('Hlášení neexistuje');
			mainMenu (4);
			sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>hlášení neexistuje</strong>');
		    echo '<div id="obsah"><p>Hlášení neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>