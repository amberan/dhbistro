<?php
	require_once ('./inc/func_main.php');
	if (is_numeric($_REQUEST['rid'])) {
		$res=MySQL_Query ("SELECT * FROM ".DB_PREFIX."notes WHERE id=".$_REQUEST['rid']);
		if ($rec=MySQL_Fetch_Assoc($res)) {
			if ($rec['secret']==0 || $rec['iduser']==$usrinfo['id'] || $usrinfo['right_power']) {
			  pageStart (StripSlashes($rec['title']));
				mainMenu (0);
				switch ($_REQUEST['idtable']) {
					case 1: $sourceurl="persons.php"; $sourcename="osoby"; break;
					case 2: $sourceurl="groups.php"; $sourcename="skupiny"; break;
					case 3: $sourceurl="cases.php"; $sourcename="případy"; break;
					case 4: $sourceurl="reports.php"; $sourcename="hlášení"; break;
					default: $sourceurl=""; $sourcename=""; break;
				}
				sparklets ('<a href="./'.$sourceurl.'">'.$sourcename.'</a> &raquo; <strong>zobrazení poznámky</strong>');
				echo '<h1>'.StripSlashes($rec['title']).'</h1>
				<div id="obsah">'.StripSlashes($rec['note']).'</div>';
			} else {
				pageStart ('Nemáte práva');
				mainMenu (0);
				sparklets ('<strong>Nemáte práva</strong>');
				echo '<h1>Nemáte práva</h1>
				<div id="obsah">Nemáte práva číst tuto poznámku.</div>';
			}
		} else {
		  pageStart ('Poznámka neexistuje');
			mainMenu (0);
			sparklets ('<strong>poznámka neexistuje</strong>');
		  echo '<div id="obsah"><p>Osoba neexistuje.</p></div>';
		}
	} else {
	  echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
	}
	pageEnd ();
?>