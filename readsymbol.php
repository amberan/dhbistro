<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);

$latteParameters['title'] = 'Zobrazení symbolu';
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);




	if (is_numeric($_REQUEST['rid'])) {
		$res=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."symbol WHERE id=".$_REQUEST['rid']);
		if ($rec=mysqli_fetch_assoc ($res)) {
                    if (($rec['deleted']==1 || $rec['secret']==1) && !$usrinfo['right_power']) {
                        unauthorizedAccess(1, $rec['secret'], $rec['deleted'], $_REQUEST['rid']);
                    }
			auditTrail(7, 1, $_REQUEST['rid']);
			mainMenu (5);
			if (!isset($_REQUEST['hidenotes'])) {
				$hn=0;
			} else {
				$hn=$_REQUEST['hidenotes'];
			}
			if ($hn==0) {
				$hidenotes='&amp;hidenotes=1">skrýt poznámky</a>';
				$backurl='readsymbol.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
			} else {
				$hidenotes='&amp;hidenotes=0">zobrazit poznámky</a>';
				$backurl='readsymbol.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
			}
			if ($usrinfo['right_power']) {
				$editbutton='; <a href="editsymbol.php?rid='.$_REQUEST['rid'].'">upravit symbol</a>; číslo symbolu: '.$rec['id'].'';
			} else if ($usrinfo['right_text']) {
				$editbutton='; <a href="editsymbol.php?rid='.$_REQUEST['rid'].'">upravit symbol</a>';
			} else {
				$editbutton='';
			}
			deleteUnread (1,$_REQUEST['rid']);
			sparklets ('<a href="./symbols.php">symboly</a> &raquo; <strong>Zobrazit symbol</strong>','<a href="readsymbol.php?rid='.$_REQUEST['rid'].$hidenotes.$editbutton);

			?>
<div id="obsah">
	<h1>Symbol</h1>
	<fieldset><legend><strong>Základní údaje</strong></legend>
		<?php if($rec['symbol']==NULL){ ?><img src="#" alt="symbol chybí" title="symbol chybí" id="symbolimg" class="noname"/>
		<?php }else{ ?><img src="getportrait.php?nrid=<?php echo($rec['id']); ?>" id="symbolimg" />
		<?php } ?>
		<div id="info">
			<?php  
			if ($rec['archiv']==1 || $rec['deleted']==1) echo '<h2>';
			if ($rec['archiv']==1) echo 'ARCHIV';
            if ($rec['deleted']==1) echo 'SMAZANÝ ZÁZNAM';
			if ($rec['archiv']==1 || $rec['deleted']==1) echo '</h2>' ?>
			<h3>Přiřazená osoba: </h3><p>
				<?php
					if($rec['assigned'] == 0) echo 'Nepřiřazený symbol';
					else {
						$res_person=mysqli_query ($database,"
								SELECT id,CONCAT(name,' ',surname) AS title 
								FROM ".DB_PREFIX."person 
								WHERE symbol=".$_REQUEST['rid']);
						$rec_person=mysqli_fetch_assoc ($res_person);						
			 		echo '<a class="redirection" href="readperson.php?rid='.(StripSlashes($rec_person['id'])).'&hidenotes=0">'.(StripSlashes($rec_person['title'])).'</a>';
					} 
				?></p>
			<div class="clear">&nbsp;</div>
			<h3>Čáry: </h3><p><?php echo(StripSlashes($rec['search_lines'])); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Křivky: </h3><p><?php echo(StripSlashes($rec['search_curves'])); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Body: </h3><p><?php echo(StripSlashes($rec['search_points'])); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Geom. tvary: </h3><p><?php echo(StripSlashes($rec['search_geometricals'])); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Písma: </h3><p><?php echo(StripSlashes($rec['search_alphabets'])); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Spec. znaky: </h3><p><?php echo(StripSlashes($rec['search_specialchars'])); ?></p>
			<div class="clear">&nbsp;</div>        
			 
			<p><strong>Datum vytvoření:</strong> <?php echo webdate($rec['created']); ?>
				<strong>Vytvořil:</strong> <?php 
				$name=getAuthor($rec['created_by'],1);
				echo (($rec['created_by']==0)?'asi Krauz':$name); ?> </p>
			<div class="clear">&nbsp;</div>
			<p><strong>Datum poslední změny:</strong> <?php echo webdate($rec['modified']); ?>
				<strong>Změnil:</strong> <?php 
				$name=getAuthor($rec['modified_by'],1);
				echo $name; ?> </p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
	
<!-- náseduje popis osoby -->
	<fieldset>
		<legend><strong>Informace k symbolu</strong></legend>
		<div class="field-text"><?php echo (StripSlashes($rec['desc'])); ?></div>
	</fieldset>	
	
<!-- násedují přiřazené případy a hlášení -->
	<fieldset>
		<legend><strong>Hlášení a případy</strong></legend>
		<h3>Výskyt v případech</h3><!-- následuje seznam případů -->
		<?php // generování seznamu přiřazených případů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3 ORDER BY ".DB_PREFIX."case.title ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."case.secret=0 ORDER BY ".DB_PREFIX."case.title ASC";
			}
			$pers=mysqli_query ($database,$sql);
			
			$i=0;
			while ($perc=mysqli_fetch_assoc ($pers)) { 
				$i++;
				if($i==1){ ?>
		<ul id=""><?php
				}
				 ?>
			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		<?php }
			if($i<>0){ ?>
		</ul>
		<!-- end of # -->
		<?php 
			}else{?><br />
		<em>Symbol nebyl přiřazen žádnému případu.</em><?php
			}
		// konec seznamu přiřazených případů ?>
		<h3>Výskyt v hlášení</h3>
		<!-- následuje seznam hlášení -->
		<?php // generování seznamu přiřazených hlášení
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."report WHERE ".DB_PREFIX."report.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4 ORDER BY ".DB_PREFIX."report.label ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."report WHERE ".DB_PREFIX."report.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4 AND ".DB_PREFIX."report.secret=0 ORDER BY ".DB_PREFIX."report.label ASC";
			}
			$pers=mysqli_query ($database,$sql);
			
			$i=0;
			while ($perc=mysqli_fetch_assoc ($pers)) { 
				$i++;
				if($i==1){ ?>
		<ul id=""><?php
				}
				 ?>
			<li><a href="readactrep.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a></li>
		<?php }
			if($i<>0){ ?>
		</ul>
		<!-- end of # -->
		<?php 
			}else{?><br />
		<em>Symbol nebyl přiřazen žádnému hlášení.</em><?php
			}
		// konec seznamu přiřazených hlášení ?>
		<div class="clear">&nbsp;</div>
	</fieldset>
	
	<fieldset><legend><strong>Poznámky</strong></legend>
	<!-- následuje seznam poznámek -->
		<?php // generování poznámek
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=7 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
			} else {
				$sql="SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=7 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
			}
			$res=mysqli_query ($database,$sql);
			$i=0;
			while ($rec_n=mysqli_fetch_assoc ($res)) { 
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
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=7" title="upravit"><span class="button-text">upravit</span></a> ';
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k symbolu?');".'" title="smazat"><span class="button-text">smazat</span></a>'; ?>
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
</div>
<!-- end of #obsah -->
<?php
		} else {
			$_SESSION['message'] = "Symbol neexistuje!";
			Header ('location: index.php');
		}
	} else {
		$_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
		Header ('location: index.php');
	}
        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
?>
