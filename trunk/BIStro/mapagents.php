<?php
	require_once ('./inc/func_main.php');
		pageStart ('Mapa agentů');
		mainMenu (4);
		sparklets ('<a href="./mapagents.php">Mapa agentů</a>');
	echo '<div id="obsah"><a href="http://maps.google.com/maps/ms?msa=0&msid=202010519094597531222.0004b849ff189c5e9f6a7" target=_new>Aktuální mapa agentů.</a></div>';
	pageEnd ();
?>