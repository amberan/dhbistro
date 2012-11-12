<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."persons WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
			$sides=Array('','světlý','temný','člověk','neznámá');
			$powers=Array('','neznámá','člověk','mimo kategorie','1. kategorie','2. kategorie','3. kategorie','4. kategorie');
			pageStart (StripSlashes($rec['surname']).', '.StripSlashes($rec['name']));
			mainMenu (5);
			if (!isset($_REQUEST['hidenotes'])) {
				$hn=0;
			} else {
				$hn=$_REQUEST['hidenotes'];
			}
			if ($hn==0) {
				$hidenotes='&amp;hidenotes=1">skrýt poznámky</a>';
				$backurl='readperson.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
			} else {
				$hidenotes='&amp;hidenotes=0">zobrazit poznámky</a>';
				$backurl='readperson.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
			}
			if ($usrinfo['right_org']) {
				$editbutton='; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>; číslo osoby: '.$rec['id'].'; <a href="orgperson.php?rid='.$_REQUEST['rid'].'">organizačně upravit osobu</a>;';
			} else if ($usrinfo['right_power']) {
				$editbutton='; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>; číslo osoby: '.$rec['id'].'';
			} else if ($usrinfo['right_text']) {
				$editbutton='; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>';
			} else {
				$editbutton='';
			}
			deleteUnread (1,$_REQUEST['rid']);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>'.StripSlashes($rec['surname']).', '.StripSlashes($rec['name']).'</strong>','<a href="readperson.php?rid='.$_REQUEST['rid'].$hidenotes.$editbutton);
			?>			
<div id="obsah">
	<h1><?php echo(StripSlashes($rec['surname']).', '.StripSlashes($rec['name'])); ?></h1>
	<fieldset><legend><h2>Základní údaje</h2></legend>
		<?php if($rec['portrait']==NULL){ ?><img src="#" alt="portrét chybí" tile="portrét chybí" id="portraitimg" class="noname"/>
		<?php }else{ ?><img src="getportrait.php?rid=<?php echo($_REQUEST['rid']); ?>" alt="<?php echo(StripSlashes($rec['name']).' '.StripSlashes($rec['surname'])); ?>" id="portraitimg" />
		<?php } ?>
		<?php if($rec['symbol']==NULL){ ?><img src="#" alt="symbol chybí" tile="symbol chybí" id="symbolimg" class="noname"/>
		<?php }else{ ?><img src="getportrait.php?srid=<?php echo($_REQUEST['rid']); ?>" alt="<?php echo(StripSlashes($rec['name']).' '.StripSlashes($rec['surname'])); ?>" id="symbolimg" />
		<?php } ?>
		<div id="info">
			<?php 
			if ($rec['secret']==1 || $rec['dead']==1 || $rec['archiv']==1) echo '<h2>';
			if ($rec['secret']==1) echo 'TAJNÉ ';
			if ($rec['dead']==1) echo 'MRTVOLA ';
			if ($rec['archiv']==1) echo 'ARCHIV';
			if ($rec['secret']==1 || $rec['dead']==1 || $rec['archiv']==1) echo '</h2>' ?>
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
			<h3>Telefon: </h3><p><?php echo(StripSlashes($rec['phone'])); ?></p>
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
			<p><strong>Datum vytvoření:</strong> <?php echo (($rec['regdate']==0)?'asi dávno':(Date ('d. m. Y',$rec['regdate']))); ?>
				<strong>Vytvořil:</strong> <?php 
				$name=getAuthor($rec['regid'],1);
				echo (($rec['regid']==0)?'asi Krauz':$name); ?> </p>
			<div class="clear">&nbsp;</div>
			<p><strong>Datum poslední změny:</strong> <?php echo(Date ('d. m. Y',$rec['datum'])); ?>
				<strong>Změnil:</strong> <?php 
				$name=getAuthor($rec['iduser'],1);
				echo $name; ?> </p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
<!-- náseduje popis osoby -->
	<fieldset>
		<legend><h2>Popis osoby</h2></legend>
		<div class="field-text"><?php echo (StripSlashes($rec['contents'])); ?></div>
	</fieldset>
	
<!-- násedují přiřazené případy a hlášení -->
	<fieldset>
		<legend><h2>Hlášení a případy</h2></legend>
		<h3>Figuruje v těchto hlášení: </h3><p><?php
				if ($usrinfo['right_power']) {
					$sql_r="SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."reports, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idreport=".DB_PREFIX."reports.id AND ".DB_PREFIX."ar2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."reports.deleted=0 ORDER BY ".DB_PREFIX."reports.label ASC";
				} else {
					$sql_r="SELECT ".DB_PREFIX."reports.secret AS 'secret', ".DB_PREFIX."reports.label AS 'label', ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."reports, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idreport=".DB_PREFIX."reports.id AND ".DB_PREFIX."ar2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."reports.deleted=0 AND ".DB_PREFIX."reports.secret=0 ORDER BY ".DB_PREFIX."reports.label ASC";
				}
				$res_r=MySQL_Query ($sql_r);
				if (MySQL_Num_Rows($res_r)) {
					$reports=Array();
					while ($rec_r=MySQL_Fetch_Assoc($res_r)) {
						$reports[]='<a href="./readactrep.php?rid='.$rec_r['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($rec_r['label']).'</a>';
					}
					echo implode ($reports,'<br />');
				} else {
					echo 'Osoba nefiguruje v žádném hlášení.';
				} ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Figuruje v těchto případech: </h3><p><?php
				if ($usrinfo['right_power']) {
					$sql_c="SELECT ".DB_PREFIX."cases.secret AS 'secret', ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."cases, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idcase=".DB_PREFIX."cases.id AND ".DB_PREFIX."c2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."cases.deleted=0 ORDER BY ".DB_PREFIX."cases.title ASC";
				} else {
					$sql_c="SELECT ".DB_PREFIX."cases.secret AS 'secret', ".DB_PREFIX."cases.title AS 'title', ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."cases, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idcase=".DB_PREFIX."cases.id AND ".DB_PREFIX."c2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."cases.deleted=0 AND ".DB_PREFIX."cases.secret=0 ORDER BY ".DB_PREFIX."cases.title ASC";
				}
				$res_c=MySQL_Query ($sql_c);
				if (MySQL_Num_Rows($res_c)) {
					$cases=Array();
					while ($rec_c=MySQL_Fetch_Assoc($res_c)) {
						$cases[]='<a href="./readcase.php?rid='.$rec_c['id'].'">'.StripSlashes ($rec_c['title']).'</a>';
					}
					echo implode ($cases,'<br />');
				} else {
					echo 'Osoba nefiguruje v žádném případu.';
				} ?></p>
		<div class="clear">&nbsp;</div>
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

<?php //skryti poznamek 
if ($hn==1) goto hidenotes; ?>
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
<?php hidenotes: ?>	

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