<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");

$latteParameters['title'] = 'Úprava symbolu';

    if (is_numeric($_REQUEST['rid']) && $user['aclSymbol']) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."symbol WHERE id=".$_REQUEST['rid']);
        if ($rec_s = mysqli_fetch_assoc($res)) {
            if ($rec_s['secret'] > $user['aclSecret'] || $rec_s['deleted'] == 1) {
                unauthorizedAccess(7, 1, $_REQUEST['rid']);
            }
            authorizedAccess(7, 1, $_REQUEST['rid']);
            mainMenu();
            sparklets('<a href="./symbols.php">symboly</a> &raquo; <strong>úprava symbolu</strong>'); ?>
<div id="obsah">
<fieldset><legend><strong>Úprava symbolu:</strong></legend>
	<p id="top-text">Symboly nahrávejte pokud možno ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen symboly jasně rozeznatelné, rozmazané fotky použijte třeba jako přílohu. <br />
	Pokud zadáváte hodnoty pro čáry, křivky, body, geometrické tvary, písma a speciální znaky, hodnota nabývá velikosti 0 až 10</p>
	<form action="symbols.php" method="post" id="inputform" enctype="multipart/form-data">
	    	<datalist id=hodnoty>
				<option>0</option>
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
				<option>6</option>
				<option>7</option>
				<option>8</option>
				<option>9</option>
				<option>10</option>
			</datalist>
		<fieldset class="symbol"><legend><strong>Symbol</strong></legend>
		<?php if ($rec_s['symbol'] == null) { ?><img src="#" alt="symbol chybí" title="symbol chybí" id="ssymbolimg" class="noname"/>
		<?php } else { ?><img src="file/symbol/<?php echo $_REQUEST['rid']; ?>" alt="symbol" id="ssymbolimg" />
		<?php } ?>
			<div id="info">
				<h3><label for="symbol">Nový&nbsp;symbol:</label></h3><input type="file" name="symbol" id="symbol" /><br />
				<h3><label for="liner">Čáry:</label></h3><input type="range" value="<?php echo $rec_s['search_lines']; ?>" min="0" max="10" step="1" name="liner" id="liner" list=hodnoty /><br />
				<h3><label for="curver">Křivky:</label></h3><input type="range" value="<?php echo $rec_s['search_curves']; ?>" min="0" max="10" step="1" name="curver" id="curver" list=hodnoty /><br />
				<h3><label for="pointer">Body:</label></h3><input type="range" value="<?php echo $rec_s['search_points']; ?>" min="0" max="10" step="1" name="pointer" id="pointer" list=hodnoty /><br />
				<h3><label for="geometrical">Geom. tvary:</label></h3><input type="range" value="<?php echo $rec_s['search_geometricals']; ?>" min="0" max="10" step="1" name="geometrical" id="geometrical" list=hodnoty /><br />
				<h3><label for="alphabeter">Písma:</label></h3><input type="range" value="<?php echo $rec_s['search_alphabets']; ?>" min="0" max="10" step="1" name="alphabeter" id="alphabeter" list=hodnoty /><br />
				<h3><label for="specialchar">Spec. znaky:</label></h3><input type="range" value="<?php echo $rec_s['search_specialchars']; ?>" min="0" max="10" step="1" name="specialchar" id="specialchar" list=hodnoty /><br />
			<div class="clear">&nbsp;</div>
<?php 			if ($user['aclSymbol'] > 1) {
                echo '
				<h3><label for="archiv">Archiv:</label></h3>
					<input type="checkbox" name="archiv" value=1';
                if ($rec_s['archiv'] == 1) {
                    echo ' checked="checked"';
                }
            }
            echo '/>';
            if ($user['aclGamemaster'] == 1) {
                echo '<br/>
				<div class="clear">&nbsp;</div>
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
            } ?>
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- náseduje popis osoby -->
		<fieldset><legend><strong>Informace k symbolu</strong></legend>
			<div class="field-text">
				<textarea cols="80" rows="15" name="desc" id="desc"><?php echo stripslashes($rec_s['desc']); ?></textarea>
			</div>
			<!-- end of .field-text -->
		</fieldset>
		<input type="hidden" name="symbolid" value="<?php echo $rec_s['id']; ?>" />
		<input type="submit" name="editsymbol" id="submitbutton" value="Uložit" title="Uložit změny"/>
	</form>

</fieldset>

	<fieldset><legend><strong>Výskyt v případech</strong></legend>
		<!-- tady dochází ke stylové nesystematičnosti, nejedná se o poznámku; pro nápravu je třeba projít všechny šablony -->
		<p><span class="poznamka-edit-buttons"><a class="connect" href="addsy2c.php?rid=<?php echo $_REQUEST['rid']; ?>" title="přiřazení"><span class="button-text">přiřazení případů</span></a><em style="font-size:smaller;"> (přiřazování)</em></span></p>
		<!-- následuje seznam případů -->
		<?php // generování seznamu přiřazených případů
            $sqlFilter = DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."case.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title'
                FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."case
                WHERE $sqlFilter AND ".DB_PREFIX."case.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3
                ORDER BY ".DB_PREFIX."case.title ASC";
            $pers = mysqli_query($database, $sql);

            $i = 0;
            while ($perc = mysqli_fetch_assoc($pers)) {
                $i++;
                if ($i == 1) { ?>
		<ul id=""><?php
                } ?>
			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		<?php
            }
            if ($i != 0) { ?>
		</ul>
		<!-- end of # -->
		<?php
            } else {?><br />
		<em>Symbol nebyl přiřazen žádnému případu.</em><?php
            }
            // konec seznamu přiřazených případů?>
	</fieldset>


	<fieldset><legend><strong>Výskyt v hlášení</strong></legend>
		<!-- tady dochází ke stylové nesystematičnosti, nejedná se o poznámku; pro nápravu je třeba projít všechny šablony -->
		<p><span class="poznamka-edit-buttons"><a class="connect" href="addsy2ar.php?rid=<?php echo $_REQUEST['rid']; ?>" title="přiřazení"><span class="button-text">přiřazení hlášení</span></a><em style="font-size:smaller;"> (přiřazování)</em></span></p>
		<!-- následuje seznam případů -->
		<?php // generování seznamu přiřazených hlášení
            $sqlFilter = DB_PREFIX."report.reportSecret<=".$user['aclSecret'];
            if ($user['aclRoot'] < 1) {
                $sqlFilter .= ' AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1)) ';
            }


            $sql = "SELECT ".DB_PREFIX."report.reportId AS 'id', ".DB_PREFIX."report.reportName AS 'label'
            FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."report
            WHERE $sqlFilter AND ".DB_PREFIX."report.reportId=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4
            ORDER BY ".DB_PREFIX."report.reportName ASC";
            $pers = mysqli_query($database, $sql);

            $i = 0;
            while ($perc = mysqli_fetch_assoc($pers)) {
                $i++;
                if ($i == 1) { ?>
		<ul id=""><?php
                } ?>
			<li><a href="/reports/<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a></li>
		<?php
            }
            if ($i != 0) { ?>
		</ul>
		<!-- end of # -->
		<?php
            } else {?><br />
		<em>Symbol nebyl přiřazen žádnému hlášení.</em><?php
            }
            // konec seznamu přiřazených hlášení?>
	</fieldset>


	<fieldset><legend><strong>Poznámky</strong></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=9" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K symbolu si můžete připsat kolik chcete poznámek.)</em></span>
		<!-- následuje seznam poznámek -->
		<?php // generování poznámek
                      $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
            $sql = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
                FROM ".DB_PREFIX."note, ".DB_PREFIX."user
                WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=7
                ORDER BY ".DB_PREFIX."note.datum DESC";
            $res = mysqli_query($database, $sql);
            $i = 0;
            while ($rec_n = mysqli_fetch_assoc($res)) {
                $i++;
                if ($i == 1) { ?>
		<div id="poznamky"><?php
                }
                if ($i > 1) {?>
			<hr /><?php
                } ?>
			<div class="poznamka">
				<h4><?php echo stripslashes($rec_n['title']).' - '.stripslashes($rec_n['user']);
                if ($rec_n['secret'] == 0) {
                    echo ' (veřejná)';
                }
                if ($rec_n['secret'] == 1) {
                    echo ' (tajná)';
                }
                if ($rec_n['secret'] == 2) {
                    echo ' (soukromá)';
                } ?></h4>
				<div><?php echo stripslashes($rec_n['note']); ?></div>
				<span class="poznamka-edit-buttons"><?php
                if (($rec_n['iduser'] == $user['userId']) || ($user['aclSymbol']>0)) {
                    echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=7" title="upravit"><span class="button-text">upravit</span></a> ';
                }
                if (($rec_n['iduser'] == $user['userId']) || ($user['aclSymbol'] > 1)) {
                    echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.urlencode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".stripslashes($rec_n['title'])."&quot; náležící k symbolu?');".'" title="smazat"><span class="button-text">smazat</span></a>';
                } ?>
				</span>
			</div>
			<!-- end of .poznamka -->
		<?php
            }
            if ($i != 0) { ?>
		</div>
		<!-- end of #poznamky -->
		<?php
            } else {?><br />
		<em>bez poznámek</em><?php
            }
            // konec poznámek?>
	</fieldset>
</div>
<!-- end of #obsah -->
<?php
        } else {
            echo '<div id="obsah"><p>Symbol neexistuje.</p></div>';
        }
    } else {
        echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
    }
    latteDrawTemplate("footer");
?>
