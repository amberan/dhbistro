<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."groups WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
		  pageStart (StripSlashes($rec['title']));
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>'.StripSlashes($rec['title']).'</strong>');
			echo '<h1>'.StripSlashes($rec['title']).'</h1>
<div id="obsah">'.StripSlashes($rec['contents']).'
<hr />
<p>Členové: ';
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."persons, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.deleted=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
	} else {
		$sql="SELECT ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."persons, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idperson=".DB_PREFIX."persons.id AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." AND ".DB_PREFIX."persons.deleted=0 AND ".DB_PREFIX."persons.secret=0 ORDER BY ".DB_PREFIX."persons.surname, ".DB_PREFIX."persons.name ASC";
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
		$groups=Array();
		while ($rec=MySQL_Fetch_Assoc($res)) {
			$groups[]='<a href="./readperson.php?rid='.$rec['id'].'">'.StripSlashes ($rec['surname']).', '.StripSlashes ($rec['name']).'</a>';
		}
		echo implode ($groups,', ');
	} else {
		echo '&mdash;';
	}
	echo '</p>
</div>';
?>
<hr />
<!--form action="procgroup.php" method="post" enctype="multipart/form-data" class="otherform">
	<p>Ke skupině je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</p>
	<div>
		<label for="attachment">Soubor:</label>
		<input type="file" name="attachment" id="attachment" />
	</div>
	<div>
		<label for="usecret">Přísně tajné:</label>
		<select name="secret" id="usecret">
			<option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
		<input type="hidden" name="groupid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'readgroup.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" name="uploadfile" value="Nahrát soubor ke skupině" class="submitbutton" /> 
	</div>
</form-->
<ul>
<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=2 ORDER BY ".DB_PREFIX."data.originalname ASC";
	} else {
	  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=2 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="getfile.php?idfile='.$rec['id'].'">'.StripSlashes($rec['title']).'</a></li>';
	}
?>
</ul>
<hr />
<!--form action="procgroup.php" method="post" class="otherform">
	<p>K osobě si můžete připsat kolik chcete poznámek.</p>
	<div>
		<label for="notetitle">Nadpis:</label>
		<input type="text" name="title" id="notetitle" />
	</div>
	<div>
	  <label for="nsecret">Přísně tajné:</label>
		<select name="secret" id="nsecret">
		  <option value="0">ne</option>
			<option value="1">ano</option>
		</select>
	</div>
	<div>
		<label for="notebody">Tělo poznámka:</label>
		<textarea cols="80" rows="7" name="note" id="notebody"></textarea>
	</div>
	<div>
		<input type="hidden" name="groupid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'readgroup.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form-->
<ul>
<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 ORDER BY ".DB_PREFIX."notes.datum DESC";
	} else {
	  $sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=2 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="readnote.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a>'.((($rec['iduser']==$usrinfo['id']) || $usrinfo['right_power'])?' &mdash; <a href="procperson.php?deletenote='.$rec['id'].'&amp;groupid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readgroup.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící ke skupině?');".'">smazat poznámku</a></li>':'');
	}
?>
</ul>
<?php
		} else {
		  pageStart ('Skupina neexistuje');
			mainMenu (3);
			sparklets ('<a href="./groups.php">skupiny</a> &raquo; <strong>skupina neexistuje</strong>');
		  echo '<div id="obsah"><p>Skupina neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>