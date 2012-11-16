<?php
	require_once ('./inc/func_main.php');
	pageStart ('Symboly');
	mainMenu (5);
	deleteUnread (7,'none');
	sparklets ('<a href="persons.php">osoby</a> &raquo; <strong>nepřiřazené symboly</strong>','<a href="newsymbol.php">nový symbol</a>');
	
	// symbolu
	$sql="SELECT * FROM ".DB_PREFIX."symbols WHERE ".DB_PREFIX."symbols.deleted=0 ORDER BY ".DB_PREFIX."symbols.created DESC";
	$res=MySQL_Query ($sql);
	if (MySQL_Num_Rows($res)) {
	  echo '<div id="obsah">
<table>
<thead>
	<tr><th>Symbol</th>
	  <th>Poznámky</th>
	  <th>Výskyt</th>
	  <th>Akce</th>
	</tr>
</thead>
<tbody>
';
		$even=0;
		while ($rec=MySQL_Fetch_Assoc($res)) {
		  echo '<tr class="'.((searchRecord(7,$rec['id']))?' unread_record':(($even%2==0)?'even':'odd')).'">
		  <td><img src="getportrait.php?nrid='.$rec['id'].'" alt="symbol chybí" /></td>
		  <td>'.(StripSlashes($rec['desc'])).'</td>
	<td>Zatim prazdne.</td>
'.(($usrinfo['right_text'])?'	<td><a href="editsymbol.php?rid='.$rec['id'].'">upravit</a> | <a href="procother.php?sdelete='.$rec['id'].'" onclick="'."return confirm('Opravdu smazat tento symbol?".'">smazat</a> | přiřadit k osobě</td>':'<td><a href="newnote.php?rid='.$rec['id'].'&idtable=7">přidat poznámku</a>').'
</tr>';
			$even++;
		}
	  echo '</tbody>
</table>
</div>';
	}
	pageEnd ();
?>