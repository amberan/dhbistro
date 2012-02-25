<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$sql="SELECT
			".DB_PREFIX."reports.datum AS 'datum',
			".DB_PREFIX."reports.label AS 'label',
			".DB_PREFIX."reports.task AS 'task',
			".DB_PREFIX."reports.summary AS 'summary',
			".DB_PREFIX."reports.impacts AS 'impacts',
			".DB_PREFIX."reports.details AS 'details',
			".DB_PREFIX."users.login AS 'autor',
			".DB_PREFIX."reports.type AS 'type'
			FROM ".DB_PREFIX."reports, ".DB_PREFIX."users
			WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.id=".$_REQUEST['rid'];
		$res=MySQL_Query ($sql);
		if ($rec_ar=MySQL_Fetch_Assoc($res)) {
				$typestring=(($rec_ar['type']==1)?'výjezd':(($rec_ar['type']==2)?'výslech':'?')); //odvozuje slovní typ hlášení
			// následuje hlavička
			pageStart (StripSlashes('Hlášení'.(($rec_ar['type']==1)?' z výjezdu':(($rec_ar['type']==2)?' z výslechu':'')).': '.$rec_ar['label']));
			mainMenu (4);
			sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>'.StripSlashes($rec_ar['label']).' ('.$typestring.')</strong>');
?>
<div id="obsah">
<<<<<<< .mine
	<h1><?php echo(StripSlashes($rec_ar['label'])); ?></h1>
	<div id="report-hlavicka" class="top">
		<span>[ <strong>Hlášení<?php echo((($rec_ar['type']==1)?' z výjezdu':(($rec_ar['type']==2)?' z výslechu':' k hlášení')));?></strong> | </span>
		<span><strong>Vyhotovil: </strong><?php echo(StripSlashes($rec_ar['autor'])); ?> | </span>
		<span><strong>Dne: </strong><?php echo(Date ('d. m. Y',$rec_ar['datum'])); ?> ]</span>
	</div>
	<fieldset><legend><h2>Obecné informace</h2></legend>
	<div id="report-info">
		<h3><?php echo((($rec_ar['type']==1)?'Úkol':(($rec_ar['type']==2)?'Předmět výslechu':'Úkol'))); ?>:</h3>
		<p><?php echo(StripSlashes($rec_ar['task'])); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Osoby přítomné:</h3>
		<p><?php 			
=======
	<h1><?php echo(StripSlashes($rec['label'])); ?></h1>
	<strong><?php echo((($rec['type']==1)?'Úkol':(($rec['type']==2)?'Předmět výslechu':'Úkol'))); ?>: </strong><?php echo(StripSlashes($rec['task'])); ?><br />
	<strong>Vyhotovil: </strong><?php echo(StripSlashes($rec['autor'])); ?><br />
	<strong>Datum vyhotovnení: </strong><?php echo(Date ('d. m. Y - H:i:s',$rec['datum'])); ?>
	<h2>Shrnutí</h2><?php echo(StripSlashes($rec['summary'])); ?>
	<h2>Možné dopady</h2><?php echo(StripSlashes($rec['impacts'])); ?>
	<h2>Podrobný průběh</h2><?php echo(StripSlashes($rec['details'])); ?>
	<hr />
	<p>Osoby přítomné akci:
	<?php 			
>>>>>>> .r50
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."persons, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.deleted=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		} else {
			$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."persons, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		}
		$res=MySQL_Query ($sql);
		if (MySQL_Num_Rows($res)) {
			$groups=Array();
			while ($rec_p=MySQL_Fetch_Assoc($res)) {
				$groups[]='<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
			}
			echo implode ($groups,', ');
		} else {
			echo '&mdash;';
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
	<!-- end of #report-info -->
	</fieldset>
	
	<fieldset>
		<legend><h2>Shrnutí</h2></legend>
		<div class="report-field-text"><?php echo(StripSlashes($rec_ar['summary'])); ?></div>
	</fieldset>
	<fieldset>
		<legend><h2>Možné dopady</h2></legend>
		<div class="report-field-text"><?php echo(StripSlashes($rec_ar['impacts'])); ?></div>
	</fieldset>
	<fieldset>
		<legend><h2>Podrobný průběh</h2></legend>
		<div class="report-field-text"><?php echo(StripSlashes($rec_ar['details'])); ?></div>
	</fieldset>

<!-- následuje seznam původních jmen -->
	<?php //generování původních jmen
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 ORDER BY ".DB_PREFIX."data.originalname ASC";
		} else {
		  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=3 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
		}
		$res=MySQL_Query ($sql);
		$i=0;
		while ($rec=MySQL_Fetch_Assoc($res)) { 
			$i++; 
			if($i==1){ ?>
	<hr />
	<ul id="puvodnijmena">
			<?php } ?>
		<li><a href="getfile.php?idfile=<?php echo($rec['id']); ?>" title=""><?php echo(StripSlashes($rec['title'])); ?></a></li>
	<?php 
		}
		if($i<>0){ ?>
	</ul>
	<!-- end of #puvodnijmena -->
	<?php 
		}
	// konec původních jmen ?>
<!-- následuje seznam poznámek -->
	<?php // generování poznámek
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=3 ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=3 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		$res=MySQL_Query ($sql);
		$i=0;
		while ($rec=MySQL_Fetch_Assoc($res)) { 
			$i++;
			if($i==1){ ?>
	<hr />
	<ul id="poznamky">
		<?php
			} ?>
		<li><a href="readnote.php?rid=<?php echo($rec['id']); ?>" title=""><?php echo(StripSlashes($rec['title'])); ?></a><?php echo(((($rec['iduser']==$usrinfo['id']) || $usrinfo['right_power'])?' &mdash; <a href="proccase.php?deletenote='.$rec['id'].'&amp;caseid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readcase.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k případu?');".'">smazat poznámku</a></li>':'')); ?></li>
	<?php 
		}
		if($i<>0){ ?>
	</ul>
	<!-- end of #poznamky -->
	<?php 
		}
	// konec poznámek ?>
</div>
<!-- end of #obsah -->
<?php
		} else {
		    pageStart ('Hlášení neexistuje');
			mainMenu (4);
			sparklets ('<a href="./reports.php">případy</a> &raquo; <strong>případ neexistuje</strong>');
		    echo '<div id="obsah"><p>Případ neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>