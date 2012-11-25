<?php
	require_once ('./inc/func_main.php');
	pageStart ('Úprava symbolu');
	mainMenu (5);
	sparklets ('<a href="./symbols.php">symboly</a> &raquo; <strong>úprava symbolu</strong>');
	if (is_numeric($_REQUEST['rid']) && $usrinfo['right_text']) {
	  $res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."symbols WHERE id=".$_REQUEST['rid']);
		if ($rec_p=MySQL_Fetch_Assoc($res)) {
?>
<div id="obsah">
<fieldset><legend><h1>Úprava symbolu:</h1></legend>
	<p id="top-text">Symboly nahrávejte pokud možno ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen symboly jasně rozeznatelné, rozmazané fotky použijte třeba jako přílohu.</p>
	<form action="procother.php" method="post" id="inputform" enctype="multipart/form-data">
		<fieldset class="symbol"><legend><h2>Symbol</h2></legend>
		<?php if($rec_p['symbol']==NULL){ ?><img src="#" alt="symbol chybí" title="symbol chybí" id="ssymbolimg" class="noname"/>
		<?php }else{ ?><img src="getportrait.php?nrid=<?php echo($_REQUEST['rid']); ?>" alt="symbol" id="ssymbolimg" />
		<?php } ?>
			<div id="info">
				<div class="clear">&nbsp;</div>
				<h3><label for="symbol">Nový&nbsp;symbol:</label></h3>
				<input type="file" name="symbol" id="symbol" />
				<div class="clear">&nbsp;</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '
				<h3><label for="archiv">Archiv:</label></h3>
					<input type="checkbox" name="archiv" value=1';
				if ($rec_p['archiv']==1) { 
					echo ' checked="checked"';
				}
				echo '/><br/>
				<div class="clear">&nbsp;</div>					
				<h3><label for="notnew">Není nové</label></h3>
					<input type="checkbox" name="notnew"/><br/>
				<div class="clear">&nbsp;</div>';
				}
?>				
			</div>
			<!-- end of #info -->
		</fieldset>
		<!-- náseduje popis osoby -->
		<fieldset><legend><h2>Popis osoby</h2></legend>
			<div class="field-text">
				<textarea cols="80" rows="15" name="desc" id="desc"><?php echo StripSlashes($rec_p['desc']); ?></textarea>
			</div>
			<!-- end of .field-text -->
		</fieldset>
		<input type="hidden" name="symbolid" value="<?php echo $rec_p['id']; ?>" />
		<input type="submit" name="editsymbol" id="submitbutton" value="Uložit" title="Uložit změny"/>
	</form>

</fieldset>

	<fieldset><legend><h3>Výskyt v případech</h3></legend>
		<!-- tady dochází ke stylové nesystematičnosti, nejedná se o poznámku; pro nápravu je třeba projít všechny šablony -->
		<p><span class="poznamka-edit-buttons"><a class="connect" href="addsy2c.php?rid=<?php echo $_REQUEST['rid']; ?>" title="přiřazení"><span class="button-text">přiřazení případů</span></a><em style="font-size:smaller;"> (přiřazování)</em></span></p>
		<!-- následuje seznam případů -->
		<?php // generování seznamu přiřazených případů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3 ORDER BY ".DB_PREFIX."cases.title ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=3 AND ".DB_PREFIX."cases.secret=0 ORDER BY ".DB_PREFIX."cases.title ASC";
			}
			$pers=MySQL_Query ($sql);
			
			$i=0;
			while ($perc=MySQL_Fetch_Assoc($pers)) { 
				$i++;
				if($i==1){ ?>
		<ul id=""><?php
				}
				 ?>
			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		<?php }
			if($i<>0){ ?>
		</ul>
		<!-- end of # -->
		<?php 
			}else{?><br />
		<em>Symbol nebyl přiřazen žádnému případu.</em><?php
			}
		// konec seznamu přiřazených případů ?>
	</fieldset>

	
	<fieldset><legend><h3>Výskyt v hlášení</h3></legend>
		<!-- tady dochází ke stylové nesystematičnosti, nejedná se o poznámku; pro nápravu je třeba projít všechny šablony -->
		<p><span class="poznamka-edit-buttons"><a class="connect" href="addsy2ar.php?rid=<?php echo $_REQUEST['rid']; ?>" title="přiřazení"><span class="button-text">přiřazení hlášení</span></a><em style="font-size:smaller;"> (přiřazování)</em></span></p>
		<!-- následuje seznam případů -->
		<?php // generování seznamu přiřazených hlášení
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4 ORDER BY ".DB_PREFIX."reports.label ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."reports.id AS 'id', ".DB_PREFIX."reports.label AS 'label' FROM ".DB_PREFIX."symbol2all, ".DB_PREFIX."reports WHERE ".DB_PREFIX."reports.id=".DB_PREFIX."symbol2all.idrecord AND ".DB_PREFIX."symbol2all.idsymbol=".$_REQUEST['rid']." AND ".DB_PREFIX."symbol2all.table=4 AND ".DB_PREFIX."reports.secret=0 ORDER BY ".DB_PREFIX."reports.label ASC";
			}
			$pers=MySQL_Query ($sql);
			
			$i=0;
			while ($perc=MySQL_Fetch_Assoc($pers)) { 
				$i++;
				if($i==1){ ?>
		<ul id=""><?php
				}
				 ?>
			<li><a href="readactrep.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['label']; ?></a></li>
		<?php }
			if($i<>0){ ?>
		</ul>
		<!-- end of # -->
		<?php 
			}else{?><br />
		<em>Symbol nebyl přiřazen žádnému hlášení.</em><?php
			}
		// konec seznamu přiřazených hlášení ?>
	</fieldset>	
	
	
	<fieldset><legend><h3>Poznámky</h3></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=9" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span>
		<!-- následuje seznam poznámek -->
		<?php // generování poznámek
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=7 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
			} else {
				$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=7 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
			}
			$res=MySQL_Query ($sql);
			$i=0;
			while ($rec_n=MySQL_Fetch_Assoc($res)) { 
				$i++;
				if($i==1){ ?>
		<div id="poznamky"><?php
				}
				if($i>1){?>
			<hr /><?php
				} ?>
			<div class="poznamka">
				<h4><?php echo(StripSlashes($rec_n['title'])).' - '.(StripSlashes($rec_n['user']));?><?php
				if ($rec_n['secret']==0) echo ' (veřejná)';
				if ($rec_n['secret']==1) echo ' (tajná)';
				if ($rec_n['secret']==2) echo ' (soukromá)';
				?></h4>
				<div><?php echo(StripSlashes($rec_n['note'])); ?></div>
				<span class="poznamka-edit-buttons"><?php
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=7" title="upravit"><span class="button-text">upravit</span></a> ';
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k symbolu?');".'" title="smazat"><span class="button-text">smazat</span></a>'; ?>
				</span>
			</div>
			<!-- end of .poznamka -->
		<?php }
			if($i<>0){ ?>
		</div>
		<!-- end of #poznamky -->
		<?php 
			}else{?><br />
		<em>bez poznámek</em><?php
			}
		// konec poznámek ?>
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
	pageEnd ();
?>