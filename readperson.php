<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

    if (is_numeric($_REQUEST['rid'])) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."person WHERE id=".$_REQUEST['rid']);
        if ($rec = mysqli_fetch_assoc($res)) {
            if (($rec['secret'] > $user['aclSecret']) || $rec['deleted'] == 1) {
                unauthorizedAccess(1, 1, $_REQUEST['rid']);
            }
            authorizedAccess(1, 1, $_REQUEST['rid']);

            $latteParameters['title'] = stripslashes($rec['surname']).', '.stripslashes($rec['name']);
            mainMenu();
            if (!isset($_REQUEST['hidenotes'])) {
                $hn = 0;
            } else {
                $hn = $_REQUEST['hidenotes'];
            }
            if ($hn == 0) {
                $hidenotes = '&amp;hidenotes=1">skrýt poznámky</a>';
            } else {
                $hidenotes = '&amp;hidenotes=0">zobrazit poznámky</a>';
            }
            if ($user['aclGamemaster'] || $user['aclUser']) {
                $editbutton = '; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>; číslo osoby: '.$rec['id'];
            } elseif ($user['aclperson']) {
                $editbutton = '; <a href="editperson.php?rid='.$_REQUEST['rid'].'">upravit osobu</a>';
            } else {
                $editbutton = '';
            }
            deleteUnread(1, $_REQUEST['rid']);
            sparklets('<a href="/persons/">osoby</a> &raquo; <strong>'.stripslashes($rec['surname']).', '.stripslashes($rec['name']).'</strong>', '<a href="readperson.php?rid='.$_REQUEST['rid'].$hidenotes.$editbutton); ?>
<div id="obsah">
	<h1><?php echo stripslashes($rec['surname']).', '.stripslashes($rec['name']); ?></h1>
	<fieldset>
        <legend>
            <strong>Základní údaje</strong>
        </legend>
		<?php if ($rec['portrait'] == null) { ?>
            <img src="#" alt="portrét chybí" title="portrét chybí" id="portraitimg" class="noname"/>
		<?php } else { ?>
            <img src="file/portrait/<?php echo $_REQUEST['rid']; ?>" alt="<?php echo stripslashes($rec['name']).' '.stripslashes($rec['surname']); ?>" id="portraitimg" />
		<?php } ?>
		<?php if ($rec['symbol'] == null) { ?>
            <img src="#" alt="symbol chybí" title="symbol chybí" id="symbolimg" class="noname"/>
		<?php } else { ?>
            <a href="readsymbol.php?rid=<?php echo $rec['symbol']; ?>"><img src="file/symbol/<?php echo $rec['symbol']; ?>" alt="<?php echo stripslashes($rec['name']).' '.stripslashes($rec['surname']); ?>" id="symbolimg" /></a>
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
			<h3>Jméno: </h3><p><?php echo stripslashes($rec['name']); ?></p>
			<div class="clear">&nbsp;</div>
			<h3>Příjmení: </h3><p><?php echo stripslashes($rec['surname']); ?></p>
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
            <?php
            if ($rec['roof'] > null) {
                echo '<h3>Dosažení stropu zaznamenáno: </h3><p>'.$rec['roof'].'</p><div class="clear">&nbsp;</div>';
            } ?>
			<div class="clear">&nbsp;</div>
            <?php
            if ($rec['roof'] > null) {
                echo '<h3>Dosažení stropu zaznamenáno: </h3><p>'.$rec['roof'].'</p><div class="clear">&nbsp;</div>';
            } ?>
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
			<h3>Telefon: </h3><p><a href ="tel:<?php echo str_replace(' ', '', $rec['phone']); ?>"><?php echo $rec['phone']; ?></a></p>
			<div class="clear">&nbsp;</div>
			<h3>Patří do skupin: </h3><p><?php
            $sqlFilter = DB_PREFIX."group.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."group.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."group.secret AS 'secret', ".DB_PREFIX."group.title AS 'title', ".DB_PREFIX."group.id AS 'id', ".DB_PREFIX."g2p.iduser
            FROM ".DB_PREFIX."group, ".DB_PREFIX."g2p
            WHERE $sqlFilter AND ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."group.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']."
            ORDER BY ".DB_PREFIX."group.title ASC";
            $res_g = mysqli_query($database, $sql);
            if (mysqli_num_rows($res_g)) {
                $groups = [];
                while ($rec_g = mysqli_fetch_assoc($res_g)) {
                    $groups[] = '<a href="./readgroup.php?rid='.$rec_g['id'].'">'.stripslashes($rec_g['title']).'</a>';
                }
                echo implode(', ', $groups);
            } else {
                echo '&mdash;';
            } ?></p>
			<div class="clear">&nbsp;</div>
			<p><strong>Datum vytvoření:</strong> <?php echo webdate($rec['regdate']); ?>
				<strong>Vytvořil:</strong> <?php
                $name = getAuthor($rec['regid'], 1);
            echo $rec['regid'] == 0 ? 'asi Krauz' : $name; ?> </p>
			<div class="clear">&nbsp;</div>
			<p><strong>Datum poslední změny:</strong> <?php echo webdate($rec['datum']); ?>
				<strong>Změnil:</strong> <?php
                $name = getAuthor($rec['iduser'], 1);
            echo $name; ?> </p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>
<!-- náseduje popis osoby -->
	<fieldset>
		<legend><strong>Popis osoby</strong></legend>
		<div class="field-text"><?php echo stripslashes($rec['contents']); ?></div>
	</fieldset>

<!-- násedují přiřazené případy a hlášení -->
	<fieldset>
		<legend><strong>Hlášení a případy</strong></legend>
		<h3>Figuruje v těchto případech: </h3><p><?php
            $sqlFilter = DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];
            $sql_c = "SELECT ".DB_PREFIX."case.secret AS 'secret', ".DB_PREFIX."case.title AS 'title', ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."c2p.iduser
            FROM ".DB_PREFIX."case, ".DB_PREFIX."c2p
            WHERE $sqlFilter AND ".DB_PREFIX."c2p.idcase=".DB_PREFIX."case.id AND ".DB_PREFIX."c2p.idperson=".$_REQUEST['rid']."
            ORDER BY ".DB_PREFIX."case.title ASC";
            $res_c = mysqli_query($database, $sql_c);
            if (mysqli_num_rows($res_c)) {
                $cases = [];
                while ($rec_c = mysqli_fetch_assoc($res_c)) {
                    $cases[] = '<a href="./readcase.php?rid='.$rec_c['id'].'">'.stripslashes($rec_c['title']).'</a>';
                }
                echo implode($cases, '<br />');
            } else {
                echo 'Osoba nefiguruje v žádném případu.';
            } ?></p>
		<div class="clear">&nbsp;</div>
                <h3>Figuruje v těchto hlášení: </h3><p><?php
                if ($user['aclRoot'] < 1) {
                    $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
                }
            $sqlFilter .= " AND ".DB_PREFIX."report.reportSecret<=".$user['aclSecret'];
            $sql_r = "SELECT ".DB_PREFIX."report.reportCreated as date_created, ".DB_PREFIX."report.reportModified as date_changed, ".DB_PREFIX."report.reportSecret AS 'secret', ".DB_PREFIX."report.reportName AS 'label', ".DB_PREFIX."report.reportId AS 'id', ".DB_PREFIX."ar2p.iduser
                FROM ".DB_PREFIX."report, ".DB_PREFIX."ar2p
                WHERE $sqlFilter AND ".DB_PREFIX."ar2p.idreport=".DB_PREFIX."report.reportId AND ".DB_PREFIX."ar2p.idperson=".$_REQUEST['rid']."
                ORDER BY ".DB_PREFIX."report.reportName ASC";
            $res_r = mysqli_query($database, $sql_r);
            if (mysqli_num_rows($res_r)) {
                $reports = [];
                while ($rec_r = mysqli_fetch_assoc($res_r)) {
                    $reports[] = '<a href="/reports/'.$rec_r['id'].'">'.stripslashes($rec_r['label']).'</a> | vytvořeno: '.webdate($rec_r['date_created']).' | změněno: '.webdate($rec_r['date_changed']);
                }
                echo implode('<br />', $reports);
            } else {
                echo 'Osoba nefiguruje v žádném hlášení.';
            } ?></p>
		<div class="clear">&nbsp;</div>
	</fieldset>

<!-- následuje seznam přiložených souborů -->
	<?php //generování seznamu přiložených souborů
            $sqlFilter = DB_PREFIX."file.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."file.mime as mime, ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id'
            FROM ".DB_PREFIX."file
            WHERE $sqlFilter AND".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=1
            ORDER BY ".DB_PREFIX."file.originalname ASC";
            $res = mysqli_query($database, $sql);
            $i = 0;
            while ($rec = mysqli_fetch_assoc($res)) {
                $i++;
                if ($i == 1) { ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
			<?php }
                if (in_array($rec['mime'], $config['mime-image'], true)) { ?>
							<li><a href="file/attachement/<?php echo $rec['id']; ?>"><img  width="300px" alt="<?php echo stripslashes($rec['title']); ?>" src="file/attachement/<?php echo $rec['id']; ?>"></a></li>
			<?php		} else { ?>
							<li><a href="file/attachement/<?php echo $rec['id']; ?>"><?php echo stripslashes($rec['title']); ?></a></li>
			<?php }
            }
            if ($i != 0) { ?>
	</ul>
	<!-- end of #prilozenadata -->
	</fieldset>
	<?php
        }
            // konec seznamu přiložených souborů?>

<?php //skryti poznamek
if ($hn != 1) { ?>
<!-- následuje seznam poznámek -->
	<?php // generování poznámek
        $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
        $sql = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
        FROM ".DB_PREFIX."note, ".DB_PREFIX."user
        WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=1
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
			<h4><?php echo stripslashes($rec['title']).' - '.stripslashes($rec['user']).' ['.webdate($rec['date_created']).']';
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
            if (($rec['iduser'] == $user['userId']) || ($user['aclPerson'])) {
                echo '<a class="edit" href="editnote.php?rid='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=1" title="upravit"><span class="button-text">upravit</span></a> ';
            }
            if (($rec['iduser'] == $user['userId']) || ($user['aclPerson'] > 1)) {
                echo '<a class="delete" href="procnote.php?deletenote='.$rec['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.urlencode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".stripslashes($rec['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
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
            $_SESSION['message'] = "Osoba neexistuje!";
            header('location: index.php');
        }
    } else {
        $_SESSION['message'] = $text['accessdeniedrecorded'];
        header('location: index.php');
    }
        latteDrawTemplate("footer");
?>
