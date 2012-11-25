<?php
	require_once ('./inc/func_main.php');
	
	if (isset($_POST['addsymbol2c'])) {
		auditTrail(7, 6, $_POST['symbolid']);
		if ($usrinfo['right_power']==1) {
			$sql="DELETE FROM ".DB_PREFIX."symbol2all WHERE ".DB_PREFIX."symbol2all.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."symbol2all.table=3";
			MySQL_Query ($sql);
		} else {
			$sql="DELETE c FROM ".DB_PREFIX."symbol2all as c, ".DB_PREFIX."cases as p WHERE c.idsymbol=p.id AND p.secret=0 AND c.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."c.table=3";
			MySQL_Query ($sql);
		}
		if (isset($_POST['case'])) {
			$case=$_POST['case'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Symbol přiřazen k příslušným případům.</p></div>';
		if (isset($_POST['case'])) {
			for ($i=0;$i<Count($case);$i++) {
				$sql="INSERT INTO ".DB_PREFIX."symbol2all VALUES('".$_POST['symbolid']."','".$case[$i]."','".$usrinfo['id']."','3')";
				MySQL_Query ($sql);
			}
		}
		pageEnd ();
	}
	
	if (isset($_POST['addsymbol2ar'])) {
		auditTrail(7, 6, $_POST['symbolid']);
		if ($usrinfo['right_power']==1) {
			$sql="DELETE FROM ".DB_PREFIX."symbol2all WHERE ".DB_PREFIX."symbol2all.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."symbol2all.table=4";
			MySQL_Query ($sql);
		} else {
			$sql="DELETE c FROM ".DB_PREFIX."symbol2all as c, ".DB_PREFIX."cases as p WHERE c.idsymbol=p.id AND p.secret=0 AND c.idsymbol=".$_POST['symbolid']." AND ".DB_PREFIX."c.table=4";
			MySQL_Query ($sql);
		}
		if (isset($_POST['report'])) {
			$report=$_POST['report'];
		}
		pageStart ('Uložení změn');
		mainMenu (5);
		sparklets ('<a href="./symbols.php">symboly</a> &raquo; <a href="./editsymbol.php?rid='.$_POST['symbolid'].'">úprava symbolu</a> &raquo; <strong>uložení změn</strong>');
		echo '<div id="obsah"><p>Symbol přiřazen k příslušným hlášení.</p></div>';
		if (isset($_POST['report'])) {
			for ($i=0;$i<Count($report);$i++) {
				MySQL_Query ("INSERT INTO ".DB_PREFIX."symbol2all VALUES('".$_POST['symbolid']."','".$report[$i]."','".$usrinfo['id']."','4')");
			}
		}
		pageEnd ();
	}
	
?>