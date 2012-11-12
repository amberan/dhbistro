<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['delallnew'])) {
	  MySQL_Query ("TRUNCATE ".DB_PREFIX."unread_".$usrinfo['id']);
	  Header ('Location: '.$_REQUEST['delallnew']);
	}
?>