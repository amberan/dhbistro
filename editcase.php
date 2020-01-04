<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);

$latteParameters['title'] = 'Úprava případu';
$latte->render($config['folder_templates'].'header.latte', $latteParameters);
		
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	    $sql_a = "SELECT * FROM ".DB_PREFIX."c2s WHERE ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid'];
	    $res_a = mysqli_query ($database,$sql_a);
	    $rec_a = mysqli_fetch_array ($res_a);
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
	    $rec = mysqli_fetch_assoc ($res);


	    if (($usrinfo['right_text']) && (($rec['secret'] == 0) || ($usrinfo['right_power']) || ($rec_a['iduser']))) {
	        $symbolbutton = ' <a href="symbols.php">přiřadit symboly</a>';
	    } else {
	        $symbolbutton = '';
	    }
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
	    if ($rec_c = mysqli_fetch_assoc ($res)) {
	        if (($rec['secret'] > $usrinfo['right_power']) || $rec['deleted'] == 1) {
	            unauthorizedAccess(3, $rec_c['secret'], $rec_c['deleted'], $_REQUEST['rid']);
	        }
	        auditTrail(3, 1, $_REQUEST['rid']);
	        mainMenu ();
	        sparklets ('<a href="./cases.php">případy</a> &raquo; <strong>úprava případu</strong>',$symbolbutton); ?>
<?php if (($rec['secret'] == 1) && (!$usrinfo['right_power']) && (!$rec_a['iduser'])) {
	            echo '<div id="obsah"><p>Hezký pokus.</p></div>';
	        } else {
	            ?>
<div id="obsah">
	<fieldset id="ramecek"><legend><strong>Úprava případu: <?php echo(StripSlashes($rec_c['title'])); ?></strong></legend>
		<form action="proccase.php" method="post" id="inputform">
			<div id="info">
				<h3><label for="title">Název:</label></h3>
		  		<input type="text" name="title" id="title" value="<?php echo StripSlashes($rec_c['title']); ?>" />
				<div class="clear">&nbsp;</div>
				
				<h3><label for="secret">Přísně&nbsp;tajné:</label></h3>
				<input type="radio" name="secret" value="0" <?php if ($rec_c['secret'] == 0) { ?>checked="checked"<?php } ?>/>ne<br/>
				<h3><label>&nbsp;</label></h3><input type="radio" name="secret" value="1"<?php if ($rec_c['secret'] == 1) { ?>checked="checked"<?php } ?>>ano
				<div class="clear">&nbsp;</div>
	
				<h3><label for="status">Stav:</label></h3>
				<select name="status" id="status">
					<option value="0"<?php if ($rec_c['status'] == 0) {
	                echo ' selected="selected"';
	            } ?>>otevřený</option>
					<option value="1"<?php if ($rec_c['status'] == 1) {
	                echo ' selected="selected"';
	            } ?>>uzavřený</option>
				</select>
				<div class="clear">&nbsp;</div>
<?php 			if ($usrinfo['right_org'] == 1) {
	                echo '					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
	            } ?>				
			</div>
			<!-- end of #info -->
			<fieldset><legend><strong>Obsah:</strong></legend>
				<textarea cols="80" rows="30" name="contents" id="contents"><?php echo StripSlashes($rec_c['contents']); ?></textarea>
			</fieldset>
			<div>
			  <input type="hidden" name="caseid" value="<?php echo $rec_c['id']; ?>" />
			  <input type="submit" name="editcase" id="submitbutton" value="Uložit změny" title="Uložit změny" />
			</div>
		</form>
	</fieldset>
	
	<fieldset><legend><strong>Řešitelé: </strong></legend>
		<form action="adds2c.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Upravit řešitele" name="setsolver" class="submitbutton editbutton" title="Upravit řešitele" />
		</form>
		<p><?php
			$sql = "SELECT ".DB_PREFIX."user.id AS 'id', ".DB_PREFIX."user.login AS 'login' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."user WHERE ".DB_PREFIX."user.id=".DB_PREFIX."c2s.idsolver AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.deleted=0 ORDER BY ".DB_PREFIX."user.login ASC";
	            $pers = mysqli_query ($database,$sql);
	            $solvers = Array();
	            while ($perc = mysqli_fetch_assoc ($pers)) {
	                $solvers[] = $perc['login'];
	            }
	            echo ((implode($solvers, '; ') <> "") ? implode($solvers, '; ') : '<em>Případ nemá přiřazené řešitele.</em>'); ?></p>		
	</fieldset>

	<fieldset><legend><strong>Osoby přiřazené k případu: </strong></legend>

		<form action="addp2c.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton editbutton" title="Upravit osoby přiřazené" />
		</form>
		<p><?php
			if ($usrinfo['right_power']) {
			    $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname' FROM ".DB_PREFIX."c2p, ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id=".DB_PREFIX."c2p.idperson AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname' FROM ".DB_PREFIX."c2p, ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id=".DB_PREFIX."c2p.idperson AND ".DB_PREFIX."c2p.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
			}
	            $pers = mysqli_query ($database,$sql);
	            $persons = Array();
	            while ($perc = mysqli_fetch_assoc ($pers)) {
	                $persons[] = '<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
	            }
	            echo ((implode($persons, '; ') <> "") ? implode($persons, '; ') : '<em>Nejsou připojeny žádné osoby.</em>'); ?></p>		
	</fieldset>
	
	
	<fieldset><legend><strong>Hlášení přiřazená k případu: </strong></legend>
		<form action="addc2ar.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Změnit přiřazení hlášení" name="setreport" class="submitbutton editbutton" title="Změnit přiřazení hlášení" />
		</form>
		<ul>
		<?php
		if ($usrinfo['right_power']) {
		    $sql = "SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.task AS 'task', ".DB_PREFIX."user.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."report, ".DB_PREFIX."user WHERE ".DB_PREFIX."report.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.id=".DB_PREFIX."report.iduser ORDER BY ".DB_PREFIX."report.label ASC";
		} else {
		    $sql = "SELECT ".DB_PREFIX."report.id AS 'id', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.task AS 'task', ".DB_PREFIX."user.login AS 'user' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."report, ".DB_PREFIX."user WHERE ".DB_PREFIX."report.id=".DB_PREFIX."ar2c.idreport AND ".DB_PREFIX."ar2c.idcase=".$_REQUEST['rid']." AND ".DB_PREFIX."user.id=".DB_PREFIX."report.iduser AND ".DB_PREFIX."report.secret=0 ORDER BY ".DB_PREFIX."report.label ASC";
		}
	            $pers = mysqli_query ($database,$sql);
	            $reports = Array();
	            while ($perc = mysqli_fetch_assoc ($pers)) {
	                $reports[] = '<li><a href="readactrep.php?rid='.$perc['id'].'">'.$perc['label'].'</a> - '.$perc['task'].' - <b>'.$perc['user'].'</b>';
	            }
	            echo ((implode($reports, '; ') <> "") ? implode($reports, '; ') : '<em>Nejsou připojena žádná hlášení.</em>'); ?>
		</ul>
	</fieldset>

	<!-- následuje seznam přiložených souborů -->
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
		<strong><em>K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
			if ($usrinfo['right_power']) {
			    $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=3 ORDER BY ".DB_PREFIX."file.originalname ASC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=3 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
			}
	            $res = mysqli_query ($database,$sql);
	            $i = 0;
	            while ($rec_f = mysqli_fetch_assoc ($res)) {
	                $i++;
	                if ($i == 1) { ?>
		<ul id="prilozenadata">
				<?php } ?>
			<li class="soubor"><a href="getfile.php?idfile=<?php echo($rec_f['id']); ?>" title=""><?php echo(StripSlashes($rec_f['title'])); ?></a><?php if ($rec_f['secret'] == 1) { ?> (TAJNÝ)<?php }; ?><span class="poznamka-edit-buttons"><?php
				if (($rec_f['iduser'] == $usrinfo['id']) || ($usrinfo['right_power'])) {
				    echo '<a class="delete" title="smazat" href="proccase.php?deletefile='.$rec_f['id'].'&amp;caseid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editcase.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.StripSlashes($rec_f['title']).'&quot; náležící k případu?\')"><span class="button-text">smazat soubor</span></a>';
				} ?>
				</span><br><br></li><?php
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

	<div id="new-file" class="otherform-wrap">
		<fieldset><legend><strong>Nový soubor</strong></legend>
		<form action="proccase.php" method="post" enctype="multipart/form-data" class="otherform">
			<div>
				<strong><label for="attachment">Soubor:</label></strong>
				<input type="file" name="attachment" id="attachment" />
			</div>
			<div>
				<strong><label for="usecret">Přísně tajné:</label></strong>
			  	<?php if ($rec_c['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_c['secret'] == 1) { ?>checked="checked"<?php }; ?>/>ano
			</div>
<?php 			if ($usrinfo['right_org'] == 1) {
	                echo '					
				<div>
				<strong><label for="fnotnew">Není nové</label></strong>
					<input type="checkbox" name="fnotnew"/><br/>
				</div>';
	            } ?>			
			<div>
				<input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editcase.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor k osobě" class="submitbutton" title="Uložit"/> 
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-file .otherform-wrap -->
	
	<fieldset><legend><strong>Aktuálně připojené poznámky:</strong></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=3" title="nová poznámka">
		<span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		<br><br>
		<ul>
		<?php
		if ($usrinfo['right_power']) {
		    $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=3 AND ".DB_PREFIX."note.deleted=0 ORDER BY ".DB_PREFIX."note.datum DESC";
		} else {
		    $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.login AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.id AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=3 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		}
	            $res_n = mysqli_query ($database,$sql_n);
	            while ($rec_n = mysqli_fetch_assoc ($res_n)) { ?>
			<li class="Clear"><a href="readnote.php?rid=<?php echo $rec_n['id']; ?>&amp;idtable=3"><?php echo StripSlashes($rec_n['title']); ?></a> - <?php echo StripSlashes($rec_n['user']);
			if ($rec_n['secret'] == 0) { ?> (veřejná)<?php }
			if ($rec_n['secret'] == 1) { ?> (tajná)<?php }
			if ($rec_n['secret'] == 2) { ?> (soukromá)<?php }
			?><span class="poznamka-edit-buttons"><?php
			if (($rec_n['iduser'] == $usrinfo['id']) || ($usrinfo['right_text'])) {
			    echo ' <a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=3" title="upravit"><span class="button-text">upravit poznámku</span></a>';
			}
			if (($rec_n['iduser'] == $usrinfo['id']) || ($usrinfo['right_power'])) {
			    echo ' <a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k hlášení?');".'" title="smazat"><span class="button-text">smazat poznámku</span></a>';
			}
			?></span></li><?php
		} ?>
		</ul>
	</fieldset>
</div>
<!-- end of #obsah -->
<?php
	        }
	    } else {
	        $_SESSION['message'] = "Osoba neexistuje!";
	        Header ('location: index.php');
	    }
	} else {
	    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	    Header ('location: index.php');
	}
	$latte->render($config['folder_templates'].'footer.latte', $latteParameters);
?>