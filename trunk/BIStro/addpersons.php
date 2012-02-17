<?php
	require_once ('./inc/func_main.php');



if (isset($_POST['addtocase'])) {
	MySQL_Query ("DELETE FROM ".DB_PREFIX."c2p WHERE ".DB_PREFIX."c2p.idcase=".$_POST['caseid']);
	$person=$_POST['person'];
	pageStart ('Uložení změn');
	mainMenu (5);
	sparklets ('<a href="./cases.php">případy</a> &raquo; <a href="./editcase.php?rid='.$_POST['caseid'].'">úprava případu</a> &raquo; <strong>uložení změn</strong>');
	echo '<div id="obsah"><p>Uživatelé k případu uloženi.</p></div>';
	for ($i=0;$i<Count($person);$i++) {
		MySQL_Query ("INSERT INTO ".DB_PREFIX."c2p VALUES('".$person[$i]."','".$_POST['caseid']."','".$usrinfo['id']."')");
	}
	pageEnd ();
}


?>