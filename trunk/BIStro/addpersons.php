<?php
	require_once ('./inc/func_main.php');

if (isset($_POST['addtocase'])) {
	MySQL_Query ("DELETE FROM ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idcase=".$_POST['caseid']);
	if (isset($_POST['person'])) {
		$person=$_POST['person'];
	}
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>');
	echo '<div id="obsah"><p>Osoby k případu uloženy.</p></div>';
	if (isset($_POST['person'])) {
			for ($i=0;$i<Count($person);$i++) {
		MySQL_Query ("INSERT INTO ".DB_PREFIX."c2p VALUES('".$person[$i]."','".$_POST['caseid']."','".$usrinfo['id']."')");
		}
	}
	pageEnd ();
}

if (isset($_POST['addtogroup'])) {
	MySQL_Query ("DELETE FROM ".DB_PREFIX."g2p WHERE ".DB_PREFIX."g2p.idgroup=".$_POST['groupid']);
	if (isset($_POST['person'])) {
		$person=$_POST['person'];
	}
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./groups.php">případy</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>');
	echo '<div id="obsah"><p>Osoby příslušné ke skupině uloženy.</p></div>';
	if (isset($_POST['person'])) {
		for ($i=0;$i<Count($person);$i++) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."g2p VALUES('".$person[$i]."','".$_POST['groupid']."','".$usrinfo['id']."')");
		}
	}
	pageEnd ();
}

if (isset($_POST['addtoareport'])) {
	MySQL_Query ("DELETE FROM ".DB_PREFIX."ar2p WHERE ".DB_PREFIX."ar2p.idreport=".$_POST['reportid']);
	if (isset($_POST['person'])) {
		$person=$_POST['person'];
	}
	// podminka pro urceni ulohy osoby v hlaseni
	if (isset($_POST['role'])) {
		$role=$_POST['role'];
	}
	 // 0: osoba přítomná
	 // 1: vyslýchaný
	 // 2: vyslýchající
	 // 3: zatčený

	if (isset($_POST['person'])) {
		for ($i=0;$i<Count($person);$i++) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."ar2p VALUES('".$person[$i]."','".$_POST['reportid']."','".$usrinfo['id']."','".$role[$i]."')");
		}
	}
	// header('Location: ./editactrep.php?rid='.$_POST['reportid']); // přesměrování zpět na předchozí stránku
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>');
	echo '<div id="obsah"><p>Osoby příslušné k hlášení uloženy.</p></div>';
	pageEnd ();
}

?>