<?php
	require_once ('./inc/func_main.php');
	
	if (isset($_POST['addtoareport'])) {
		MySQL_Query ("DELETE FROM ".DB_PREFIX."ar2c WHERE ".DB_PREFIX."ar2c.idreport=".$_POST['reportid']);
		if (isset($_POST['case'])) {
			$case=$_POST['case'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>','<a href="readreport.php?rid='.$_POST['reportid'].'&hidenotes=0&truenames=0">zobrazit upravené</a>');
		echo '<div id="obsah"><p>Hlášení přiřazeno k příslušným případům.</p></div>';
		if (isset($_POST['case'])) {
			for ($i=0;$i<Count($case);$i++) {
				MySQL_Query ("INSERT INTO ".DB_PREFIX."ar2c VALUES('".$_POST['reportid']."','".$case[$i]."','".$usrinfo['id']."')");
			}
		}
		pageEnd ();
	}
	
	if (isset($_POST['addcasetoareport'])) {
		MySQL_Query ("DELETE FROM ".DB_PREFIX."ar2c WHERE ".DB_PREFIX."ar2c.idcase=".$_POST['caseid']);
		if (isset($_POST['report'])) {
			$report=$_POST['report'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>','<a href="readcase.php?rid='.$_POST['caseid'].'&hidenotes=0">zobrazit upravené</a>');
		echo '<div id="obsah"><p>Hlášení k případu přiložena či odebrána.</p></div>';
		if (isset($_POST['report'])) {
			for ($i=0;$i<Count($report);$i++) {
				MySQL_Query ("INSERT INTO ".DB_PREFIX."ar2c VALUES('".$report[$i]."','".$_POST['caseid']."','".$usrinfo['id']."')");
			}
		}
		pageEnd ();
	}
	
?>