<?php
	require_once ('./inc/func_main.php');
	pageStart ('Symboly');
	mainMenu (5);
	sparklets ('<a href="persons.php">osoby</a> &raquo; <strong>nepřiřazené symboly</strong>','<a href="newsymbol.php">přidat symbol</a>');
	
	// symbolu
	$sql="SELECT ".DB_PREFIX."persons.phone AS 'phone', ".DB_PREFIX."persons.secret AS 'secret', ".DB_PREFIX."persons.name AS 'name', ".DB_PREFIX."persons.surname AS 'surname', ".DB_PREFIX."persons.id AS 'id' FROM ".DB_PREFIX."persons WHERE ".DB_PREFIX."persons.deleted=0 ORDER BY ".DB_PREFIX."persons.surname DESC";
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr><th>Jméno</th>
	  <th>Telefon</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.((searchRecord(1,$rec['id']))?' unread_record':(($even%2==0)?'even':'odd')).'">';
//'.(($sportraits)?'<td><img src="getportrait.php?rid='.$rec['id'].'" alt="portrét chybí" /></td>':'').'
//'.(($ssymbols)?'<td><img src="getportrait.php?srid='.$rec['id'].'" alt="symbol chybí" /></td>':'').'
	echo '<td>'.(($rec['secret'])?'<span class="secret"><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a></span>':'<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name']))).'</a>').'</td>
	<td>'.$rec['phone'].'</td>
'.(($usrinfo['right_text'])?'	<td><a href="editperson.php?rid='.$rec['id'].'">upravit</a> | <a href="procperson.php?delete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat osobu &quot;".implode(', ',Array(StripSlashes($rec['surname']),StripSlashes($rec['name'])))."&quot;?');".'">smazat</a></td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=5">přidat poznámku</a>').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
	pageEnd ();
?>