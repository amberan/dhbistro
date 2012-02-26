<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."persons WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
		  $sides=Array('','světlý','temný','člověk','neznámá');
		  $powers=Array('','neznámá','člověk','mimo kategorie','1. kategorie','2. kategorie','3. kategorie','4. kategorie');
		  pageStart (StripSlashes($rec['surname']).', '.StripSlashes($rec['name']));
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>'.StripSlashes($rec['surname']).', '.StripSlashes($rec['name']).'</strong>');
			?><?php 
						echo '<img src="getportrait.php?rid='.$_REQUEST['rid'].'" alt="portrét chybí" id="portraitimg" />
			<h1>'.StripSlashes($rec['surname']).', '.StripSlashes($rec['name']).'</h1>
			<div id="obsah">
			<p>Jméno: <strong>'.StripSlashes($rec['name']).'</strong><br />
Příjmení: <strong>'.StripSlashes($rec['surname']).'</strong><br />
Strana: <strong>';
			switch ($rec['side']) {
				case 1: $side = 'světlo'; break;
				case 2: $side = 'tma'; break;
				case 3: $side = 'člověk'; break;
				default: $side = 'neznámá'; break;
			}
			echo $side.'</strong><br />
Síla: <strong>';
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
			echo $power.'</strong><br />
Specializace: <strong>';
			switch ($rec['spec']) {
				case 1: $side = 'bílý mág'; break;
				case 2: $side = 'černý mág'; break;
				case 3: $side = 'léčitel'; break;
				case 4: $side = 'obrateň'; break;
				case 5: $side = 'upír'; break;
				case 6: $side = 'vlkodlak'; break;
				case 7: $side = 'vědma'; break;
				case 8: $side = 'zaříkávač'; break;
				default: $side = 'neznámá'; break;
			}
			echo $side.'</strong><br />			
Přísně tajné: <strong>'.(($rec['secret'])?'ano':'ne').'</strong></p>
'.StripSlashes($rec['contents']).'
<hr />
<p>Patří do skupin: ';
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."groups, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."groups.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."groups.deleted=0 ORDER BY ".DB_PREFIX."groups.title ASC";
	} else {
		$sql="SELECT ".DB_PREFIX."groups.secret AS 'secret', ".DB_PREFIX."groups.title AS 'title', ".DB_PREFIX."groups.id AS 'id', ".DB_PREFIX."g2p.iduser FROM ".DB_PREFIX."groups, ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".DB_PREFIX."groups.id AND ".DB_PREFIX."g2p.idperson=".$_REQUEST['rid']." AND ".DB_PREFIX."groups.deleted=0 AND ".DB_PREFIX."groups.secret=0 ORDER BY ".DB_PREFIX."groups.title ASC";
	}
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
		$groups=Array();
		while ($rec=MySQL_Fetch_Assoc($res)) {
			$groups[]='<a href="./readgroup.php?rid='.$rec['id'].'">'.StripSlashes ($rec['title']).'</a>';
		}
		echo implode ($groups,', ');
	} else {
		echo '&mdash;';
	}
	echo '</p>
</div>';
?>
<hr />
<!--form action="procperson.php" method="post" enctype="multipart/form-data" class="otherform">
	<p>K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</p>
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
		<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'readperson.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" name="uploadfile" value="Nahrát soubor k osobě" class="submitbutton" /> 
	</div>
</form-->
<ul>
<?php
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 ORDER BY ".DB_PREFIX."data.originalname ASC";
	} else {
	  $sql="SELECT ".DB_PREFIX."data.originalname AS 'title', ".DB_PREFIX."data.id AS 'id' FROM ".DB_PREFIX."data WHERE ".DB_PREFIX."data.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."data.idtable=1 AND ".DB_PREFIX."data.secret=0 ORDER BY ".DB_PREFIX."data.originalname ASC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<li><a href="getfile.php?idfile='.$rec['id'].'">'.StripSlashes($rec['title']).'</a></li>';
	}
?>
</ul>
<hr />
<!--form action="procperson.php" method="post" class="otherform">
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
		<input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
		<input type="hidden" name="backurl" value="<?php echo 'readperson.php?rid='.$_REQUEST['rid']; ?>" />
		<input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" />
	</div>
</form-->
<ul>
<?php
	echo '<h3>Poznámky:</h3>';
	if ($usrinfo['right_power']) {
		$sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND (".DB_PREFIX."notes.secret<2 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	} else {
	  $sql="SELECT ".DB_PREFIX."notes.iduser AS 'iduser', ".DB_PREFIX."notes.title AS 'title', ".DB_PREFIX."notes.note AS 'note', ".DB_PREFIX."notes.secret AS 'secret', ".DB_PREFIX."notes.id AS 'id' FROM ".DB_PREFIX."notes WHERE ".DB_PREFIX."notes.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."notes.idtable=1 AND (".DB_PREFIX."notes.secret=0 OR ".DB_PREFIX."notes.iduser=".$usrinfo['id'].") ORDER BY ".DB_PREFIX."notes.datum DESC";
	}
	$res=MySQL_Query ($sql);
	while ($rec=MySQL_Fetch_Assoc($res)) {
		echo '<h4><a href="readnote.php?rid='.$rec['id'].'">'.StripSlashes($rec['title']).'</a>';
		if ($rec['secret']==0) echo ' (veřejná)';
		if ($rec['secret']==1) echo ' (tajná)';
		if ($rec['secret']==2) echo ' (soukromá)';
		echo '</h4>';
		echo '<div id="obsah"><p>'.StripSlashes($rec['note']).'</p></div>';
		if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_text'])) echo '<a href="procperson.php?editnote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'">upravit poznámku</a> ';
		if (($rec['iduser']==$usrinfo['id']) || ($usrinfo['right_power'])) echo '<a href="procperson.php?deletenote='.$rec['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl='.URLEncode('readperson.php?rid='.$_REQUEST['rid']).'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".StripSlashes($rec['title'])."&quot; náležící k osobě?');".'">smazat poznámku</a>';
	}
?>
</ul>
<?php
		} else {
			pageStart ('Osoba neexistuje');
			mainMenu (5);
			sparklets ('<a href="./persons.php">osoby</a> &raquo; <strong>osoba neexistuje</strong>');
		  echo '<div id="obsah"><p>Osoba neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>