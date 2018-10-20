<?php
  require_once ('./inc/func_main.php');
	// odhlaseni
  mysqli_query ($database,"DELETE FROM ".DB_PREFIX."loggedin WHERE iduser=".$usrinfo['id']);
  unset($_SESSION['sid']);
  Header ('location: login.php');
?>
