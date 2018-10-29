<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
		$res=mysqli_query ($database,"SELECT * FROM ".DB_PREFIX."groups WHERE id=".$_REQUEST['rid']);
		if ($rec_g=mysqli_fetch_assoc ($res)) {
                    if (($rec_g['secret']>$usrinfo['right_power']) || $rec_g['deleted']==1) {
                        unauthorizedAccess(2, $rec_g['secret'], $rec_g['deleted'], $_REQUEST['rid']);
                    }
                    auditTrail(2, 1, $_REQUEST['rid']);
                    pageStart ('Úprava skupiny');
                    mainMenu (3);
                    sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>úprava skupiny</strong>');
?>
<div id="obsah">
<fieldset><h1><legend>Úprava skupiny: <?php echo StripSlashes($rec_g['title']); ?></legend></h1>
<form action="procgroup.php" method="post" id="inputform">
	<div id="info"><?php
		if($rec_g['secret']==1){ ?>
	 	<h2>TAJNÉ</h2><?php } ?><?php
		if($rec_g['archived']==1){ ?>
	 	<h2>ARCHIV</h2><?php } ?>	
		<h3><label for="title">Název:</label></h3>
		<input type="text" name="title" id="title" value="<?php echo StripSlashes($rec_g['title']); ?>" />
		
                <div class="clear">&nbsp;</div>
                <h3><label for="archived">Archiv:</label></h3>
			<input type="checkbox" name="archived" value=1 <?php if ($rec_g['archived']==1) { ?>checked="checked"<?php } ?>/><br/>
		<div class="clear">&nbsp;</div>
		                
                <h3><label for="secret">Přísně tajné:</label></h3>
			<input type="checkbox" name="secret" value=1 <?php if ($rec_g['secret']==1) { ?>checked="checked"<?php } ?>/><br/>
		<div class="clear">&nbsp;</div>
<?php if ($usrinfo['right_org'] == 1) {
				echo '					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
				}
?>			
	</div>
	<!-- end of #info -->
	<fieldset><h2><legend>Popis:</legend></h2>
		<textarea cols="80" rows="30" name="contents" id="contents"><?php echo StripSlashes($rec_g['contents']); ?></textarea>
	</fieldset>
	<input type="hidden" name="groupid" value="<?php echo $rec_g['id']; ?>" />
	<input type="submit" name="editgroup" id="submitbutton" value="Uložit změny"  title="Uložit změny"/>
</form>
</fieldset>

	<fieldset><h2><legend>Členové: </legend></h2>
	<form action="addp2g.php" method="post" class="otherform">
		<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton editbutton" title="Upravit členy"/>
	</form>
	<p><?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."g2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
	} else {
		$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."g2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."g2p.idperson AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
	}
	$pers=mysqli_query ($database,$sql);
	$persons=Array();
	while ($perc=mysqli_fetch_assoc ($pers)) {
		$persons[]='<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
	}
	echo ((implode($persons, '; ')<>"")?implode($persons, '; '):'<em>Nejsou připojeny žádné osoby.</em>');
	?></p>
	</fieldset>
	
	<!-- následuje seznam přiložených souborů -->
	<fieldset><h3><legend>Přiložené soubory</legend></h3>
		<strong><em>Ke skupině je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 ORDER BY ".DB_PREFIX."data.originalname ASC";
			} else {
			  $sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
			}
			$res=mysqli_query ($database,$sql);
			$i=0;
			while ($rec_f=mysqli_fetch_assoc ($res)) { 
				$i++; 
				if($i==1){ ?>
		<ul id="prilozenadata">
				<?php } ?>
			<li class="soubor"><a href="getfile.php?idfile=<?php echo($rec_f['id']); ?>" title=""><?php echo(StripSlashes($rec_f['title'])); ?></a><?php if($rec_f['secret']==1){ ?> (TAJNÝ)<?php }; ?><span class="poznamka-edit-buttons"><?php
				if (($rec_f['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" title="smazat" href="procgroup.php?deletefile='.$rec_f['id'].'&amp;groupid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="return confirm(\'Opravdu odebrat soubor &quot;'.StripSlashes($rec_f['title']).'&quot; náležící ke skupině?\')"><span class="button-text">smazat soubor</span></a>'; ?>
				</span></li><?php 
			}
			if($i<>0){ ?>
		</ul>
		<!-- end of #prilozenadata -->
		<?php 
			}else{?><br />
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
			  	<?php if ($rec_g['secret']!=1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_g['secret']==1){ ?>checked="checked"<?php }; ?>/>ano
			</div>
<?php 		if ($usrinfo['right_org'] == 1)	{
			echo '					
			<div>
			<strong><label for="fnotnew">Není nové</label></strong>
			<input type="checkbox" name="fnotnew"/><br/>
			</div>';
			}
?>			
			<div>
				<input type="hidden" name="groupid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editgroup.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor ke skupině" class="submitbutton" title="Uložit"/> 
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-file .otherform-wrap -->
	
	<fieldset><h2><legend>Aktuálně připojené poznámky:</legend></h2>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=2&amp;s=<?php echo $rec_g['secret']; ?>" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		<ul>
		<?php
		if ($usrinfo['right_power']) {
			$sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND ".DB_PREFIX."notes.deleted=0 ORDER BY ".DB_PREFIX."notes.datum DESC";
		} else {
		  $sql_n="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
		}
		$res_n=mysqli_query ($database,$sql_n);
		while ($rec_n=mysqli_fetch_assoc ($res_n)) { ?>
			<li><a href="readnote.php?rid=<?php echo $rec_n['id']; ?>&amp;idtable=2"><?php echo StripSlashes($rec_n['title']); ?></a> - <?php echo StripSlashes($rec_n['user']); 
			if ($rec_n['secret']==0){ ?> (veřejná)<?php }
			if ($rec_n['secret']==1){ ?> (tajná)<?php }
			if ($rec_n['secret']==2){ ?> (soukromá)<?php }
			?><span class="poznamka-edit-buttons"><?php
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo ' <a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=2" title="upravit"><span class="button-text">upravit poznámku</span></a>';
			if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo ' <a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící ke skupině?');".'" title="smazat"><span class="button-text">smazat poznámku</span></a>';
			?></span></li><?php
		}
		?>
		</ul>
	</fieldset>

</div>
<!-- end of #obsah -->
<?php
		} else {
                    pageStart ('Skupina neexistuje');
                    mainMenu (5);
                    sparklets ('<a href="./groups.php">osoby</a> &raquo; <strong>skupina neexistuje</strong>');
                    echo '<div id="obsah"><p>Skupina neexistuje.</p></div>';
		}
	} else {
            pageStart ('Tohle nezkoušejte');
            mainMenu (5);
            sparklets ('<a href="./groups.php">osoby</a> &raquo; <strong>tohle nezkoušejte</strong>');
            echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>