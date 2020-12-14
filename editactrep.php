<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Zobrazení symbolu';

$reportarray = mysqli_fetch_assoc (mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."report WHERE id=".$_REQUEST['rid'])); // načte data z DB
$type = intval($reportarray['type']); // určuje typ hlášení
	$typestring = (($type == 1) ? 'výjezd' : (($type == 2) ? 'výslech' : '?')); //odvozuje slovní typ hlášení
$author = $reportarray['iduser']; // určuje autora hlášení


if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || ($user['userId'] == $author && $reportarray['status'] < 1))) {
    $sql = "SELECT
		".DB_PREFIX."report.id AS 'id',
		".DB_PREFIX."report.datum AS 'datum',
		".DB_PREFIX."report.label AS 'label',
		".DB_PREFIX."report.task AS 'task',
                ".DB_PREFIX."report.deleted AS 'deleted',
		".DB_PREFIX."report.summary AS 'summary',
		".DB_PREFIX."report.impacts AS 'impacts',
		".DB_PREFIX."report.details AS 'details',
		".DB_PREFIX."report.secret AS 'secret',
		".DB_PREFIX."report.status AS 'status',
		".DB_PREFIX."user.userName AS 'autor',
		".DB_PREFIX."report.type AS 'type',
		".DB_PREFIX."report.adatum AS 'adatum',
		".DB_PREFIX."report.start AS 'start',
		".DB_PREFIX."report.end AS 'end',
		".DB_PREFIX."report.energy AS 'energy',
		".DB_PREFIX."report.inputs AS 'inputs'
		FROM ".DB_PREFIX."report, ".DB_PREFIX."user
		WHERE ".DB_PREFIX."report.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."report.id=".$_REQUEST['rid'];
    $res = mysqli_query ($database,$sql);
    if ($rec_actr = mysqli_fetch_assoc ($res)) {
        //test oprávněnosti přístupu
        if (($rec_actr['secret'] > $user['aclDirector']) || $rec_actr['deleted'] == 1) {
            unauthorizedAccess(4, $rec_actr['secret'], $rec_actr['deleted'], $_REQUEST['rid']);
        }
        //auditní stopa
        auditTrail(4, 1, $_REQUEST['rid']);
        // následuje generování hlavičky
        $latteParameters['title'] = ('Úprava hlášení'.(($type == 1) ? ' z výjezdu' : (($type == 2) ? ' z výslechu' : '')));
        mainMenu ();
        sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>úprava hlášení'.(($type == 1) ? ' z výjezdu' : (($type == 2) ? ' z výslechu' : '')).'</strong>','<a href="symbols.php">přiřadit symboly</a>');

        $aday = (Date ('j',$rec_actr['adatum']));
        $amonth = (Date ('n',$rec_actr['adatum']));
        $ayear = (Date ('Y',$rec_actr['adatum'])); ?>
<div id="obsah">
    <form action="procactrep.php" method="post" id="inputform">
        <fieldset id="ramecek">
            <legend><strong>Úprava hlášení<?php echo (($type == 1) ? ' z výjezdu' : (($type == 2) ? ' z výslechu' : '')); ?></strong></legend>
            <fieldset>
                <legend><strong>Základní údaje</strong></legend>
                <div id="info">
                    <h3><label for="label">Označení&nbsp;<?php echo (($type == 1) ? 'výjezdu' : (($type == 2) ? 'výslechu' : 'hlášení')); ?>:</label></h3>
                    <input type="text" size="80" name="label" id="label" value="<?php echo StripSlashes($rec_actr['label']); ?>" />
                    <div class="clear">&nbsp;</div>
                    <h3><label for="task"><?php echo((($type == 1) ? 'Úkol' : (($type == 2) ? 'Předmět&nbsp;výslechu' : 'Úkol'))); ?>:</label></h3>
                    <input type="text" size="80" name="task" id="task" value="<?php echo StripSlashes($rec_actr['task']); ?>" />
                    <div class="clear">&nbsp;</div>
                    <h3><label for="adatum"><?php if ($type == '1') { ?>Datum&nbsp;akce<?php } else {
            if ($type == '2') { ?>Datum&nbsp;výslechu<?php }
        }; ?>:</label></h3>
                    <?php echo date_picker("adatum")?>
                    <div class="clear">&nbsp;</div>
                    <h3><label for="start">Začátek:</label></h3>
                    <input type="text" name="start" id="start" value="<?php echo StripSlashes($rec_actr['start']); ?>" />
                    <div class="clear">&nbsp;</div>
                    <h3><label for="end">Konec:</label></h3>
                    <input type="text" name="end" id="end" value="<?php echo StripSlashes($rec_actr['end']); ?>" />
                    <div class="clear">&nbsp;</div>
                    <h3><label for="secret">Přísně tajné:</label></h3>
                    <select name="secret" id="secret">
                        <option value="0" <?php if ($rec_actr['secret'] == 0) {
            echo ' selected="selected"';
        } ?>>ne</option>
                        <option value="1" <?php if ($rec_actr['secret'] == 1) {
            echo ' selected="selected"';
        } ?>>ano</option>
                    </select>
                    <div class="clear">&nbsp;</div>
                    <h3><label for="status">Stav:</label></h3>
                    <select name="status" id="status">
                        <option value="0" <?php if ($rec_actr['status'] == 0) {
            echo ' selected="selected"';
        } ?>>rozpracované</option>
                        <option value="1" <?php if ($rec_actr['status'] == 1) {
            echo ' selected="selected"';
        } ?>>dokončené</option>
                        <?php if ($usrinfo['right_text']) {
            echo '<option value="2"';
            if ($rec_actr['status'] == 2) {
                echo ' selected="selected"';
            }
            echo '>analyzované</option>';
            echo '<option value="3"';
            if ($rec_actr['status'] == 3) {
                echo ' selected="selected"';
            }
            echo '>archivované</option>';
        } ?>
                    </select>
                    <div class="clear">&nbsp;</div>
                </div>
                <!-- end of #info -->
            </fieldset>

            <fieldset>
                <legend><strong>Shrnutí:</strong></legend>
                <textarea cols="80" rows="7" name="summary" id="summary"><?php echo StripSlashes($rec_actr['summary']); ?></textarea>
            </fieldset>

            <fieldset>
                <legend><strong>Možné dopady:</strong></legend>
                <textarea cols="80" rows="7" name="impacts" id="impacts"><?php echo StripSlashes($rec_actr['impacts']); ?></textarea>
            </fieldset>

            <fieldset>
                <legend><strong>Podrobný popis průběhu:</strong></legend>
                <textarea cols="80" rows="30" name="details" id="details"><?php echo StripSlashes($rec_actr['details']); ?></textarea>
            </fieldset>

            <fieldset>
                <legend><strong>Energetická náročnost:</strong></legend>
                <textarea cols="80" rows="7" name="energy" id="energy"><?php echo StripSlashes($rec_actr['energy']); ?></textarea>
            </fieldset>

            <fieldset>
                <legend><strong>Počáteční vstupy:</strong></legend>
                <textarea cols="80" rows="7" name="inputs" id="inputs"><?php echo StripSlashes($rec_actr['inputs']); ?></textarea>
            </fieldset>

            <input type="hidden" name="reportid" value="<?php echo $rec_actr['id']; ?>" />
            <input type="submit" name="editactrep" id="submitbutton" value="Uložit změny" />
        </fieldset>
    </form>

    <fieldset>
        <legend><strong>Osoby přiřazené k hlášení: </strong></legend>
        <form action="addp2ar.php" method="post" class="otherform">
            <input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
            <input type="submit" value="Upravit osoby" name="setperson" class="submitbutton editbutton" title="Upravit osoby" />
        </form>
        <p><?php
		if ($user['aclDirector']) {
		    $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname' FROM ".DB_PREFIX."ar2p, ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id=".DB_PREFIX."ar2p.idperson AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname' FROM ".DB_PREFIX."ar2p, ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id=".DB_PREFIX."ar2p.idperson AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
		}
        $pers = mysqli_query ($database,$sql);
        $persons = Array();
        while ($perc = mysqli_fetch_assoc ($pers)) {
            $persons[] = '<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
        }
        echo ((implode($persons, '; ') <> "") ? implode($persons, '; ') : '<em>Nejsou připojeny žádné osoby.</em>'); ?></p>
    </fieldset>

    <fieldset>
        <legend><strong>Přiřazené případy</strong></legend>
        <!-- tady dochází ke stylové nesystematičnosti, nejedná se o poznámku; pro nápravu je třeba projít všechny šablony -->
        <p><span class="poznamka-edit-buttons"><a class="connect" href="addar2c.php?rid=<?php echo $_REQUEST['rid']; ?>" title="přiřazení"><span class="button-text">přiřazení případů</span></a><em style="font-size:smaller;">
                    (přiřazování)</em></span></p>
        <!-- následuje seznam případů -->
        <?php // generování seznamu přiřazených případů
			if ($user['aclDirector']) {
			    $sql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."case.title ASC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."case.secret=0 ORDER BY ".DB_PREFIX."case.title ASC";
			}
        $pers = mysqli_query ($database,$sql);
			
        $i = 0;
        while ($perc = mysqli_fetch_assoc ($pers)) {
            $i++;
            if ($i == 1) { ?>
        <ul id=""><?php
				} ?>
            <li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
            <?php
        }
        if ($i <> 0) { ?>
        </ul>
        <!-- end of # -->
        <?php
			} else {?><br />
        <em>Žádný případ nebyl přiřazen.</em><?php
			}
        // konec seznamu přiřazených případů ?>
    </fieldset>

    <!-- následuje seznam přiložených souborů -->
    <fieldset>
        <legend><strong>Přiložené soubory</strong></legend>
        <strong><em>K hlášení je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
        <?php //generování seznamu přiložených souborů
			if ($user['aclDirector']) {
			    $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=4 ORDER BY ".DB_PREFIX."file.originalname ASC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=4 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
			}
        $res = mysqli_query ($database,$sql);
        $i = 0;
        while ($rec_f = mysqli_fetch_assoc ($res)) {
            $i++;
            if ($i == 1) { ?>
        <ul id="prilozenadata">
            <?php } ?>
            <li class="soubor"><a href="getfile.php?idfile=<?php echo $rec_f['id']; ?>" title=""><?php echo StripSlashes($rec_f['title']); ?></a><?php if ($rec_f['secret'] == 1) { ?> (TAJNÝ)<?php }; ?><span
                      class="poznamka-edit-buttons"><?php
				if (($rec_f['iduser'] == $user['userId']) || ($user['aclDirector'])) {
				    echo '<a class="delete" title="smazat" href="procactrep.php?deletefile='.$rec_f['id'].'&amp;reportid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editactrep.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.StripSlashes($rec_f['title']).'&quot; náležící k hlášení?\')"><span class="button-text">smazat soubor</span></a>';
				} ?>
                </span></li><?php
        }
        if ($i <> 0) { ?>
        </ul>
        <!-- end of #prilozenadata -->
        <?php
			} else {?><br />
        <em>bez přiložených souborů</em><?php
			}
        // konec seznamu přiložených souborů ?>
    </fieldset>
    <!-- formulář přiřazení nových souborů -->
    <div id="new-file" class="otherform-wrap">
        <fieldset>
            <legend><strong>Nový soubor</strong></legend>
            <form action="procactrep.php" method="post" enctype="multipart/form-data" class="otherform">
                <div>
                    <strong><label for="attachment">Soubor:</label></strong>
                    <input type="file" name="attachment" id="attachment" />
                </div>
                <div>
                    <strong><label for="usecret">Přísně tajné:</label></strong>
                    <?php if ($rec_actr['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked" />ne&nbsp;/<?php }; ?>
                    &nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_actr['secret'] == 1) { ?>checked="checked" <?php }; ?> />ano
                </div>
                <?php 		if ($user['aclGamemaster'] == 1) {
            echo '					
			<div>
			<strong><label for="fnotnew">Není nové</label></strong>
			<input type="checkbox" name="fnotnew"/><br/>
			</div>';
        } ?>
                <div>
                    <input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
                    <input type="hidden" name="backurl" value="<?php echo 'editactrep.php?rid='.$_REQUEST['rid']; ?>" />
                    <input type="submit" name="uploadfile" value="Nahrát soubor k případu" class="submitbutton" title="Uložit" />
                </div>
            </form>
        </fieldset>
    </div>
    <!-- end of #new-file .otherform-wrap -->

    <!-- následuje seznam připojených poznámek -->
    <fieldset>
        <legend><strong>Poznámky</strong></legend>
        <span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=4" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K
                hlášení si můžete připsat kolik poznámek chcete.)</em></span>
        <!-- následuje seznam poznámek -->
        <?php // generování poznámek
			if ($user['aclDirector']) {
			    $sql = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=4 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret<2 OR ".DB_PREFIX."note.iduser=".$user['userId'].") ORDER BY ".DB_PREFIX."note.datum DESC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=4 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$user['userId'].") ORDER BY ".DB_PREFIX."note.datum DESC";
			}
        $res = mysqli_query ($database,$sql);
        $i = 0;
        while ($rec_n = mysqli_fetch_assoc ($res)) {
            $i++;
            if ($i == 1) { ?>
        <div id="poznamky"><?php
				}
            if ($i > 1) {?>
            <hr /><?php
				} ?>
            <div class="poznamka">
                <h4><?php echo StripSlashes($rec_n['title']).' - '.StripSlashes($rec_n['user']); ?><?php
				if ($rec_n['secret'] == 0) {
				    echo ' (veřejná)';
				}
            if ($rec_n['secret'] == 1) {
                echo ' (tajná)';
            }
            if ($rec_n['secret'] == 2) {
                echo ' (soukromá)';
            } ?></h4>
                <div><?php echo StripSlashes($rec_n['note']); ?></div>
                <span
                      class="poznamka-edit-buttons"><?php
				if (($rec_n['iduser'] == $user['userId']) || ($usrinfo['right_text'])) {
				    echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=4" title="upravit"><span class="button-text">upravit</span></a> ';
				}
            if (($rec_n['iduser'] == $user['userId']) || ($user['aclDirector'])) {
                echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editactrep.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k hlášení?');".'" title="smazat"><span class="button-text">smazat</span></a>';
            } ?>
                </span>
            </div>
            <!-- end of .poznamka -->
            <?php
        }
        if ($i <> 0) { ?>
        </div>
        <!-- end of #poznamky -->
        <?php
			} else {?><br />
        <em>bez poznámek</em><?php
			}
        // konec poznámek ?>
    </fieldset>
    <div id="new-note" class="otherform-wrap">
        <fieldset>
            <legend><strong>Nová poznámka</strong></legend>
            <form action="procnote.php" method="post" class="otherform">
                <div>
                    <strong><label for="notetitle">Nadpis:</label></strong>
                    <input type="text" name="title" id="notetitle" />
                </div>
                <div>
                    <strong><label for="nsecret">Utajení:</label></strong>
                    <?php if ($rec_actr['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" id="nsecret" value="0" checked="checked" />veřejná&nbsp;/<?php }; ?>
                    &nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_actr['secret'] == 1) { ?>checked="checked" <?php }; ?> />tajná&nbsp;/
                    &nbsp;<input type="radio" name="secret" value="2" />soukromá
                </div>
                <?php 			if ($user['aclGamemaster'] == 1) {
            echo '					
				<div>
				<strong><label for="nnotnew">Není nové</label></strong>
					<input type="checkbox" name="nnotnew"/><br/>
				</div>';
        } ?>
                <div>
                    <!--  label for="notebody">Tělo poznámka:</label -->
                    <textarea cols="80" rows="7" name="note" id="notebody"></textarea>
                </div>
                <div>
                    <input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
                    <input type="hidden" name="backurl" value="<?php echo 'editactrep.php?rid='.$_REQUEST['rid']; ?>" />
                    <input type="hidden" name="tableid" value="4" />
                    <input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" title="Uložit" />
                </div>
            </form>
        </fieldset>
    </div>
    <!-- end of #new-note .otherform-wrap -->



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
latteDrawTemplate("footer");
?>
