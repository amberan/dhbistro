<?php
	require_once ('./inc/func_main.php');
	
	if (isset($_POST['addtoareport'])) {
		MySQL_Query ("DELETE FROM ".DB_PREFIX."ar2c WHERE ".DB_PREFIX."ar2c.idreport=".$_POST['reportid']);
		if (isset($_POST['case'])) {
			$case=$_POST['case'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Hlášení přiřazeno k příslušným případům.</p></div>';
		if (isset($_POST['case'])) {
			for ($i=0;$i<Count($case);$i++) {
				MySQL_Query ("INSERT INTO ".DB_PREFIX."ar2c VALUES('".$_POST['reportid']."','".$case[$i]."','".$usrinfo['id']."')");
			}
		}
		pageEnd ();
	}
	
	?>