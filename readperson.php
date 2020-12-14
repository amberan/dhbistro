<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

	if (is_numeric($_REQUEST['rid'])) {
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."person WHERE id=".$_REQUEST['rid']);
	    if ($rec = mysqli_fetch_assoc ($res)) {
	        if (($rec['secret'] > $user['aclDirector']) || $rec['deleted'] == 1) {
	            unauthorizedAccess(1, $rec['secret'], $rec['deleted'], $_REQUEST['rid']);
	        }
	        auditTrail(1, 1, $_REQUEST['rid']);
	        $sides = Array('', 'světlý', 'temný', 'člověk', 'neznámá');
	        $powers = Array('', 'neznámá', 'člověk', 'mimo kategorie', '1. kategorie', '2. kategorie', '3. kategorie', '4. kategorie');

			
	        $latteParameters['title'] = StripSlashes($rec['surname']).', '.StripSlashes($rec['name']);
	        mainMenu ();
	        if (!isset($_REQUEST['hidenotes'])) {
	            $hn = 0;
	        } else {
	            $hn = $_REQUEST['hidenotes'];
	        }
	        if ($hn == 0) {
	            $hidenotes = '&amp;hidenotes=1">skrýt poznámky</a>';
	            $backurl = 'readperson.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
	        } else {
	            $hidenotes = '&amp;hidenotes=0">zobrazit poznámky</a>';
	            $backurl = 'readperson.php?rid='.$_REQUEST['rid'].'&hidenotes=0';
	        }
	        if ($user['aclGamemaster']) {
	            $editbutton = '; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>; číslo osoby: '.$rec['id'].'; <a href="orgperson.php?rid='.$_REQUEST['rid'].'">organizačně upravit osobu</a>;';
	        } else {
	            if ($user['aclDirector'] > 0) {
	                $editbutton = '; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>; číslo osoby: '.$rec['id'].'';
	            } else {
	                if ($usrinfo['right_text']) {
	                    $editbutton = '; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>';
	                } else {
	                    $editbutton = '';
	                }
	            }
	        }
	        deleteUnread (1,$_REQUEST['rid']);
	        sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>'.StripSlashes($rec['surname']).', '.StripSlashes($rec['name']).'</strong>','<a href="readperson.php?rid='.$_REQUEST['rid'].$hidenotes.$editbutton); ?>			
<div id="obsah">
	<h1><?php echo StripSlashes($rec['surname']).', '.StripSlashes($rec['name']); ?></h1>
	<fieldset><legend><strong>Základní údaje</strong></legend>
		<?php if ($rec['portrait'] == NULL) { ?><img src="#" alt="portrét chybí" title="portrét chybí" id="portraitimg" class="noname"/>
		<?php } else { ?><img src="getportrait.php?rid=<?php echo $_REQUEST['rid']; ?>" alt="<?php echo StripSlashes($rec['name']).' '.StripSlashes($rec['surname']); ?>" id="portraitimg" />
		<?php } ?>
		<?php if ($rec['symbol'] == NULL) { ?><img src="#" alt="symbol chybí" title="symbol chybí" id="symbolimg" class="noname"/>
		<?php } else { ?><a href="readsymbol.php?rid=<?php echo $rec['symbol']; ?>"><img src="getportrait.php?nrid=<?php echo $rec['symbol']; ?>" alt="<?php echo StripSlashes($rec['name']).' '.StripSlashes($rec['surname']); ?>" id="symbolimg" /></a>
		<?php } ?>
		<div id="info">
			<?php
			if ($rec['secret'] > 0 || $rec['dead'] == 1 || $rec['archiv'] == 1 || $rec['deleted'] == 1) {
			    echo '<h2>';
			}
	        if ($rec['secret'] > 0) {
	            echo 'TAJNÉ: '.$rec['secret'];
	        }
	        if ($rec['dead'] == 1) {
	            echo 'MRTVOLA ';
	        }
	        if ($rec['archiv'] == 1) {
	            echo 'ARCHIV';
	        }
	        if ($rec['deleted'] == 1) {
	            echo 'SMAZANÝ ZÁZNAM';
	        }
	        if ($rec['secret'] == 1 || $rec['dead'] == 1 || $rec['archiv'] == 1 || $rec['deleted'] == 1) {
	            echo '</h2>';
	        } ?>
			<h3>Jméno: </h3><p><?php echo StripSlashes($rec['name']); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Příjmení: </h3><p><?php echo StripSlashes($rec['surname']); ?></p>
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
					case 9: $side = 'vykladač'; break;
					case 10: $side = 'jasnovidec'; break;
					default: $side = 'neznámá'; break;
				}
	        echo $side; ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Telefon: </h3><p><a href ="tel:<?php echo str_replace(' ', '',$rec['phone']); ?>"><?php echo $rec['phone']; ?></a></p>
			<div class="clear">&nbsp;</div>
			<h3>Patří do skupin: </h3><p><?php
				if ($user['aclDirector']) {
				    $sql = "SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."group, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."group.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."group.deleted=0 ORDER BY ".DB_PREFIX."group.title ASC";
				} else {
				    $sql = "SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."group, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."group.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."group.deleted=0 AND ".DB_PREFIX."group.secret=0 ORDER BY ".DB_PREFIX."group.title ASC";
				}
	        $res_g = mysqli_query ($database,$sql);
	        if (mysqli_num_rows ($res_g)) {
	            $groups = Array();
	            while ($rec_g = mysqli_fetch_assoc ($res_g)) {
	                $groups[] = '<a href="./readgroup.php?rid='.$rec_g['id'].'">'.StripSlashes ($rec_g['title']).'</a>';
	            }
	            echo implode ($groups,', ');
	        } else {
	            echo '&mdash;';
	        } ?></p>
			<div class="clear">&nbsp;</div>
			<p><strong>Datum vytvoření:</strong> <?php echo webdate($rec['regdate']); ?>
				<strong>Vytvořil:</strong> <?php
				$name = getAuthor($rec['regid'],1);
	        echo (($rec['regid'] == 0) ? 'asi Krauz' : $name); ?> </p>
			<div class="clear">&nbsp;</div>
			<p><strong>Datum poslední změny:</strong> <?php echo webdate($rec['datum']); ?>
				<strong>Změnil:</strong> <?php
				$name = getAuthor($rec['iduser'],1);
	        echo $name; ?> </p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
<!-- náseduje popis osoby -->
	<fieldset>
		<legend><strong>Popis osoby</strong></legend>
		<div class="field-text"><?php echo (StripSlashes($rec['contents'])); ?></div>
	</fieldset>
	
<!-- násedují přiřazené případy a hlášení -->
	<fieldset>
		<legend><strong>Hlášení a případy</strong></legend>
		<h3>Figuruje v těchto případech: </h3><p><?php
				if ($user['aclDirector']) {
				    $sql_c = "SELECT ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."case, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idcase=".DB_PREFIX."case.id AND ".DB_PREFIX."c2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."case.deleted=0 ORDER BY ".DB_PREFIX."case.title ASC";
				} else {
				    $sql_c = "SELECT ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."case, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idcase=".DB_PREFIX."case.id AND ".DB_PREFIX."c2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."case.deleted=0 AND ".DB_PREFIX."case.secret=0 ORDER BY ".DB_PREFIX."case.title ASC";
				}
	        $res_c = mysqli_query ($database,$sql_c);
	        if (mysqli_num_rows ($res_c)) {
	            $cases = Array();
	            while ($rec_c = mysqli_fetch_assoc ($res_c)) {
	                $cases[] = '<a href="./readcase.php?rid='.$rec_c['id'].'">'.StripSlashes ($rec_c['title']).'</a>';
	            }
	            echo implode ($cases,'<br />');
	        } else {
	            echo 'Osoba nefiguruje v žádném případu.';
	        } ?></p>
		<div class="clear">&nbsp;</div>
                <h3>Figuruje v těchto hlášení: </h3><p><?php
				if ($user['aclDirector']) {
				    $sql_r = "SELECT ".DB_PREFIX."report.adatum as date_created, ".DB_PREFIX."report.datum as date_changed, ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."report, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idreport=".DB_PREFIX."report.id AND ".DB_PREFIX."ar2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC";
				} else {
				    $sql_r = "SELECT ".DB_PREFIX."report.adatum as date_created, ".DB_PREFIX."report.datum as date_changed, ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."ar2p.iduser FROM ".DB_PREFIX."report, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idreport=".DB_PREFIX."report.id AND ".DB_PREFIX."ar2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."report.deleted=0 AND ".DB_PREFIX."report.secret=0 ORDER BY ".DB_PREFIX."report.label ASC";
				}
	        $res_r = mysqli_query ($database,$sql_r);
	        if (mysqli_num_rows ($res_r)) {
	            $reports = Array();
	            while ($rec_r = mysqli_fetch_assoc ($res_r)) {
	                $reports[] = '<a href="./readactrep.php?rid='.$rec_r['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($rec_r['label']).'</a> | vytvořeno: '.(webdate($rec_r['date_created'])).' | změněno: '.(webdate($rec_r['date_changed']));
	            }
	            echo implode ($reports,'<br />');
	        } else {
	            echo 'Osoba nefiguruje v žádném hlášení.';
	        } ?></p>
		<div class="clear">&nbsp;</div>
	</fieldset>
	
<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
		if ($user['aclDirector']) {
		    $sql = "SELECT ".DB_PREFIX."file.mime as mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=1 ORDER BY ".DB_PREFIX."file.originalname ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."file.mime as mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=1 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
		}
	        $res = mysqli_query ($database,$sql);
	        $i = 0;
	        while ($rec = mysqli_fetch_assoc ($res)) {
	            $i++;
	            if ($i == 1) { ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
			<?php }
	            if (in_array($rec['mime'],$config['mime-image'])) { ?>
							<li><a href="getfile.php?idfile=<?php echo $rec['id']; ?>"><img  width="300px" alt="<?php echo StripSlashes($rec['title']); ?>" src="getfile.php?idfile=<?php echo $rec['id']; ?>"></a></li>
			<?php		} else { ?>
							<li><a href="getfile.php?idfile=<?php echo $rec['id']; ?>"><?php echo StripSlashes($rec['title']); ?></a></li>
			<?php }
	        }
	        if ($i <> 0) { ?>
	</ul>
	<!-- end of #prilozenadata -->
	</fieldset>
	<?php
		}
	        // konec seznamu přiložených souborů ?>

<?php //skryti poznamek
if ($hn != 1) { ?>
<!-- následuje seznam poznámek -->
	<?php // generování poznámek
		if ($user['aclDirector']) {
		    $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=1 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$user['userId'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=1 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$user['userId'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		}
		$res = mysqli_query ($database,$sql);
		$i = 0;
		while ($rec = mysqli_fetch_assoc ($res)) {
		    $i++;
		    if ($i == 1) { ?>
	<fieldset><legend><strong>Poznámky</strong></legend>
	<div id="poznamky"><?php
			}
		    if ($i > 1) {?>
		<hr /><?php
			} ?>
		<div class="poznamka">
			<h4><?php echo StripSlashes($rec['title']).' - '.(StripSlashes($rec['user'])).' ['.(webdate($rec['date_created'])).']';
		    if ($rec['secret'] == 0) {
		        echo ' (veřejná)';
		    }
		    if ($rec['secret'] == 1) {
		        echo ' (tajná)';
		    }
		    if ($rec['secret'] == 2) {
		        echo ' (soukromá)';
		    } ?></h4>
			<div><?php echo StripSlashes($rec['note']); ?></div>
			<span class="poznamka-edit-buttons"><?php
			if (($rec['iduser'] == $user['userId']) || ($usrinfo['right_text'])) {
			    echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=1" title="upravit"><span class="button-text">upravit</span></a> ';
			}
		    if (($rec['iduser'] == $user['userId']) || ($user['aclDirector'])) {
		        echo '<a class="delete" href="procnote.php?deletenote='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
		    } ?>
			</span>
		</div>
		<!-- end of .poznamka -->
	<?php
		}
		if ($i <> 0) { ?>
	</div>
	<!-- end of #poznamky -->
	</fieldset>
	<?php }
	// konec poznámek ?>
<?php } ?>	

</div>
<!-- end of #obsah -->

<?php
	    } else {
	        $_SESSION['message'] = "Osoba neexistuje!";
	        Header ('location: index.php');
	    }
	} else {
	    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	    Header ('location: index.php');
	}
        latteDrawTemplate("footer");
?>
