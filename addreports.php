<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
$latteParameters['title'] = 'Úprava hlášení';
  
use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);

	if (isset($_POST['addtoareport'])) {
		auditTrail(4, 6, $_POST['reportid']);
		if ($usrinfo['right_power']==1) {
			mysqli_query ($database,"DELETE FROM ".DB_PREFIX."ar2c WHERE ".DB_PREFIX."ar2c.idreport=".$_POST['reportid']);
		} else {
			mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2c as c, ".DB_PREFIX."cases as p WHERE c.idcase=p.id AND p.secret=0 AND c.idreport=".$_POST['reportid']);
		}
		if (isset($_POST['case'])) {
			$case=$_POST['case'];
		}
		mainMenu (5);
		sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>','<a href="readactrep.php?rid='.$_POST['reportid'].'&hidenotes=0&truenames=0">zobrazit upravené</a>');
		echo '<div id="obsah"><p>Hlášení přiřazeno k příslušným případům.</p></div>';
		if (isset($_POST['case'])) {
			for ($i=0;$i<Count($case);$i++) {
				mysqli_query ($database,"INSERT INTO ".DB_PREFIX."ar2c VALUES('".$_POST['reportid']."','".$case[$i]."','".$usrinfo['id']."')");
			}
		}
		$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	}
	
	if (isset($_POST['addcasetoareport'])) {
		auditTrail(3, 6, $_POST['caseid']);
		if ($usrinfo['right_power']==1) {
			mysqli_query ($database,"DELETE FROM ".DB_PREFIX."ar2c WHERE ".DB_PREFIX."ar2c.idcase=".$_POST['caseid']);
		} else {
			mysqli_query ($database,"DELETE c FROM ".DB_PREFIX."ar2c as c, ".DB_PREFIX."reports as p WHERE c.idreport=p.id AND p.secret=0 AND c.idcase=".$_POST['caseid']);
		}
		if (isset($_POST['report'])) {
			$report=$_POST['report'];
		}
		mainMenu (5);
		sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>','<a href="readcase.php?rid='.$_POST['caseid'].'&hidenotes=0">zobrazit upravené</a>');
		echo '<div id="obsah"><p>Hlášení k případu přiložena či odebrána.</p></div>';
		if (isset($_POST['report'])) {
			for ($i=0;$i<Count($report);$i++) {
				mysqli_query ($database,"INSERT INTO ".DB_PREFIX."ar2c VALUES('".$report[$i]."','".$_POST['caseid']."','".$usrinfo['id']."')");
			}
		}
		$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
	}
	
?>