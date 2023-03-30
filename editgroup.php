<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;


latteDrawTemplate("header");

    if (is_numeric($_REQUEST['rid']) && $user['aclGroup']) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."group WHERE id=".$_REQUEST['rid']);
        if ($rec_g = mysqli_fetch_assoc($res)) {
            if (($rec_g['secret'] > $user['aclSecret']) || $rec_g['deleted'] == 1) {
                unauthorizedAccess(2, 1, $_REQUEST['rid']);
            }
            authorizedAccess(2, 1, $_REQUEST['rid']);
            $latteParameters['title'] = 'Úprava skupiny';
            mainMenu();
            sparklets('<a href="./groups/">skupiny</a> &raquo; <strong>úprava skupiny</strong>'); ?>
<div id="obsah">
<fieldset><legend><strong>Úprava skupiny: <?php echo stripslashes($rec_g['title']); ?></strong></legend>
<form action="groups/" method="post" id="inputform">
	<div id="info"><?php
        if ($rec_g['secret'] == 1) { ?>
	 	<h2>TAJNÉ</h2><?php }
            if ($rec_g['archived'] == 1) { ?>
	 	<h2>ARCHIV</h2><?php } ?>
		<h3><label for="title">Název:</label></h3>
		<input type="text" name="title" id="title" value="<?php echo stripslashes($rec_g['title']); ?>" />

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
		<textarea cols="80" rows="30" name="contents" id="contents"><?php echo stripslashes($rec_g['contents']); ?></textarea>
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
        $sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];

            $sql = "SELECT ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname'
        FROM ".DB_PREFIX."g2p, ".DB_PREFIX."person
        WHERE $sqlFilter AND ".DB_PREFIX."person.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."person.surname, ".DB_PREFIX."person.name ASC";

            $pers = mysqli_query($database, $sql);
            $persons = [];
            while ($perc = mysqli_fetch_assoc($pers)) {
                $persons[] = '<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
            }
            echo implode('; ', $persons) != "" ? implode('; ', $persons) : '<em>Nejsou připojeny žádné osoby.</em>'; ?></p>
	</fieldset>

	<!-- následuje seznam přiložených souborů -->
	<fieldset><legend><strong>Přiložené soubory</strong></legend>
		<strong><em>Ke skupině je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
            $sqlFilter = DB_PREFIX."file.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."file.iduser AS 'iduser', ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.secret AS 'secret', ".DB_PREFIX."file.id AS 'id'
                FROM ".DB_PREFIX."file
                WHERE $sqlFilter AND ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=2 ORDER BY ".DB_PREFIX."file.originalname ASC";
            $res = mysqli_query($database, $sql);
            $i = 0;
            while ($rec_f = mysqli_fetch_assoc($res)) {
                $i++;
                if ($i == 1) { ?>
		<ul id="prilozenadata">
				<?php } ?>
			<li class="soubor"><a href="file/attachement/<?php echo $rec_f['id']; ?>" title=""><?php echo stripslashes($rec_f['title']); ?></a><?php if ($rec_f['secret'] == 1) { ?> (TAJNÝ)<?php } ?><span class="poznamka-edit-buttons"><?php
                if (($rec_f['iduser'] == $user['userId']) || ($user['aclGroup']) > 1) {
                    echo '<a class="delete" title="smazat" href="groups/?deletefile='.$rec_f['id'].'&amp;groupid='.$_REQUEST['rid'].'&amp;backurl='.urlencode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.stripslashes($rec_f['title']).'&quot; náležící ke skupině?\')"><span class="button-text">smazat soubor</span></a>';
                } ?>
				</span></li><?php
            }
            if ($i != 0) { ?>
		</ul>
		<!-- end of #prilozenadata -->
		<?php
            } else {?><br />
		<em>bez přiložených souborů</em><?php
            }
            // konec seznamu přiložených souborů?>
	</fieldset>

	<div id="new-file" class="otherform-wrap">
		<fieldset><legend><strong>Nový soubor</strong></legend>
		<form action="groups/" method="post" enctype="multipart/form-data" class="otherform">
			<div>
				<strong><label for="attachment">Soubor:</label></strong>
				<input type="file" name="attachment" id="attachment" />
			</div>
			<div>
				<strong><label for="usecret">Přísně tajné:</label></strong>
			  	<?php if ($rec_g['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php } ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_g['secret'] == 1) { ?>checked="checked"<?php } ?>/>ano
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
		<hr><ul>
		<?php
            $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
            $sql_n = "SELECT ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
            FROM ".DB_PREFIX."note, ".DB_PREFIX."user
            WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=2
            ORDER BY ".DB_PREFIX."note.datum DESC";
            $res_n = mysqli_query($database, $sql_n);
            while ($rec_n = mysqli_fetch_assoc($res_n)) { ?>
			<li><span><a href="readnote.php?rid=<?php echo $rec_n['id']; ?>&amp;idtable=2"><?php echo stripslashes($rec_n['title']); ?></a> - <?php echo stripslashes($rec_n['user']);
            if ($rec_n['secret'] == 0) { ?> (veřejná)<?php }
            if ($rec_n['secret'] == 1) { ?> (tajná)<?php }
            if ($rec_n['secret'] == 2) { ?> (soukromá)<?php }
            ?><span class="poznamka-edit-buttons"><?php
            if (($rec_n['iduser'] == $user['userId']) || ($user['aclGroup'])) {
                echo ' <a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=2" title="upravit"><span class="button-text">upravit poznámku</span></a>';
            }
            if (($rec_n['iduser'] == $user['userId']) || ($user['aclGroup'] > 1)) {
                echo ' <a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.urlencode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".stripslashes($rec_n['title'])."&quot; náležící ke skupině?');".'" title="smazat"><span class="button-text">smazat poznámku</span></a>';
            }
            ?></span></span></li><?php
        } ?>
		</ul>
	</fieldset>

</div>
<!-- end of #obsah -->
<?php
        } else {
            $_SESSION['message'] = "Skupina neexistuje!";
            header('location: index.php');
        }
    } else {
        $_SESSION['message'] = $text['accessdeniedrecorded'];
        header('location: index.php');
    }
    latteDrawTemplate("footer");
?>
