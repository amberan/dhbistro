<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delallnew'])) {
	  MySQL_Query ("TRUNCATE ".DB_PREFIX."unread_".$usrinfo['id']);
	  Header ('Location: '.$_REQUEST['delallnew']);
	}
	if (isset($_POST['editdashboard'])) {
		pageStart ('Upravena nástěnka');
		mainMenu (5);
		sparklets ('<a href="dashboard.php">nástěnka</a> &raquo; <strong>nástěnka upravena</strong>');
		$sql="INSERT INTO ".DB_PREFIX."dashboard VALUES('','".Time()."','".$usrinfo['id']."','".mysql_real_escape_string(safeInput($_POST['contents']))."')";
		MySQL_Query ($sql);
		echo '<div id="obsah"><p>Nástěnka upravena.</p></div>';
		pageEnd ();
	}
?>