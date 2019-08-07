<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	if (is_numeric ($_REQUEST['idfile'])) {
		if ($usrinfo['right_power']) {
			$sql="SELECT mime, uniquename AS 'soubor', originalname AS 'nazev', size FROM ".DB_PREFIX."file WHERE id=".$_REQUEST['idfile'];
		} else {
			$sql="SELECT mime, uniquename AS 'soubor', originalname AS 'nazev', size FROM ".DB_PREFIX."file WHERE id=".$_REQUEST['idfile']." AND secret=0";
		}
    $getres=mysqli_query ($database,$sql);
    if ($getrec=mysqli_fetch_assoc ($getres)) {
		if (in_array($getrec['mime'],$config['mime-image'])) {
			header('Content-Type: '.$getrec['mime']);
		} else {
			header('Content-Type: application/octet-stream');

		}
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