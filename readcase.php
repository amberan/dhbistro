<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

if (is_numeric($_REQUEST['rid'])) {
    $sql_a = "SELECT * FROM ".DB_PREFIX."c2s WHERE ".DB_PREFIX."c2s.idsolver=".$user['userId']." AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid'];
    $res_a = mysqli_query($database, $sql_a);
    $rec_a = mysqli_fetch_array($res_a);
    $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
    if ($rec = mysqli_fetch_assoc($res)) {
        if ((($rec['secret'] > $user['aclSecret']) && $user['userId'] != $rec_a['idsolver']) || $rec['deleted'] == 1) {
            unauthorizedAccess(3, 1, $_REQUEST['rid']);
        }
        authorizedAccess(3, 1, $_REQUEST['rid']);

        $latteParameters['title'] = stripslashes($rec['title']);

        mainMenu();
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
        if (($rec['secret'] <= $user['aclSecret']) || (!$rec_a['iduser'])) {
            $editbutton = '; <a href="editcase.php?rid='.$_REQUEST['rid'].'">upravit případ</a>';
        } else {
            $editbutton = '';
        }
        deleteUnread(3, $_REQUEST['rid']);
        sparklets('<a href="/cases/">případy</a> &raquo; <strong>'.stripslashes($rec['title']).'</strong>', $spaction.$editbutton);
        if (($rec['secret'] > $user['aclSecret']) && (!$rec_a['iduser'])) {
            echo '<div id="obsah"><p>Hezký pokus.</p></div>';
        } else {
            ?>
<div id="obsah">
	<h1><?php echo stripslashes($rec['title']); ?></h1>
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
            $sql = "SELECT ".DB_PREFIX."user.userId AS 'id', ".DB_PREFIX."user.userName AS 'login' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."user WHERE ".DB_PREFIX."user.userId=".DB_PREFIX."c2s.idsolver AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.userDeleted=0 ORDER BY ".DB_PREFIX."user.userName ASC";
            $pers = mysqli_query($database, $sql);
            $solvers = [];
            while ($perc = mysqli_fetch_assoc($pers)) {
                $solvers[] = $perc['login'];
            }
            echo implode('; ', $solvers) != "" ? implode('; ', $solvers) : '<em>Případ nemá přiřazené řešitele.</em>'; ?>
			</p>
			<div class="clear">&nbsp;</div>
			<h3>Osoby spojené s případem: </h3>
			<p>
			<?php
            $sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."c2p.iduser
            FROM ".DB_PREFIX."person, ".DB_PREFIX."c2p
            WHERE $sqlFilter AND ".DB_PREFIX."c2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']."
            ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
            $res = mysqli_query($database, $sql);
            if (mysqli_num_rows($res)) {
                $groups = [];
                while ($rec_p = mysqli_fetch_assoc($res)) {
                    $groups[] = '<a href="./readperson.php?rid='.$rec_p['id'].'">'.stripslashes($rec_p['surname']).', '.stripslashes($rec_p['name']).'</a>';
                }
                echo implode(', ', $groups);
            } else {
                echo "<em>K případu nejsou připojeny žádné osoby.</em>";
            } ?>
			</p>
			<div class="clear">&nbsp;</div>
			<h3>Hlášení přiřazená k případu:</h3>
				<?php
            $sqlFilter = DB_PREFIX."report.reportSecret<=".$user['aclSecret'];
            if ($user['aclRoot'] < 1) {
                $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
            }
            $sql = "SELECT ".DB_PREFIX."report.reportId AS 'id', ".DB_PREFIX."report.reportName AS 'label', ".DB_PREFIX."report.reportTask AS 'task', ".DB_PREFIX."report.reportType AS 'type', ".DB_PREFIX."report.reportEventDate AS 'adatum', ".DB_PREFIX."user.userName AS 'user'
            FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."report, ".DB_PREFIX."user
            WHERE $sqlFilter AND ".DB_PREFIX."report.reportId=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.userId=".DB_PREFIX."report.reportOwner
            ORDER BY ".DB_PREFIX."report.reportName ASC";
            $pers = mysqli_query($database, $sql);
            $i = 0;
            while ($perc = mysqli_fetch_assoc($pers)) {
                $i++;
                if ($i == 1) {
                    echo '<ul id="pripady">';
                } ?>
					<li><a href="/reports/<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a> <span class="top">[ <strong><?php echo $perc['type'] == 1 ? 'Výjezd' : ($perc['type'] == 2 ? 'Výslech' : 'Hlášení'); ?></strong> | <strong>Ze dne:</strong> <?php echo $perc['adatum']; ?> | <strong>Vyhotovil:</strong> <?php echo $perc['user']; ?> ]</span> - <?php echo $perc['task']; ?></li>
				<?php
            }
            if ($i != 0) {
                echo "</ul>\n<!-- end of #pripady -->";
            } else {
                echo "<p><em>K případu není přiřazeno žádné hlášení.</em></p>";
            } ?>
				<div class="clear">&nbsp;</div>
				<p>
					<strong>Datum poslední změny:</strong> <?php echo webdate($rec['datum']); ?>
					<strong>Změnil:</strong>
					<?php echo  getAuthor($rec['iduser'], 1); ?>
				</p>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>

	<fieldset><legend><strong>Popis</strong></legend>
		<div class="field-text"><?php echo stripslashes($rec['contents']); ?></div>
	</fieldset>

<!-- následuje seznam přiložených symbolů -->
	<?php //skryti symbolů
        if ($hs != 1) { ?>
	<fieldset><legend><strong>Přiložené symboly</strong></legend>
	<?php //generování seznamu přiložených symbolů
        $sql_s = "SELECT ".DB_PREFIX."symbol2all.idsymbol AS 'id' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."symbol WHERE ".DB_PREFIX."symbol2all.idsymbol = ".DB_PREFIX."symbol.id AND ".DB_PREFIX."symbol.assigned=0 AND ".DB_PREFIX."symbol2all.idrecord=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."symbol.deleted=0";
        $res_s = mysqli_query($database, $sql_s);
        if (mysqli_num_rows($res_s)) {
            $inc = 0; ?>
		<div id="symbols">
		<table>
		<?php
            while ($rec_s = mysqli_fetch_assoc($res_s)) {
                if ($inc == 0 || $inc == 8) {
                    echo '<tr>';
                }
                echo '<td><img src="file/symbol/'.$rec_s['id'].'" alt="symbol chybí" /></td>';
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
    $sqlFilter = DB_PREFIX."file.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."file.mime as mime,  ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id'
    FROM ".DB_PREFIX."file
    WHERE $sqlFilter AND ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=3
    ORDER BY ".DB_PREFIX."file.originalname ASC";
            $res_f = mysqli_query($database, $sql);
            $i = 0;
            while ($rec_f = mysqli_fetch_assoc($res_f)) {
                $i++;
                if ($i == 1) { ?>
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
	<ul id="prilozenadata">
		<?php }
                if (in_array($rec_f['mime'], $config['mime-image'], true)) { ?>
							<li><a href="file/attachement/<?php echo $rec_f['id']; ?>"><img  width="300px" alt="<?php echo stripslashes($rec_f['title']); ?>" src="file/attachement/<?php echo $rec_f['id']; ?>"></a></li>
			<?php		} else { ?>
							<li><a href="file/attachement/<?php echo $rec_f['id']; ?>"><?php echo stripslashes($rec_f['title']); ?></a></li>
			<?php }
            }
            if ($i != 0) {
                echo "</ul>\n<!-- end of #prilozenadata -->\n</fieldset>";
            }
            // konec seznamu přiložených souborů?>

	<?php //skryti poznamek
    if ($hn != 1) { ?>
	<!-- následuje seznam poznámek -->
	<?php // generování poznámek
        $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
        $sql_n = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
        FROM ".DB_PREFIX."note, ".DB_PREFIX."user
        WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=3
        ORDER BY ".DB_PREFIX."note.datum DESC";
        $res_n = mysqli_query($database, $sql_n);
        $i = 0;
            while ($rec_n = mysqli_fetch_assoc($res_n)) {
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
                echo stripslashes($rec_n['title']).' - '.stripslashes($rec_n['user']).' ['.webdate($rec_n['date_created']).']';
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
			<div><?php echo stripslashes($rec_n['note']); ?></div>
			<span class="poznamka-edit-buttons"><?php
                if (($rec_n['iduser'] == $user['userId']) || ($user['aclCase'])) {
                    echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=3" title="upravit"><span class="button-text">upravit</span></a> ';
                }
                if (($rec_n['iduser'] == $user['userId']) || ($user['aclCase'] > 1)) {
                    echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.urlencode($backurl).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".stripslashes($rec_n['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
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
	<?php 	}
    // konec poznámek
        } ?>
</div>
<!-- end of #obsah -->
<?php
        }
    } else {
        echo      $_SESSION['message'] = "Případ neexistuje!";
    }
} else {
    echo $_SESSION['message'] = $text['accessdeniedrecorded'];
}
latteDrawTemplate("footer");
?>
