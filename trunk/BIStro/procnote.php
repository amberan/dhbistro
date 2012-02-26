<?php
	require_once ('./inc/func_main.php');




	if (isset($_POST['setnote'])) {
		if (!preg_match ('/^[[:blank:]]*$/i',$_POST['note']) && !preg_match ('/^[[:blank:]]*$/i',$_POST['title']) && is_numeric($_POST['secret'])) {
			MySQL_Query ("INSERT INTO ".DB_PREFIX."notes VALUES('','".mysql_real_escape_string($_POST['note'])."','".mysql_real_escape_string($_POST['title'])."','".Time()."','".$usrinfo['id']."','1','".$_POST['personid']."','".$_POST['secret']."','0')");
		}
		Header ('Location: '.$_POST['backurl']);
	}
	if (isset($_GET['deletenote'])) {
		MySQl_Query("UPDATE ".DB_PREFIX."notes SET deleted=1 WHERE ".DB_PREFIX."notes.id=".$_GET['deletenote']);
		Header ('Location: '.URLDecode($_GET['backurl']));
	}



?>