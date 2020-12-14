<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
latteDrawTemplate("header");

	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	    $res = mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."group WHERE id=".$_REQUEST['rid']);
	    if ($rec_g = mysqli_fetch_assoc ($res)) {
	        if (($rec_g['secret'] > $user['aclDirector']) || $rec_g['deleted'] == 1) {
	            unauthorizedAccess(2, $rec_g['secret'], $rec_g['deleted'], $_REQUEST['rid']);
	        }
	        auditTrail(2, 1, $_REQUEST['rid']);
	        $latteParameters['title'] = 'Úprava skupiny';
	        mainMenu ();
	        sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>úprava skupiny</strong>'); ?>
<div id="obsah">
<fieldset><legend><strong>Úprava skupiny: <?php echo StripSlashes($rec_g['title']); ?></strong></legend>
<form action="procgroup.php" method="post" id="inputform">
	<div id="info"><?php
		if ($rec_g['secret'] == 1) { ?>
	 	<h2>TAJNÉ</h2><?php }
	        if ($rec_g['archived'] == 1) { ?>
	 	<h2>ARCHIV</h2><?php } ?>	
		<h3><label for="title">Název:</label></h3>
		<input type="text" name="title" id="title" value="<?php echo StripSlashes($rec_g['title']); ?>" />
		
                <div class="clear">&nbsp;</div>
                <h3><label for="archived">Archiv:</label></h3>
			<input type="checkbox" name="archived" value=1 <?php if ($rec_g['archived'] == 1) { ?>checked="checked"<?php } ?>/><br/>
		<div class="clear">&nbsp;</div>
		                
                <h3><label for="secret">Přísně tajné:</label></h3>
			<input type="checkbox" name="secret" value=1 <?php if ($rec_g['secret'] == 1) { ?>checked="checked"<?php } ?>/><br/>
		<div class="clear">&nbsp;</div>
<?php if ($user['aclGamemaster'] == 1) {
	            echo '					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
	        } ?>			
	</div>
	<!-- end of #info -->
	<fieldset><legend><strong>Popis:</strong></legend>
		<textarea cols="80" rows="30" name="contents" id="contents"><?php echo StripSlashes($rec_g['contents']); ?></textarea>
	</fieldset>
	<input type="hidden" name="groupid" value="<?php echo $rec_g['id']; ?>" />
	<input type="submit" name="editgroup" id="submitbutton" value="Uložit změny"  title="Uložit změny"/>
</form>
</fieldset>

	<fieldset><legend><strong>Členové: </strong></legend>
	<form action="addp2g.php" method="post" class="otherform">
		<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton editbutton" title="Upravit členy"/>
	</form>
	<p><?php
	if ($user['aclDirector']) {
	    $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname' FROM ".DB_PREFIX."g2p, ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
	} else {
	    $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname' FROM ".DB_PREFIX."g2p, ".DB_PREFIX."person WHERE ".DB_PREFIX."person.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." AND ".DB_PREFIX."person.secret=0 ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";
	}
	        $pers = mysqli_query ($database,$sql);
	        $persons = Array();
	        while ($perc = mysqli_fetch_assoc ($pers)) {
	            $persons[] = '<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
	        }
	        echo ((implode($persons, '; ') <> "") ? implode($persons, '; ') : '<em>Nejsou připojeny žádné osoby.</em>'); ?></p>
	</fieldset>
	
	<!-- následuje seznam přiložených souborů -->
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
		<strong><em>Ke skupině je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
			if ($user['aclDirector']) {
			    $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=2 ORDER BY ".DB_PREFIX."file.originalname ASC";
			} else {
			    $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id' FROM ".DB_PREFIX."file WHERE ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=2 AND ".DB_PREFIX."file.secret=0 ORDER BY ".DB_PREFIX."file.originalname ASC";
			}
	        $res = mysqli_query ($database,$sql);
	        $i = 0;
	        while ($rec_f = mysqli_fetch_assoc ($res)) {
	            $i++;
	            if ($i == 1) { ?>
		<ul id="prilozenadata">
				<?php } ?>
			<li class="soubor"><a href="getfile.php?idfile=<?php echo $rec_f['id']; ?>" title=""><?php echo StripSlashes($rec_f['title']); ?></a><?php if ($rec_f['secret'] == 1) { ?> (TAJNÝ)<?php }; ?><span class="poznamka-edit-buttons"><?php
				if (($rec_f['iduser'] == $user['userId']) || ($user['aclDirector'])) {
				    echo '<a class="delete" title="smazat" href="procgroup.php?deletefile='.$rec_f['id'].'&amp;groupid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.StripSlashes($rec_f['title']).'&quot; náležící ke skupině?\')"><span class="button-text">smazat soubor</span></a>';
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

	<div id="new-file" class="otherform-wrap">
		<fieldset><legend><strong>Nový soubor</strong></legend>
		<form action="procgroup.php" method="post" enctype="multipart/form-data" class="otherform">
			<div>
				<strong><label for="attachment">Soubor:</label></strong>
				<input type="file" name="attachment" id="attachment" />
			</div>
			<div>
				<strong><label for="usecret">Přísně tajné:</label></strong>
			  	<?php if ($rec_g['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_g['secret'] == 1) { ?>checked="checked"<?php }; ?>/>ano
			</div>
<?php 		if ($user['aclGamemaster'] == 1) {
	            echo '					
			<div>
			<strong><label for="fnotnew">Není nové</label></strong>
			<input type="checkbox" name="fnotnew"/><br/>
			</div>';
	        } ?>			
			<div>
				<input type="hidden" name="groupid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editgroup.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor ke skupině" class="submitbutton" title="Uložit"/> 
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-file .otherform-wrap -->
	
	<fieldset><legend><strong>Aktuálně připojené poznámky:</strong></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=2&amp;s=<?php echo $rec_g['secret']; ?>" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		<ul>
		<?php
		if ($user['aclDirector']) {
		    $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=2 AND ".DB_PREFIX."note.deleted=0 ORDER BY ".DB_PREFIX."note.datum DESC";
		} else {
		    $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id' FROM ".DB_PREFIX."note, ".DB_PREFIX."user WHERE ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=2 AND ".DB_PREFIX."note.deleted=0 AND (".DB_PREFIX."note.secret=0 OR ".DB_PREFIX."note.iduser=".$user['userId'].") ORDER BY ".DB_PREFIX."note.datum DESC";
		}
	        $res_n = mysqli_query ($database,$sql_n);
	        while ($rec_n = mysqli_fetch_assoc ($res_n)) { ?>
			<li><a href="readnote.php?rid=<?php echo $rec_n['id']; ?>&amp;idtable=2"><?php echo StripSlashes($rec_n['title']); ?></a> - <?php echo StripSlashes($rec_n['user']);
			if ($rec_n['secret'] == 0) { ?> (veřejná)<?php }
			if ($rec_n['secret'] == 1) { ?> (tajná)<?php }
			if ($rec_n['secret'] == 2) { ?> (soukromá)<?php }
			?><span class="poznamka-edit-buttons"><?php
			if (($rec_n['iduser'] == $user['userId']) || ($usrinfo['right_text'])) {
			    echo ' <a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=2" title="upravit"><span class="button-text">upravit poznámku</span></a>';
			}
			if (($rec_n['iduser'] == $user['userId']) || ($user['aclDirector'])) {
			    echo ' <a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící ke skupině?');".'" title="smazat"><span class="button-text">smazat poznámku</span></a>';
			}
			?></span></li><?php
		} ?>
		</ul>
	</fieldset>

</div>
<!-- end of #obsah -->
<?php
	    } else {
	        $_SESSION['message'] = "Skupina neexistuje!";
	        Header ('location: index.php');
	    }
	} else {
	    $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
	    Header ('location: index.php');
	}
	latteDrawTemplate("footer");
?>
