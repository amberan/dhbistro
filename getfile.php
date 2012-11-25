<?php
	require_once ('./inc/func_main.php');
	if (is_numeric ($_REQUEST['idfile'])) {
		if ($usrinfo['right_power']) {
			$sql="SELECT mime, uniquename AS 'soubor', originalname AS 'nazev', size FROM ".DB_PREFIX."data WHERE id=".$_REQUEST['idfile'];
		} else {
			$sql="SELECT mime, uniquename AS 'soubor', originalname AS 'nazev', size FROM ".DB_PREFIX."data WHERE id=".$_REQUEST['idfile']." AND secret=0";
		}
    $getres=MySQL_Query ($sql);
    if ($getrec=MySQL_Fetch_Assoc ($getres)) {
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.$getrec['nazev'].'";');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
			header('Content-Length: '.$getrec['size']);
      $getf=FOpen ('./files/'.$getrec['soubor'],'r');
      FPassThru ($getf);
    }
  }
?>