<?php
	require_once ('./inc/func_main.php');
	if (is_numeric ($_REQUEST['rid'])) {
    $getres=MySQL_Query ("SELECT portrait FROM ".DB_PREFIX."persons WHERE ".(($usrinfo['right_power'])?'':' secret=0 AND ')." id=".$_REQUEST['rid']);
    if ($getrec=MySQL_Fetch_Assoc ($getres)) {
      header('Content-Type: image/jpeg');
      header('Content-Disposition: inline; filename="portrait'.$_REQUEST['rid'].'.jpg"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      $getf=FOpen ("./files/portraits/".$getrec["portrait"],"r");
      FPassThru ($getf);
    }
  }
?>
