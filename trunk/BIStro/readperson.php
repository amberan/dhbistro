<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."persons WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
			$sides=Array('','světlý','temný','člověk','neznámá');
			$powers=Array('','neznámá','člověk','mimo kategorie','1. kategorie','2. kategorie','3. kategorie','4. kategorie');
			pageStart (StripSlashes($rec['surname']).', '.StripSlashes($rec['name']));
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>'.StripSlashes($rec['surname']).', '.StripSlashes($rec['name']).'</strong>');
			?>			
<div id="obsah">
	<h1><?php echo(StripSlashes($rec['surname']).', '.StripSlashes($rec['name'])); ?></h1>
	<fieldset><legend><h2>Základní údaje</h2></legend>
		<img src="getportrait.php?rid=<?php echo($_REQUEST['rid']); ?>" alt="portrét chybí" id="portraitimg" />
		<div id="info">
			<h3>Jméno: </h3><p><?php echo(StripSlashes($rec['name'])); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Příjmení: </h3><p><?php echo(StripSlashes($rec['surname'])); ?></p>
			<div class="clear">&nbsp;</div> 
			<h3>Strana: </h3><p><?php 
				switch ($rec['side']) {
					case 1: $side = 'světlo'; break;
					case 2: $side = 'tma'; break;
					case 3: $side = 'člověk'; break;
					default: $side = 'neznámá'; break;
				}
				echo $side; ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Síla: </h3><p><?php 
				switch ($rec['power']) {
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
						$power = $rec['power'].'. kategorie'; break;
					case 8:
						$power = 'mimo kategorie'; break; 
					default: $power = 'neznámá'; break;
				}
				echo $power; ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Specializace: </h3><p><?php 
				switch ($rec['spec']) {
					case 1: $side = 'bílý mág'; break;
					case 2: $side = 'černý mág'; break;
					case 3: $side = 'léčitel'; break;
					case 4: $side = 'obrateň'; break;
					case 5: $side = 'upír'; break;
					case 6: $side = 'vlkodlak'; break;
					case 7: $side = 'vědma'; break;
					case 8: $side = 'zaříkávač'; break;
					default: $side = 'neznámá'; break;
				}
				echo $side; ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Přísně tajné: </h3><p><?php echo (($rec['secret'])?'ano':'ne'); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Patří do skupin: </h3><p><?php
				if ($usrinfo['right_power']) {
					$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."groups, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."groups.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."groups.deleted=0 ORDER BY ".DB_PREFIX."groups.title ASC";
				} else {
					$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."groups, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."groups.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."groups.deleted=0 AND ".DB_PREFIX."groups.secret=0 ORDER BY ".DB_PREFIX."groups.title ASC";
				}
				$res_g=MySQL_Query ($sql);
				if (MySQL_Num_Rows($res_g)) {
					$groups=Array();
					while ($rec_g=MySQL_Fetch_Assoc($res_g)) {
						$groups[]='<a href="./readgroup.php?rid='.$rec_g['id'].'">'.StripSlashes ($rec_g['title']).'</a>';
					}
					echo implode ($groups,', ');
				} else {
					echo '&mdash;';
				} ?></p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
<!-- náseduje popis osoby -->
	<fieldset>
		<legend><h2>Popis osoby</h2></legend>
		<div class="field-text"><?php echo (StripSlashes($rec['contents'])); ?></div>
	</fieldset>
	
<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 ORDER BY ".DB_PREFIX."data.originalname ASC";
		} else {
		  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
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
			if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=1" title="upravit">upravit</a> ';
			if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procperson.php?deletenote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat">smazat</a>'; ?>
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
<!-- 
	echo '<h3>Poznámky:</h3>';
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	} else {
	  $sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<h4><a href="readnote.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a>';
		if ($rec['secret']==0) echo ' (veřejná)';
		if ($rec['secret']==1) echo ' (tajná)';
		if ($rec['secret']==2) echo ' (soukromá)';
		echo '</h4>';
		echo '<div id="obsah"><p>'.StripSlashes($rec['note']).'</p></div>';
		if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a href="editnote.php?rid='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=1">upravit poznámku</a> ';
		if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a href="procperson.php?deletenote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'">smazat poznámku</a>';
	}
?> -->
</div>
<!-- end of #obsah -->

<?php
		} else {
			pageStart ('Osoba neexistuje');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>osoba neexistuje</strong>');
		  echo '<div id="obsah"><p>Osoba neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>