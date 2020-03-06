<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

if (is_numeric($_REQUEST['rid'])) {
    $sql_a = "SELECT * FROM ".DB_PREFIX."c2s WHERE ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid'];
    $res_a = mysqli_query ($database,$sql_a);
    $rec_a = mysqli_fetch_array ($res_a);
    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
    if ($rec = mysqli_fetch_assoc ($res)) {
        if ((($rec['secret'] > $usrinfo['right_power']) && $usrinfo['id'] <> $rec_a['idsolver']) || $rec['deleted'] == 1) {
            unauthorizedAccess(3, $rec['secret'], $rec['deleted'], $_REQUEST['rid']);
        }
        auditTrail(3, 1, $_REQUEST['rid']);


        $latteParameters['title'] = StripSlashes($rec['title']);


        mainMenu ();
        if (!isset($_REQUEST['hidenotes'])) {
            $hn = 0;
        } else {
            $hn = $_REQUEST['hidenotes'];
        }
        if (!isset($_REQUEST['hidesymbols'])) {
            $hs = 0;
        } else {
            $hs = $_REQUEST['hidesymbols'];
        }
        if ($hn == 0 && $hs == 0) {
            $spaction = '<a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;hidesymbols=0">skrýt poznámky</a>; <a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;hidesymbols=1">skrýt symboly</a>';
            $backurl = 'readcase.php?rid='.$_REQUEST['rid'].'&hidenotes=0&hidesymbols=0';
        } else {
            if ($hn == 0 && $hs == 1) {
                $spaction = '<a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;hidesymbols=1">skrýt poznámky</a>; <a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;hidesymbols=0">zobrazit symboly</a>';
                $backurl = 'readcase.php?rid='.$_REQUEST['rid'].'&hidenotes=0&hidesymbols=1';
            } else {
                if ($hn == 1 && $hs == 0) {
                    $spaction = '<a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;hidesymbols=0">zobrazit poznámky</a>; <a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;hidesymbols=1">skrýt symboly</a>';
                    $backurl = 'readcase.php?rid='.$_REQUEST['rid'].'&hidenotes=1&hidesymbols=0';
                } else {
                    if ($hn == 1 && $hs == 1) {
                        $spaction = '<a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;hidesymbols=1">zobrazit poznámky</a>; <a href="readcase.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;hidesymbols=0">zobrazit symboly</a>';
                        $backurl = 'readcase.php?rid='.$_REQUEST['rid'].'&hidenotes=1&hidesymbols=1';
                    }
                }
            }
        }
        if (($usrinfo['right_text']) && (($rec['secret'] == 0) || ($usrinfo['right_power']) || ($rec_a['iduser']))) {
            $editbutton = '; <a href="editcase.php?rid='.$_REQUEST['rid'].'">upravit případ</a>';
        } else {
            $editbutton = '';
        }
        deleteUnread (3,$_REQUEST['rid']);
        sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>'.StripSlashes($rec['title']).'</strong>',$spaction.$editbutton);
        if (($rec['secret'] == 1) && (!$usrinfo['right_power']) && (!$rec_a['iduser'])) {
            echo '<div id="obsah"><p>Hezký pokus.</p></div>';
        } else {
            ?>
<div id="obsah">
	<h1><?php echo StripSlashes($rec['title']); ?></h1>
	<fieldset><legend><strong>Obecné informace</strong></legend>
		<div id="info">
			<?php if ($rec['secret'] == 1) {
                echo '<h2>TAJNÉ</h2>';
            } ?>
			<?php if ($rec['deleted'] == 1) {
                echo '<h2>SMAZANÝ ZÁZNAM</h2>';
            } ?>
			<div class="clear">&nbsp;</div>
			<h3>Řešitelé: </h3>
			<p>
			<?php
			$sql = "SELECT ".DB_PREFIX."user.id AS 'id', ".DB_PREFIX."user.login AS 'login' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."user WHERE ".DB_PREFIX."user.id=".DB_PREFIX."c2s.idsolver AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.deleted=0 ORDER BY ".DB_PREFIX."user.login ASC";
            $pers = mysqli_query ($database,$sql);
            $solvers = Array();
            while ($perc = mysqli_fetch_assoc ($pers)) {
                $solvers[] = $perc['login'];
            }
            echo ((implode($solvers, '; ') <> "") ? implode($solvers, '; ') : '<em>Případ nemá přiřazené řešitele.</em>'); ?>
			</p>
			<div class="clear">&nbsp;</div>
			<h3>Osoby spojené s případem: </h3>
			<p>
			<?php
			if ($usrinfo['right_power']) {
			    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."person, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."person.deleted=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."c2p.iduser FROM ".DB_PREFIX."person, ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
			}
            $res = mysqli_query ($database,$sql);
            if (mysqli_num_rows ($res)) {
                $groups = Array();
                while ($rec_p = mysqli_fetch_assoc ($res)) {
                    $groups[] = '<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
                }
                echo implode ($groups,', ');
            } else {
                echo "<em>K případu nejsou připojeny žádné osoby.</em>";
            } ?>
			</p>
			<div class="clear">&nbsp;</div>
			<h3>Hlášení přiřazená k případu:</h3>
				<?php
				if ($usrinfo['right_power']) {
				    $sql = "SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.task AS 'task', ".DB_PREFIX."report.type AS 'type', ".DB_PREFIX."report.adatum AS 'adatum', ".DB_PREFIX."user.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."report, ".DB_PREFIX."user WHERE ".DB_PREFIX."report.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.id=".DB_PREFIX."report.iduser ORDER BY ".DB_PREFIX."report.label ASC";
				} else {
				    $sql = "SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.task AS 'task', ".DB_PREFIX."report.type AS 'type', ".DB_PREFIX."report.adatum AS 'adatum', ".DB_PREFIX."user.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."report, ".DB_PREFIX."user WHERE ".DB_PREFIX."report.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.id=".DB_PREFIX."report.iduser AND ".DB_PREFIX."report.secret=0 ORDER BY ".DB_PREFIX."report.label ASC";
				}
            $pers = mysqli_query ($database,$sql);
            $i = 0;
            while ($perc = mysqli_fetch_assoc ($pers)) {
                $i++;
                if ($i == 1) {
                    echo '<ul id="pripady">';
                } ?>
					<li><a href="readactrep.php?rid=<?php echo $perc['id']; ?>&hidenotes=0&truenames=0"><?php echo $perc['label']; ?></a> <span class="top">[ <strong><?php echo((($perc['type'] == 1) ? 'Výjezd' : (($perc['type'] == 2) ? 'Výslech' : 'Hlášení'))); ?></strong> | <strong>Ze dne:</strong> <?php echo Date ('d.m.Y',$perc['adatum']); ?> | <strong>Vyhotovil:</strong> <?php echo $perc['user']; ?> ]</span> - <?php echo $perc['task']; ?></li>
				<?php
            }
            if ($i <> 0) {
                echo "</ul>\n<!-- end of #pripady -->";
            } else {
                echo "<p><em>K případu není přiřazeno žádné hlášení.</em></p>";
            } ?>
				<div class="clear">&nbsp;</div>
				<p>	
					<strong>Datum poslední změny:</strong> <?php echo webdate($rec['datum']); ?>
					<strong>Změnil:</strong> 
					<?php echo 	$name = getAuthor($rec['iduser'],1); ?> 
				</p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
	
	<fieldset><legend><strong>Popis</strong></legend>
		<div class="field-text"><?php echo StripSlashes($rec['contents']); ?></div>
	</fieldset>

<!-- následuje seznam přiložených symbolů -->
	<?php //skryti symbolů
		if ($hs != 1) { ?>
	<fieldset><legend><strong>Přiložené symboly</strong></legend>
	<?php //generování seznamu přiložených symbolů
		$sql_s = "SELECT ".DB_PREFIX."symbol2all.idsymbol AS 'id' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."symbol WHERE ".DB_PREFIX."symbol2all.idsymbol = ".DB_PREFIX."symbol.id AND ".DB_PREFIX."symbol.assigned=0 AND ".DB_PREFIX."symbol2all.idrecord=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."symbol.deleted=0";
		$res_s = mysqli_query ($database,$sql_s);
		if (mysqli_num_rows ($res_s)) {
		    $inc = 0; ?>
		<div id="symbols">
		<table>
		<?php
			while ($rec_s = mysqli_fetch_assoc ($res_s)) {
			    if ($inc == 0 || $inc == 8) {
			        echo '<tr>';
			    }
			    echo '<td><img src="getportrait.php?nrid='.$rec_s['id'].'" alt="symbol chybí" /></td>';
			    if ($inc == 7) {
			        echo '</tr>';
			    }
			    $inc++;
			} ?> </table></div> <?php
		} else {
		    echo 'Žádné přiložené symboly.';
		} ?>
	</fieldset>
	<!-- konec seznamu přiložených symbolů -->
	<?php } ?>	
	
	<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."file.mime as mime,  ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=3 ORDER BY ".DB_PREFIX."file.originalname ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."file.mime as mime,  ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=3 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
		}
            $res_f = mysqli_query ($database,$sql);
            $i = 0;
            while ($rec_f = mysqli_fetch_assoc ($res_f)) {
                $i++;
                if ($i == 1) { ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
		<?php }
                if (in_array($rec_f['mime'],$config['mime-image'])) { ?>
							<li><a href="getfile.php?idfile=<?php echo $rec_f['id']; ?>"><img  width="300px" alt="<?php echo StripSlashes($rec_f['title'] ); ?>" src="getfile.php?idfile=<?php echo $rec_f['id']; ?>"></a></li>
			<?php		} else { ?>
							<li><a href="getfile.php?idfile=<?php echo $rec_f['id']; ?>"><?php echo StripSlashes($rec_f['title']); ?></a></li>
			<?php }
            }
            if ($i <> 0) {
                echo "</ul>\n<!-- end of #prilozenadata -->\n</fieldset>";
            }
            // konec seznamu přiložených souborů ?>

	<?php //skryti poznamek
	if ($hn != 1) { ?>
	<!-- následuje seznam poznámek -->
	<?php // generování poznámek
		if ($usrinfo['right_power']) {
		    $sql_n = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=3 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		} else {
		    $sql_n = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=3 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		}
		$res_n = mysqli_query ($database,$sql_n);
		$i = 0;
			while ($rec_n = mysqli_fetch_assoc ($res_n)) {
			    $i++;
			    if ($i == 1) { ?>
	<fieldset><legend><strong>Poznámky</strong></legend>
	<div id="poznamky"><?php
				}
			    if ($i > 1) {?>
		<hr /><?php
				} ?>
		<div class="poznamka">
			<h4>
			<?php
				echo StripSlashes($rec_n['title']).' - '.StripSlashes($rec_n['user']).' ['.webdate($rec_n['date_created']).']';
			    if ($rec_n['secret'] == 0) {
			        echo ' (veřejná)';
			    }
			    if ($rec_n['secret'] == 1) {
			        echo ' (tajná)';
			    }
			    if ($rec_n['secret'] == 2) {
			        echo ' (soukromá)';
			    } ?>
			</h4>
			<div><?php echo StripSlashes($rec_n['note']); ?></div>
			<span class="poznamka-edit-buttons"><?php
				if (($rec_n['iduser'] == $usrinfo['id']) || ($usrinfo['right_text'])) {
				    echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=3" title="upravit"><span class="button-text">upravit</span></a> ';
				}
			    if (($rec_n['iduser'] == $usrinfo['id']) || ($usrinfo['right_power'])) {
			        echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
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
	<?php 	}
	// konec poznámek
		} ?>	
</div>
<!-- end of #obsah -->
<?php
        }
    } else {
        $_SESSION['message'] = "Případ neexistuje!";
        Header ('location: index.php');
    }
} else {
    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
    Header ('location: index.php');
}
latteDrawTemplate("footer");
?>