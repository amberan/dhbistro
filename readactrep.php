<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteHeader($latteParameters);

	if (is_numeric($_REQUEST['rid'])) {
	    $check = mysqli_fetch_assoc (mysqli_query ($database,"SELECT ".DB_PREFIX."user.idperson AS 'aid'
											FROM ".DB_PREFIX."user, ".DB_PREFIX."report
											WHERE ".DB_PREFIX."report.id=".$_REQUEST['rid']."
											AND ".DB_PREFIX."report.iduser=".DB_PREFIX."user.id"));
	    if ($check['aid'] == 0) {
	        $connector = '';
	        $notconnected = 1;
	    } else {
	        $connector = ' AND '.DB_PREFIX.'user.idperson='.DB_PREFIX.'person.id';
	        $notconnected = 0;
	    }
	    $sql = "SELECT
			".DB_PREFIX."report.datum AS 'datum',
                        ".DB_PREFIX."report.deleted AS 'deleted',
			".DB_PREFIX."report.label AS 'label',
			".DB_PREFIX."report.task AS 'task',
			".DB_PREFIX."report.summary AS 'summary',
			".DB_PREFIX."report.impacts AS 'impacts',
			".DB_PREFIX."report.details AS 'details',
			".DB_PREFIX."user.login AS 'autor',
			".DB_PREFIX."report.type AS 'type',
			".DB_PREFIX."report.adatum AS 'adatum',
			".DB_PREFIX."report.start AS 'start',
			".DB_PREFIX."report.end AS 'end',
			".DB_PREFIX."report.energy AS 'energy',
			".DB_PREFIX."report.inputs AS 'inputs',
			".DB_PREFIX."report.secret AS 'secret',
			".DB_PREFIX."person.name AS 'name',
			".DB_PREFIX."person.surname AS 'surname'
			FROM ".DB_PREFIX."report, ".DB_PREFIX."user, ".DB_PREFIX."person
			WHERE ".DB_PREFIX."report.iduser=".DB_PREFIX."user.id 
			AND ".DB_PREFIX."report.id=".$_REQUEST['rid'].$connector;
	    $res = mysqli_query ($database,$sql);
	    if ($rec_ar = mysqli_fetch_assoc ($res)) {
	        if (($rec_ar['secret'] > $usrinfo['right_power']) || $rec_ar['deleted'] == 1) {
	            unauthorizedAccess(4, $rec_ar['secret'], $rec_ar['deleted'], $_REQUEST['rid']);
	        }
	        if (isset($_SESSION['sid'])) {
	            auditTrail(4, 1, $_REQUEST['rid']);
	        }
	        $typestring = (($rec_ar['type'] == 1) ? 'výjezd' : (($rec_ar['type'] == 2) ? 'výslech' : '?')); //odvozuje slovní typ hlášení
	        $latteParameters['title'] = (StripSlashes('Hlášení'.(($rec_ar['type'] == 1) ? ' z výjezdu' : (($rec_ar['type'] == 2) ? ' z výslechu' : '')).': '.$rec_ar['label']));



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
	        if (!isset($_REQUEST['truenames'])) {
	            $tn = 0;
	        } else {
	            $tn = $_REQUEST['truenames'];
	        }
	        if (($usrinfo['right_power']) && ($hn == 0) && ($tn == 0)) {
	            $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">skrýt poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=1">zobrazit celá jména</a>';
	            $author = $rec_ar['autor'];
	            $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=0';
	        } else {
	            if (($usrinfo['right_power']) && ($hn == 1) && ($tn == 0)) {
	                $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=1">zobrazit celá jména</a>';
	                $author = $rec_ar['autor'];
	                $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=0';
	            } else {
	                if (($usrinfo['right_power']) && ($notconnected == 0) && ($hn == 1) && ($tn == 1)) {
	                    $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=1">zobrazit poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">zobrazit volací znaky</a>';
	                    $author = $rec_ar['surname'].' '.$rec_ar['name'];
	                    $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=1';
	                } else {
	                    if (($usrinfo['right_power']) && ($notconnected == 0) && ($hn == 0) && ($tn == 1)) {
	                        $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=1">skrýt poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit volací znaky</a>';
	                        $author = $rec_ar['surname'].' '.$rec_ar['name'];
	                        $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=1';
	                    } else {
	                        if (($usrinfo['right_power']) && ($notconnected == 1) && ($hn == 1) && ($tn == 1)) {
	                            $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=1">zobrazit poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">zobrazit volací znaky</a>';
	                            $author = 'NENÍ NAPOJEN';
	                            $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=1';
	                        } else {
	                            if (($usrinfo['right_power']) && ($notconnected == 1) && ($hn == 0) && ($tn == 1)) {
	                                $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=1">skrýt poznámky</a>; <a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit volací znaky</a>';
	                                $author = 'NENÍ NAPOJEN';
	                                $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=1';
	                            } else {
	                                if ($hn == 0) {
	                                    $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=1&amp;truenames=0">skrýt poznámky</a>';
	                                    $author = $rec_ar['autor'];
	                                    $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=0&truenames=0';
	                                } else {
	                                    if ($hn == 1) {
	                                        $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&amp;hidenotes=0&amp;truenames=0">zobrazit poznámky</a>';
	                                        $author = $rec_ar['autor'];
	                                        $backurl = 'readactrep.php?rid='.$_REQUEST['rid'].'&hidenotes=1&truenames=0';
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        if ($usrinfo['right_text']) {
	            $editbutton = '; <a href="editactrep.php?rid='.$_REQUEST['rid'].'">upravit hlášení</a>';
	        } else {
	            $editbutton = '';
	        }
	        deleteUnread (4,$_REQUEST['rid']);
	        sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>'.StripSlashes($rec_ar['label']).' ('.$typestring.')</strong>',$spaction.$editbutton); ?>
<div id="obsah">
	<h1><?php echo StripSlashes($rec_ar['label']); ?></h1>
	<div id="hlavicka" class="top">
		<span>[ <strong>Hlášení<?php echo((($rec_ar['type'] == 1) ? ' z výjezdu' : (($rec_ar['type'] == 2) ? ' z výslechu' : ' k akci'))); ?></strong> | </span>
		<span><strong>Vyhotovil: </strong><?php echo StripSlashes($author); ?> | </span>
		<span><strong>Dne: </strong><?php echo webdate($rec_ar['datum']); ?> ]</span>
		<br>
	</div>
	<fieldset><legend><strong>Obecné informace</strong></legend>
	<div id="info">
		<?php if ($rec_ar['secret'] == 1) {
	            echo '<h2>TAJNÉ</h2>';
	        } ?>
                <?php if ($rec_ar['deleted'] == 1) {
	            echo '<h2>SMAZANÝ ZÁZNAM</h2>';
	        } ?>
		<h3>Datum<?php echo((($rec_ar['type'] == 1) ? ' výjezdu' : (($rec_ar['type'] == 2) ? ' výslechu' : ' akce'))); ?>:</h3>
		<p><?php echo webdate($rec_ar['adatum']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Začátek<?php echo((($rec_ar['type'] == 1) ? ' výjezdu' : (($rec_ar['type'] == 2) ? ' výslechu' : ' akce'))); ?>:</h3>
		<p><?php echo StripSlashes($rec_ar['start']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Konec<?php echo((($rec_ar['type'] == 1) ? ' výjezdu' : (($rec_ar['type'] == 2) ? ' výslechu' : ' akce'))); ?>:</h3>
		<p><?php echo StripSlashes($rec_ar['end']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo((($rec_ar['type'] == 1) ? 'Úkol' : (($rec_ar['type'] == 2) ? 'Předmět výslechu' : 'Úkol'))); ?>:</h3>
		<p><?php echo StripSlashes($rec_ar['task']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo((($rec_ar['type'] == 1) ? 'Velitel zásahu' : (($rec_ar['type'] == 2) ? 'Vyslýchající' : 'Velitel akce'))); ?>: </h3>
		<p><?php
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=".(($rec_ar['type'] == 1) ? '4' : (($rec_ar['type'] == 2) ? '2' : '4'))." AND ".DB_PREFIX."person.deleted=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=".(($rec_ar['type'] == 1) ? '4' : (($rec_ar['type'] == 2) ? '2' : '4'))." AND ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		}
	        $res = mysqli_query ($database,$sql);
	        if (mysqli_num_rows ($res)) {
	            $groups = Array();
	            while ($rec_p = mysqli_fetch_assoc ($res)) {
	                $groups[] = '<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
	            }
	            echo implode ($groups,'; ');
	        } else { ?>
			<em>Není označen.</em><?php
		} ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo((($rec_ar['type'] == 1) ? 'Zatčený' : (($rec_ar['type'] == 2) ? 'Vyslýchaný' : 'Zatčený'))); ?>: </h3>
		<p><?php
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=".(($rec_ar['type'] == 1) ? '3' : (($rec_ar['type'] == 2) ? '1' : '3'))." AND ".DB_PREFIX."person.deleted=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=".(($rec_ar['type'] == 1) ? '3' : (($rec_ar['type'] == 2) ? '1' : '3'))." AND ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		}
	        $res = mysqli_query ($database,$sql);
	        if (mysqli_num_rows ($res)) {
	            $groups = Array();
	            while ($rec_p = mysqli_fetch_assoc ($res)) {
	                $groups[] = '<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
	            }
	            echo implode ($groups,'; ');
	        } else { ?>
			<em>Není označen.</em><?php
		} ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Osoby přítomné: </h3>
		<p><?php
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=0 AND ".DB_PREFIX."person.deleted=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=0 AND ".DB_PREFIX."person.deleted=0 AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		}
	        $res = mysqli_query ($database,$sql);
	        if (mysqli_num_rows ($res)) {
	            $groups = Array();
	            while ($rec_p = mysqli_fetch_assoc ($res)) {
	                $groups[] = '<a href="./readperson.php?rid='.$rec_p['id'].'">'.StripSlashes ($rec_p['surname']).', '.StripSlashes ($rec_p['name']).'</a>';
	            }
	            echo implode ($groups,'; ');
	        } else { ?>
			<em>K hlášení nejsou připojeny žádné osoby.</em><?php
		} ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Přiřazené případy:</h3>
		<?php
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."case.title ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."case.secret=0 ORDER BY ".DB_PREFIX."case.title ASC";
		}
	        $pers = mysqli_query ($database,$sql);
	        $i = 0;
	        while ($perc = mysqli_fetch_assoc ($pers)) {
	            $i++;
	            if ($i == 1) {?>
		<ul id="pripady"><?php
				} ?>
			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>" title=""><?php echo $perc['title']; ?></a></li>
		<?php
	        }
	        if ($i <> 0) { ?>
		</ul><?php
			} else { ?>
		<p><em>Hlášení není přiřazeno k žádnému případu.</em></p><?php
			} ?>
		<!-- end of #pripady -->
		<div class="clear">&nbsp;</div>				
	</div>
	<!-- end of #info -->
	</fieldset>
	
	<fieldset>
		<legend><strong>Shrnutí</strong></legend>
		<div class="field-text"><?php echo StripSlashes($rec_ar['summary']); ?></div>
	</fieldset>
	<fieldset>
		<legend><strong>Možné dopady</strong></legend>
		<div class="field-text"><?php echo StripSlashes($rec_ar['impacts']); ?></div>
	</fieldset>
	<fieldset>
		<legend><strong>Podrobný průběh</strong></legend>
		<div class="field-text"><?php echo StripSlashes($rec_ar['details']); ?></div>
	</fieldset>
	<fieldset>
	<legend><strong>Energetická náročnost</strong></legend>
		<div class="field-text"><?php echo StripSlashes($rec_ar['energy']); ?></div>
	</fieldset>
	<fieldset>
		<legend><strong>Počáteční vstupy<strong></legend>
		<div class="field-text"><?php echo StripSlashes($rec_ar['inputs']); ?></div>
	</fieldset>

	
	
<!-- následuje seznam přiložených symbolů -->
	<?php //skryti symbolů
	if ($hs != 1) { ?>
	<fieldset><legend><strong>Přiložené symboly</strong></legend>
	<?php //generování seznamu přiložených symbolů
	$sql_s = "SELECT ".DB_PREFIX."symbol2all.idsymbol AS 'id' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."symbol WHERE ".DB_PREFIX."symbol2all.idsymbol = ".DB_PREFIX."symbol.id AND ".DB_PREFIX."symbol.assigned=0 AND ".DB_PREFIX."symbol2all.idrecord=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4 AND ".DB_PREFIX."symbol.deleted=0";
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
	}
		?>
		
	</fieldset>
	<!-- konec seznamu přiložených symbolů -->
<?php } ?>	
		
<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."file.mime as mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=4 ORDER BY ".DB_PREFIX."file.originalname ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."file.mime as mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=4 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
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
	<?php
		}
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
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=4 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=4 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
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
			<h4><?php echo StripSlashes($rec['title']).' - '.StripSlashes($rec['user']).' ['.webdate($rec['date_created']).']'; ?><?php
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
			if (($rec['iduser'] == $usrinfo['id']) || ($usrinfo['right_text'])) {
			    echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=4" title="upravit"><span class="button-text">upravit</span></a> ';
			}
		    if (($rec['iduser'] == $usrinfo['id']) || ($usrinfo['right_power'])) {
		        echo '<a class="delete" href="procnote.php?deletenote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
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
	        $_SESSION['message'] = "Hlášení neexistuje!";
	        Header ('location: index.php');
	    }
	} else {
	    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	    Header ('location: index.php');
	}
	latteFooter($latteParameters);
?>