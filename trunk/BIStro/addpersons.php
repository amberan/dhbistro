<?php
	require_once ('./inc/func_main.php');

if (isset($_POST['addtocase'])) {
	MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=0 AND p.dead=0 AND c.idcase=".$_POST['caseid']);
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==0 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=0 AND p.dead=0 AND c.idcase=".$_POST['caseid']);
	} 
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==1 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=1 AND p.dead=0 AND c.idcase=".$_POST['caseid']);
	}
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==0 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=0 AND p.dead=1 AND c.idcase=".$_POST['caseid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==1 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=1 AND p.dead=0 AND c.idcase=".$_POST['caseid']);
	}
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==1 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=1 AND p.dead=1 AND c.idcase=".$_POST['caseid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==0 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=0 AND p.dead=1 AND c.idcase=".$_POST['caseid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==1 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."c2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=1 AND p.dead=1 AND c.idcase=".$_POST['caseid']);
	}
	if (isset($_POST['person'])) {
		$person=$_POST['person'];
	}
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>','<a href="readcase.php?rid='.$_POST['caseid'].'&hidenotes=0">zobrazit upravené</a>');
	echo '<div id="obsah"><p>Osoby k případu uloženy.</p></div>';
	if (isset($_POST['person'])) {
			for ($i=0;$i<Count($person);$i++) {
		MySQL_Query ("INSERT INTO ".DB_PREFIX."c2p VALUES('".$person[$i]."','".$_POST['caseid']."','".$usrinfo['id']."')");
		}
	}
	pageEnd ();
}

if (isset($_POST['addtogroup'])) {
	MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=0 AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==0 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=0 AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
	} 
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==1 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=1 AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
	}
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==0 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=0 AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==1 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=1 AND p.dead=0 AND c.idgroup=".$_POST['groupid']);
	}
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==1 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=1 AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==0 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=0 AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==1 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."g2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=1 AND p.dead=1 AND c.idgroup=".$_POST['groupid']);
	}
	if (isset($_POST['person'])) {
		$person=$_POST['person'];
	}
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./groups.php">skupiny</a> &raquo; <a href="./editgroup.php?rid='.$_POST['groupid'].'">úprava skupiny</a> &raquo; <strong>uložení změn</strong>','<a href="readgroup.php?rid='.$_POST['groupid'].'&hidenotes=0">zobrazit upravené</a>');
	echo '<div id="obsah"><p>Osoby příslušné ke skupině uloženy.</p></div>';
	if (isset($_POST['person'])) {
		for ($i=0;$i<Count($person);$i++) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."g2p VALUES('".$person[$i]."','".$_POST['groupid']."','".$usrinfo['id']."')");
		}
	}
	pageEnd ();
}

if (isset($_POST['addtoareport'])) {
	MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=0 AND p.dead=0 AND c.idreport=".$_POST['reportid']);
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==0 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=0 AND p.dead=0 AND c.idreport=".$_POST['reportid']);
	} 
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==1 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=1 AND p.dead=0 AND c.idreport=".$_POST['reportid']);
	}
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==0 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=0 AND p.dead=1 AND c.idreport=".$_POST['reportid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==1 && $_POST['fdead']==0) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=1 AND p.dead=0 AND c.idreport=".$_POST['reportid']);
	}
	if ($usrinfo['right_power']==0 && $_POST['farchiv']==1 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=0 AND p.archiv=1 AND p.dead=1 AND c.idreport=".$_POST['reportid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==0 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=0 AND p.dead=1 AND c.idreport=".$_POST['reportid']);
	}
	if ($usrinfo['right_power']==1 && $_POST['farchiv']==1 && $_POST['fdead']==1) {
		MySQL_Query ("DELETE c FROM ".DB_PREFIX."ar2p as c, ".DB_PREFIX."persons as p WHERE c.idperson=p.id AND p.secret=1 AND p.archiv=1 AND p.dead=1 AND c.idreport=".$_POST['reportid']);
	}
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
	 // 4: velitel vyjezdu

	if (isset($_POST['person'])) {
		for ($i=0;$i<Count($person);$i++) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."ar2p VALUES('".$person[$i]."','".$_POST['reportid']."','".$usrinfo['id']."','".$role[$i]."')");
		}
	}
	// header('Location: ./editactrep.php?rid='.$_POST['reportid']); // přesměrování zpět na předchozí stránku
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./reports.php">hlášení</a> &raquo; <a href="./editactrep.php?rid='.$_POST['reportid'].'">úprava hlášení</a> &raquo; <strong>uložení změn</strong>','<a href="readactrep.php?rid='.$_POST['reportid'].'&hidenotes=0&truenames=0">zobrazit upravené</a>');
	echo '<div id="obsah"><p>Osoby příslušné k hlášení uloženy.</p></div>';
	pageEnd ();
}

?>