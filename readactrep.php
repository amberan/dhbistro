<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

    if (is_numeric($_REQUEST['rid'])) {
        $wasModifiedSql = "SELECT ".DB_PREFIX."user.personId AS 'aid'
											FROM ".DB_PREFIX."user, ".DB_PREFIX."report
											WHERE ".DB_PREFIX."report.reportId=".$_REQUEST['rid']."
											AND ".DB_PREFIX."report.reportModifiedBy=".DB_PREFIX."user.userId";
        $wasModified = mysqli_fetch_assoc(mysqli_query($database, $wasModifiedSql));
        if ($wasModified['aid'] == 0) {
            $connector = '';
            $notconnected = 1;
        } else {
            $connector = ' AND '.DB_PREFIX.'user.personId='.DB_PREFIX.'person.id';
            $notconnected = 0;
        }
        $sql = "SELECT
        	".DB_PREFIX."report.reportOwner,
			".DB_PREFIX."report.reportCreated AS 'datum',
            ".DB_PREFIX."report.reportDeleted AS 'deleted',
			".DB_PREFIX."report.reportName AS 'label',
			".DB_PREFIX."report.reportTask AS 'task',
			".DB_PREFIX."report.reportSummary AS 'summary',
			".DB_PREFIX."report.reportImpact AS 'impacts',
			".DB_PREFIX."report.reportDetail AS 'details',
			".DB_PREFIX."user.userName AS 'autor',
			".DB_PREFIX."report.reportType AS 'type',
			".DB_PREFIX."report.reportEventDate AS 'adatum',
			".DB_PREFIX."report.reportEventStart AS 'start',
			".DB_PREFIX."report.reportEventEnd AS 'end',
			".DB_PREFIX."report.reportCost AS 'energy',
			".DB_PREFIX."report.reportInput AS 'inputs',
			".DB_PREFIX."report.reportSecret AS 'secret',
			".DB_PREFIX."person.name AS 'name',
			".DB_PREFIX."person.surname AS 'surname'
			FROM ".DB_PREFIX."report, ".DB_PREFIX."user, ".DB_PREFIX."person
			WHERE ".DB_PREFIX."report.reportModifiedBy=".DB_PREFIX."user.userId
			AND ".DB_PREFIX."report.reportId=".$_REQUEST['rid'].$connector;
        $res = mysqli_query($database, $sql);
        if ($rec_ar = mysqli_fetch_assoc($res)) {
            if (($rec_ar['reportSecret'] > $user['aclSecret']) || ($user['aclRoot'] > 0 && $rec_ar['reportDeleted'] != null)) {
                unauthorizedAccess(4, 1, $_REQUEST['rid']);
            }
            if (isset($_SESSION['sid'])) {
                authorizedAccess(4, 1, $_REQUEST['rid']);
            }
            $typestring = $rec_ar['type'] == 1 ? 'výjezd' : ($rec_ar['type'] == 2 ? 'výslech' : '?'); //odvozuje slovní typ hlášení
            $latteParameters['title'] = (stripslashes('Hlášení'.$rec_ar['type'] == 1 ? ' z výjezdu' : ($rec_ar['type'] == 2 ? ' z výslechu' : '').': '.$rec_ar['label']));

            mainMenu();

            $notes = $names = $symbols = false;
            if (isset($_REQUEST['notes'])) {
                $notes = $_REQUEST['notes'];
            }
            if (isset($_REQUEST['symbols'])) {
                $symbols = $_REQUEST['symbols'];
            }
            if (isset($_REQUEST['names'])) {
                $names = $_REQUEST['names'];
            }

            if ($notes == 0) {
                $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&notes=1&names='.$symbols.'&symbols='.$symbols.'">zobrazit poznámky</a>; ';
            } else {
                $spaction = '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&notes=0&names='.$symbols.'&symbols='.$symbols.'">skrýt poznámky</a>; ';
            }

            if ($symbols == 0) {
                $spaction .= '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&notes='.$notes.'&names='.$symbols.'&symbols=1">zobrazit symboly</a>; ';
            } else {
                $spaction .= '<a href="readactrep.php?rid='.$_REQUEST['rid'].'&notes='.$notes.'&names='.$symbols.'&symbols=0">skrýt symboly</a>; ';
            }


            if ($names == 0) {
                $spaction .='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&notes='.$notes.'&names=1">zobrazit celá jména</a>';
            } else {
                $spaction .='<a href="readactrep.php?rid='.$_REQUEST['rid'].'&notes='.$notes.'&names=0">skrýt celá jména</a>';
            }

            if ($notconnected == 0 && $names == 1) {
                $author = $rec_ar['surname'].' '.$rec_ar['name'];
            } elseif ($notconnected == 0 && $names == 0) {
                $author = $rec_ar['autor'];
            } else {
                $author = 'Nuzivatel neni napojen na osobu';
            }


            if ($user['aclReport']) {
                $editbutton = '; <a href="editactrep.php?rid='.$_REQUEST['rid'].'">upravit hlášení</a>';
            } else {
                $editbutton = '';
            }
            deleteUnread(4, $_REQUEST['rid']);
            sparklets('<a href="./reports.php">hlášení</a> &raquo; <strong>'.stripslashes($rec_ar['label']).' ('.$typestring.')</strong>', $spaction.$editbutton);

            $leaderSql = 'SELECT '.DB_PREFIX.'person.secret, '.DB_PREFIX.'person.name, '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.id, '.DB_PREFIX.'ar2p.iduser, '.DB_PREFIX.'ar2p.role
            FROM '.DB_PREFIX.'person, '.DB_PREFIX.'ar2p
            WHERE '.DB_PREFIX.'ar2p.idperson='.DB_PREFIX.'person.id AND '.DB_PREFIX.'ar2p.idreport='.$_REQUEST['rid'].' AND '.$user['sqlDeleted'].' AND '.$user['sqlSecret'].' AND '.DB_PREFIX.'ar2p.role=';
            if ($rec_ar['type'] == 2) {
                $leaderSql .= '2';
            } else {
                $leaderSql .= '4';
            }
            $leaderSql .= ' ORDER BY '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.name ASC';
            $leaderQuery = mysqli_query($database, $leaderSql);

            $arestedSql = 'SELECT '.DB_PREFIX.'person.secret, '.DB_PREFIX.'person.name, '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.id, '.DB_PREFIX.'ar2p.iduser, '.DB_PREFIX.'ar2p.role
            FROM '.DB_PREFIX.'person, '.DB_PREFIX.'ar2p
            WHERE '.DB_PREFIX.'ar2p.idperson='.DB_PREFIX.'person.id AND '.DB_PREFIX.'ar2p.idreport='.$_REQUEST['rid'].' AND '.DB_PREFIX.'ar2p.role=';
            if ($rec_ar['type'] == 2) {
                $arestedSql .= '1';
            } else {
                $arestedSql .= '3';
            }
            $arestedSql .= ' ORDER BY '.DB_PREFIX.'person.surname, '.DB_PREFIX.'person.name ASC';
            $arestedQuery = mysqli_query($database, $arestedSql);

            $sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];
            $attendedSql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."ar2p.iduser, ".DB_PREFIX."ar2p.role
            FROM ".DB_PREFIX."person, ".DB_PREFIX."ar2p
            WHERE $sqlFilter AND ".DB_PREFIX."ar2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."ar2p.role=0
            ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
            $attendedQuery = mysqli_query($database, $attendedSql);

            $sqlFilter = DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];
            $casesSql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title'
            FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."case
            WHERE $sqlFilter AND ".DB_PREFIX."case.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']."
            ORDER BY ".DB_PREFIX."case.title ASC";
            $casesQuery = mysqli_query($database, $casesSql);

            $symbolsSql = "SELECT ".DB_PREFIX."symbol2all.idsymbol AS 'id' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."symbol WHERE ".DB_PREFIX."symbol2all.idsymbol = ".DB_PREFIX."symbol.id AND ".DB_PREFIX."symbol.assigned=0 AND ".DB_PREFIX."symbol2all.idrecord=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4 AND ".DB_PREFIX."symbol.deleted=0";
            $symbolsQuery = mysqli_query($database, $symbolsSql);

            $sqlFilter = DB_PREFIX."file.secret<=".$user['aclSecret'];
            $attachmentSql = "SELECT ".DB_PREFIX."file.mime as mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id'
            FROM ".DB_PREFIX."file
            WHERE $ sqlFilter AND ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=4
            ORDER BY ".DB_PREFIX."file.originalname ASC"; ?>





<div id="obsah">
	<h1><?php echo stripslashes($rec_ar['label']); ?></h1>
	<div id="hlavicka" class="top">
		<span>[ <strong>Hlášení<?php echo $rec_ar['type'] == 1 ? ' z výjezdu' : ($rec_ar['type'] == 2 ? ' z výslechu' : ' k akci'); ?></strong> | </span>
		<span><strong>Vyhotovil: </strong><?php echo stripslashes($author); ?> | </span>
		<span><strong>Dne: </strong><?php echo $rec_ar['datum']; ?> ]</span>
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
		<h3>Datum<?php echo $rec_ar['type'] == 1 ? ' výjezdu' : ($rec_ar['type'] == 2 ? ' výslechu' : ' akce'); ?>:</h3>
		<p><?php echo $rec_ar['adatum']; ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Začátek<?php echo $rec_ar['type'] == 1 ? ' výjezdu' : ($rec_ar['type'] == 2 ? ' výslechu' : ' akce'); ?>:</h3>
		<p><?php echo stripslashes($rec_ar['start']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Konec<?php echo $rec_ar['type'] == 1 ? ' výjezdu' : ($rec_ar['type'] == 2 ? ' výslechu' : ' akce'); ?>:</h3>
		<p><?php echo stripslashes($rec_ar['end']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo $rec_ar['type'] == 1 ? 'Úkol' : ($rec_ar['type'] == 2 ? 'Předmět výslechu' : 'Úkol'); ?>:</h3>
		<p><?php echo stripslashes($rec_ar['task']); ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo $rec_ar['type'] == 1 ? 'Velitel zásahu' : ($rec_ar['type'] == 2 ? 'Vyslýchající' : 'Velitel akce'); ?>: </h3>
		<p><?php

            if (mysqli_num_rows($leaderQuery)) {
                while ($leader = mysqli_fetch_assoc($leaderQuery)) {
                    $leaders[] = '<a href="./readperson.php?rid='.$leader['id'].'">'.stripslashes($leader['surname']).', '.stripslashes($leader['name']).'</a>';
                }
                echo implode('; ', $leaders);
            } else { ?>
			<em>Není označen.</em><?php
            } ?></p>
		<div class="clear">&nbsp;</div>
		<h3><?php echo $rec_ar['type'] == 1 ? 'Zatčený' : ($rec_ar['type'] == 2 ? 'Vyslýchaný' : 'Zatčený'); ?>: </h3>
		<p><?php

            if (mysqli_num_rows($arestedQuery)) {
                while ($arrested = mysqli_fetch_assoc($arestedQuery)) {
                    $arresteds[] = '<a href="./readperson.php?rid='.$arrested['id'].'">'.stripslashes($arrested['surname']).', '.stripslashes($arrested['name']).'</a>';
                }
                echo implode('; ', $arresteds);
            } else { ?>
			<em>Není označen.</em><?php
        } ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Osoby přítomné: </h3>
		<p><?php

            if (mysqli_num_rows($attendedQuery)) {
                while ($attended = mysqli_fetch_assoc($attendedQuery)) {
                    $attendeds[] = '<a href="./readperson.php?rid='.$attended['id'].'">'.stripslashes($attended['surname']).', '.stripslashes($attended['name']).'</a>';
                }
                echo implode('; ', $attendeds);
            } else { ?>
			<em>K hlášení nejsou připojeny žádné osoby.</em><?php
        } ?></p>
		<div class="clear">&nbsp;</div>
		<h3>Přiřazené případy:</h3>
		<?php

            $i = 0;
            while ($cases = mysqli_fetch_assoc($casesQuery)) {
                $i++;
                if ($i == 1) {?>
		<ul id="pripady"><?php
                } ?>
			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>" title=""><?php echo $perc['title']; ?></a></li>
		<?php
            }
            if ($i != 0) { ?>
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
		<div class="field-text"><?php echo stripslashes($rec_ar['summary']); ?></div>
	</fieldset>
	<fieldset>
		<legend><strong>Možné dopady</strong></legend>
		<div class="field-text"><?php echo stripslashes($rec_ar['impacts']); ?></div>
	</fieldset>
	<fieldset>
		<legend><strong>Podrobný průběh</strong></legend>
		<div class="field-text"><?php echo stripslashes($rec_ar['details']); ?></div>
	</fieldset>
	<fieldset>
	<legend><strong>Energetická náročnost</strong></legend>
		<div class="field-text"><?php echo stripslashes($rec_ar['energy']); ?></div>
	</fieldset>
	<fieldset>
		<legend><strong>Počáteční vstupy<strong></legend>
		<div class="field-text"><?php echo stripslashes($rec_ar['inputs']); ?></div>
	</fieldset>



<!-- následuje seznam přiložených symbolů -->
	<?php //skryti symbolů
    if ($symbols == 1) { ?>
	<fieldset><legend><strong>Přiložené symboly</strong></legend>
	<?php //generování seznamu přiložených symbolů

    if (mysqli_num_rows($symbolsQuery)) {
        $inc = 0; ?>
		<div id="symbols">
		<table>
		<?php
        while ($symbols = mysqli_fetch_assoc($symbolsQuery)) {
            if ($inc == 0 || $inc == 8) {
                echo '<tr>';
            }
            echo '<td><img src="file/symbol/'.$symbols['id'].'" alt="symbol chybí" /></td>';
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

            $attachmentQuery = mysqli_query($database, $attachmentSql);
            $i = 0;
            while ($attachement = mysqli_fetch_assoc($attachmentQuery)) {
                $i++;
                if ($i == 1) { ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
			<?php }
                if (in_array($attachement['mime'], $config['mime-image'], true)) { ?>
				<li><a href="file/attachement/<?php echo $attachement['id']; ?>"><img  width="300px" alt="<?php echo stripslashes($attachement['title']); ?>" src="file/attachement/<?php echo $attachement['id']; ?>"></a></li>
<?php		} else { ?>
				<li><a href="file/attachement/<?php echo $attachement['id']; ?>"><?php echo stripslashes($attachement['title']); ?></a></li>
	<?php
        }
            }
            if ($i != 0) { ?>
	</ul>
	<!-- end of #prilozenadata -->
	</fieldset>
	<?php
        }
            // konec seznamu přiložených souborů?>
<?php //skryti poznamek
if ($notes == 1) { ?>
<!-- následuje seznam poznámek -->
	<?php // generování poznámek
        $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
        $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
        FROM ".DB_PREFIX."note, ".DB_PREFIX."user
        WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=4
        ORDER BY ".DB_PREFIX."note.datum DESC";
        $res = mysqli_query($database, $sql);
        $i = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            $i++;
            if ($i == 1) { ?>
	<fieldset><legend><strong>Poznámky</strong></legend>
	<div id="poznamky"><?php
            }
            if ($i > 1) {?>
		<hr /><?php
            } ?>
		<div class="poznamka">
			<h4><?php echo stripslashes($rec['title']).' - '.stripslashes($rec['user']).' ['.webdate($rec['date_created']).']'; ?><?php
            if ($rec['secret'] == 0) {
                echo ' (veřejná)';
            }
            if ($rec['secret'] == 1) {
                echo ' (tajná)';
            }
            if ($rec['secret'] == 2) {
                echo ' (soukromá)';
            } ?></h4>
			<div><?php echo stripslashes($rec['note']); ?></div>
			<span class="poznamka-edit-buttons"><?php
            if (($rec['iduser'] == $user['userId']) || ($user['aclRepor'])) {
                echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=4" title="upravit"><span class="button-text">upravit</span></a> ';
            }
            if (($rec['iduser'] == $user['userId']) || ($user['aclReport'] > 1)) {
                echo '<a class="delete" href="procnote.php?deletenote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.urlencode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".stripslashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
            } ?>
			</span>
		</div>
		<!-- end of .poznamka -->
	<?php
        }
        if ($i != 0) { ?>
	</div>
	<!-- end of #poznamky -->
	</fieldset>
	<?php }
    // konec poznámek?>
<?php } ?>
</div>
<!-- end of #obsah -->
<?php
        } else {
            echo        $_SESSION['message'] = "Hlášení neexistuje!";
            //  header('location: index.php');
        }
    } else {
        echo    $_SESSION['message'] = $text['accessdeniedrecorded'];
        //    header('location: index.php');
    }
    latteDrawTemplate("footer");
?>
