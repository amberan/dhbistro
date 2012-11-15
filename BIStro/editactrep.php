<?php
require_once ('./inc/func_main.php');
$reportarray=MySQL_Fetch_Assoc(MySQL_Query("SELECT * FROM ".DB_PREFIX."reports WHERE id=".$_REQUEST['rid'])); // načte data z DB
$type=intval($reportarray['type']); // určuje typ hlášení
	$typestring=(($type==1)?'výjezd':(($type==2)?'výslech':'?')); //odvozuje slovní typ hlášení
$author=$reportarray['iduser']; // určuje autora hlášení

// následuje generování hlavičky
pageStart ('Úprava hlášení'.(($type==1)?' z výjezdu':(($type==2)?' z výslechu':'')));
mainMenu (3);
sparklets ('<a href="./reports.php">hlášení</a> &raquo; <strong>úprava hlášení'.(($type==1)?' z výjezdu':(($type==2)?' z výslechu':'')).'</strong>');

// kalendář
function date_picker($name, $startyear=NULL, $endyear=NULL) {
	global $aday;
	global $amonth;
	global $ayear;
	if($startyear==NULL) $startyear = date("Y")-10;
	if($endyear==NULL) $endyear=date("Y")+5;

	$months=array('','Leden','Únor','Březen','Duben','Květen',
			'Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec');

	// roletka dnů
	$html="<select class=\"day\" name=\"".$name."day\">";
	for($i=1;$i<=31;$i++)
	{
		$html.="<option ".(($i==$aday)?' selected':'')." value='$i'>$i</option>";
	}
	$html.="</select> ";

	// roletka měsíců
	$html.="<select class=\"month\" name=\"".$name."month\">";

	for($i=1;$i<=12;$i++)
	{
		$html.="<option ".(($i==$amonth)?' selected':'')." value='$i'>$months[$i]</option>";
	}
	$html.="</select> ";

	// roletka let
	$html.="<select class=\"year\" name=\"".$name."year\">";

	for($i=$startyear;$i<=$endyear;$i++)
	{
		$html.="<option ".(($i==$ayear)?' selected':'')." value='$i'>$i</option>";
	}
	$html.="</select> ";

		return $html;
}

if (is_numeric($_REQUEST['rid']) && ($usrinfo['right_text'] || ($usrinfo['id']==$author && $reportarray['status']<1))) {
	$sql="SELECT
		".DB_PREFIX."reports.id AS 'id',
		".DB_PREFIX."reports.datum AS 'datum',
		".DB_PREFIX."reports.label AS 'label',
		".DB_PREFIX."reports.task AS 'task',
		".DB_PREFIX."reports.summary AS 'summary',
		".DB_PREFIX."reports.impacts AS 'impacts',
		".DB_PREFIX."reports.details AS 'details',
		".DB_PREFIX."reports.secret AS 'secret',
		".DB_PREFIX."reports.status AS 'status',
		".DB_PREFIX."users.login AS 'autor',
		".DB_PREFIX."reports.type AS 'type',
		".DB_PREFIX."reports.adatum AS 'adatum',
		".DB_PREFIX."reports.start AS 'start',
		".DB_PREFIX."reports.end AS 'end',
		".DB_PREFIX."reports.energy AS 'energy',
		".DB_PREFIX."reports.inputs AS 'inputs'
		FROM ".DB_PREFIX."reports, ".DB_PREFIX."users
		WHERE ".DB_PREFIX."reports.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."reports.id=".$_REQUEST['rid'];
	$res=MySQL_Query ($sql);
	if ($rec_actr=MySQL_Fetch_Assoc($res)) {
	$aday=(Date ('j',$rec_actr['adatum']));
	$amonth=(Date ('n',$rec_actr['adatum']));
	$ayear=(Date ('Y',$rec_actr['adatum']));
	?>
<div id="obsah">
<form action="procactrep.php" method="post" id="inputform">
<fieldset><legend><h1>Úprava hlášení<?php echo (($type==1)?' z výjezdu':(($type==2)?' z výslechu':''));?></h1></legend>
	<fieldset><legend><h2>Základní údaje</h2></legend>
		<div id="info">
			<h3><label for="label">Označení&nbsp;<?php echo (($type==1)?'výjezdu':(($type==2)?'výslechu':'hlášení'));?>:</label></h3>
			<input type="text" size="80" name="label" id="label" value="<?php echo StripSlashes($rec_actr['label']); ?>" />
			<div class="clear">&nbsp;</div>
			<h3><label for="task"><?php echo((($type==1)?'Úkol':(($type==2)?'Předmět&nbsp;výslechu':'Úkol')));?>:</label></h3>
			<input type="text" size="80" name="task" id="task" value="<?php echo StripSlashes($rec_actr['task']); ?>" />
			<div class="clear">&nbsp;</div>
			<h3><label for="adatum"><?php if($type=='1'){ ?>Datum&nbsp;akce<?php }else if($type=='2'){ ?>Datum&nbsp;výslechu<?php }; ?>:</label></h3>
			<?php echo date_picker("adatum")?>
			<div class="clear">&nbsp;</div>
			<h3><label for="start">Začátek:</label></h3>
			<input type="start" name="start" id="start" value="<?php echo StripSlashes($rec_actr['start']); ?>" />
			<div class="clear">&nbsp;</div>
			<h3><label for="end">Konec:</label></h3>
			<input type="end" name="end" id="end" value="<?php echo StripSlashes($rec_actr['end']); ?>" />
			<div class="clear">&nbsp;</div>
			<h3><label for="secret">Přísně tajné:</label></h3>
			<select name="secret" id="secret">
			<option value="0"<?php if ($rec_actr['secret']==0) { echo ' selected="selected"'; } ?>>ne</option>
			<option value="1"<?php if ($rec_actr['secret']==1) { echo ' selected="selected"'; } ?>>ano</option>
			</select>
			<div class="clear">&nbsp;</div>
			<h3><label for="status">Stav:</label></h3>
			<select name="status" id="status">
			<option value="0"<?php if ($rec_actr['status']==0) { echo ' selected="selected"'; } ?>>rozpracované</option>
			<option value="1"<?php if ($rec_actr['status']==1) { echo ' selected="selected"'; } ?>>dokončené</option>
			<?php if ($usrinfo['right_text']) {
				echo '<option value="2"'; 
				if ($rec_actr['status']==2) { echo ' selected="selected"'; } 
				echo '>analyzované</option>';
				}
			?>
			</select>
			<div class="clear">&nbsp;</div>
		</div>
		<!-- end of #info -->
	</fieldset>

	<fieldset><legend><h2>Shrnutí:</h2></legend>
		<textarea cols="80" rows="20" name="summary" id="summary"><?php echo StripSlashes($rec_actr['summary']); ?></textarea>
	</fieldset>
	
	<fieldset><legend><h2>Možné dopady:</h2></legend>
		<textarea cols="80" rows="20" name="impacts" id="impacts"><?php echo StripSlashes($rec_actr['impacts']); ?></textarea>
	</fieldset>
	
	<fieldset><legend><h2>Podrobný popis průběhu:</h2></legend>
		<textarea cols="80" rows="30" name="details" id="details"><?php echo StripSlashes($rec_actr['details']); ?></textarea>
	</fieldset>
	
	<fieldset><legend><h2>Energetická náročnost:</h2></legend>
		<textarea cols="80" rows="10" name="energy" id="energy"><?php echo StripSlashes($rec_actr['energy']); ?></textarea>
	</fieldset>
	
	<fieldset><legend><h2>Počáteční vstupy:</h2></legend>
		<textarea cols="80" rows="10" name="inputs" id="inputs"><?php echo StripSlashes($rec_actr['inputs']); ?></textarea>
	</fieldset>	
	
	<input type="hidden" name="reportid" value="<?php echo $rec_actr['id']; ?>" />
	<input type="submit" name="editactrep" id="submitbutton" value="Uložit změny" />

</fieldset>
</form>
	
	<fieldset><legend><h2>Osoby přiřazené k hlášení: </h2></legend>
		<form action="addp2ar.php" method="post" class="otherform">
			<input type="hidden" name="rid" value="<?php echo $_REQUEST['rid']; ?>" />
			<input type="submit" value="Upravit osoby" name="setperson" class="submitbutton editbutton" title="Upravit osoby" />
		</form>
		<p><?php
		if ($usrinfo['right_power']) {
			$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."ar2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."ar2p.idperson AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		} else {
			$sql="SELECT ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname' FROM ".DB_PREFIX."ar2p, ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.id=".DB_PREFIX."ar2p.idperson AND ".DB_PREFIX."ar2p.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
		}
		$pers=MySQL_Query ($sql);
		$persons=Array();
		while ($perc=MySQL_Fetch_Assoc($pers)) {
			$persons[]='<a href="readperson.php?rid='.$perc['id'].'">'.$perc['surname'].', '.$perc['name'].'</a>';
		}
		echo ((implode($persons, '; ')<>"")?implode($persons, '; '):'<em>Nejsou připojeny žádné osoby.</em>');
		?></p>
	</fieldset>

	<fieldset><legend><h3>Přiřazené případy</h3></legend>
		<!-- tady dochází ke stylové nesystematičnosti, nejedná se o poznámku; pro nápravu je třeba projít všechny šablony -->
		<p><span class="poznamka-edit-buttons"><a class="connect" href="addar2c.php?rid=<?php echo $_REQUEST['rid']; ?>" title="přiřazení"><span class="button-text">přiřazení případů</span></a><em style="font-size:smaller;"> (přiřazování)</em></span></p>
		<!-- následuje seznam případů -->
		<?php // generování seznamu přiřazených případů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." ORDER BY ".DB_PREFIX."cases.title ASC";
			} else {
				$sql="SELECT ".DB_PREFIX."cases.id AS 'id', ".DB_PREFIX."cases.title AS 'title' FROM ".DB_PREFIX."ar2c, ".DB_PREFIX."cases WHERE ".DB_PREFIX."cases.id=".DB_PREFIX."ar2c.idcase AND ".DB_PREFIX."ar2c.idreport=".$_REQUEST['rid']." AND ".DB_PREFIX."cases.secret=0 ORDER BY ".DB_PREFIX."cases.title ASC";
			}
			$pers=MySQL_Query ($sql);
			
			$i=0;
			while ($perc=MySQL_Fetch_Assoc($pers)) { 
				$i++;
				if($i==1){ ?>
		<ul id=""><?php
				} ?>
			<li><a href="readcase.php?rid=<?php echo $perc['id']; ?>"><?php echo $perc['title']; ?></a></li>
		<?php }
			if($i<>0){ ?>
		</ul>
		<!-- end of # -->
		<?php 
			}else{?><br />
		<em>bez poznámek</em><?php
			}
		// konec seznamu přiřazených případů ?>
	</fieldset>

	<!-- následuje seznam přiložených souborů -->
	<fieldset><legend><h3>Přiložené soubory</h3></legend>
		<strong><em>K hlášení je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
		<?php //generování seznamu přiložených souborů
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=4 ORDER BY ".DB_PREFIX."data.originalname ASC";
			} else {
			  $sql="SELECT ".DB_PREFIX."data.iduser AS 'iduser', ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.secret AS 'secret', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=4 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
			}
			$res=MySQL_Query ($sql);
			$i=0;
			while ($rec_f=MySQL_Fetch_Assoc($res)) { 
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
	<!-- formulář přiřazení nových souborů -->
	<div id="new-file" class="otherform-wrap">
		<fieldset><legend><strong>Nový soubor</strong></legend>
		<form action="procactrep.php" method="post" enctype="multipart/form-data" class="otherform">
			<div>
				<strong><label for="attachment">Soubor:</label></strong>
				<input type="file" name="attachment" id="attachment" />
			</div>
			<div>
				<strong><label for="usecret">Přísně tajné:</label></strong>
			  	<?php if ($rec_actr['secret']!=1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_actr['secret']==1){ ?>checked="checked"<?php }; ?>/>ano
			</div>
<?php 		if ($usrinfo['right_power'] == 1)	{
			echo '					
			<div>
			<strong><label for="fnotnew">Není nové</label></strong>
			<input type="checkbox" name="fnotnew"/><br/>
			</div>';
			}
?>			
			<div>
				<input type="hidden" name="reportid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editactrep.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="submit" name="uploadfile" value="Nahrát soubor k případu" class="submitbutton" title="Uložit"/>
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-file .otherform-wrap -->

	<!-- následuje seznam připojených poznámek -->
	<fieldset><legend><h3>Poznámky</h3></legend>
		<span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=1" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K hlášení si můžete připsat kolik poznámek chcete.)</em></span>
		<!-- následuje seznam poznámek -->
		<?php // generování poznámek
			if ($usrinfo['right_power']) {
				$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=4 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
			} else {
				$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."users.login AS 'user', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes, ".DB_PREFIX."users WHERE ".DB_PREFIX."notes.iduser=".DB_PREFIX."users.id AND ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=4 AND ".DB_PREFIX."notes.deleted=0 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
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
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;idtable=4" title="upravit"><span class="button-text">upravit</span></a> ';
				if (($rec_n['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;itemid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('editactrep.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec_n['title'])."&quot; náležící k hlášení?');".'" title="smazat"><span class="button-text">smazat</span></a>'; ?>
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
	<div id="new-note" class="otherform-wrap">
		<fieldset><legend><strong>Nová poznámka</strong></legend>
		<form action="procnote.php" method="post" class="otherform">
			<div>
				<strong><label for="notetitle">Nadpis:</label></strong>
				<input type="text" name="title" id="notetitle" />
			</div>
			<div>
			  <strong><label for="nsecret">Utajení:</label></strong>
			  	<?php if ($rec_actr['secret']!=1) { ?>&nbsp;<input type="radio" name="secret" id="nsecret" value="0" checked="checked"/>veřejná&nbsp;/<?php }; ?>
				&nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_actr['secret']==1){ ?>checked="checked"<?php }; ?>/>tajná&nbsp;/
				&nbsp;<input type="radio" name="secret" value="2" />soukromá
			</div>
<?php 			if ($usrinfo['right_power'] == 1)	{
				echo '					
				<div>
				<strong><label for="nnotnew">Není nové</label></strong>
					<input type="checkbox" name="nnotnew"/><br/>
				</div>';
				}
?>
			<div>
				<!--  label for="notebody">Tělo poznámka:</label -->
				<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
			</div>
			<div>
				<input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
				<input type="hidden" name="backurl" value="<?php echo 'editactrep.php?rid='.$_REQUEST['rid']; ?>" />
				<input type="hidden" name="tableid" value="4" />
				<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" title="Uložit"/>
			</div>
		</form>
		</fieldset>
	</div>
	<!-- end of #new-note .otherform-wrap -->



</div>
<!-- end of #obsah -->
<?php
} else {
echo '<div id="obsah"><p>Hlášení neexistuje.</p></div>';
}
} else {
echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
}
pageEnd ();
?>