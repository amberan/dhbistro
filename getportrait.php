<?php
	require_once ('./inc/func_main.php');
	if (isset($_REQUEST['rid']) && is_numeric ($_REQUEST['rid'])) {
    	$getres=mysqli_query ($database,"SELECT portrait FROM ".DB_PREFIX."persons WHERE ".(($usrinfo['right_power'])?'':' secret=0 AND ')." id=".$_REQUEST['rid']);
    	if ($getrec=mysqli_fetch_assoc ($getres)) {
      		header('Content-Type: image/jpg');
      		header('Content-Disposition: inline; filename="portrait'.$_REQUEST['rid'].'.jpg"');
      		header('Expires: 0');
      		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      		header('Pragma: public');
      		$getf=FOpen ("./files/portraits/".$getrec['portrait'],"r");
      		FPassThru ($getf);
    	}
  	}  
  	if (isset($_REQUEST['srid']) && is_numeric ($_REQUEST['srid'])) {
  		$getres=mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."persons WHERE ".(($usrinfo['right_power'])?'':' secret=0 AND ')." id=".$_REQUEST['srid']);
  		if ($getrec=mysqli_fetch_assoc ($getres)) {
  			header('Content-Type: image/jpg');
  			header('Content-Disposition: inline; filename="symbol'.$_REQUEST['srid'].'.jpg"');
  			header('Expires: 0');
  			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  			header('Pragma: public');
  			$getf=FOpen ("./files/symbols/".$getrec['symbol'],"r");
  			FPassThru ($getf);
  		}
  	}
  	if (isset($_REQUEST['nrid']) && is_numeric ($_REQUEST['nrid'])) {
  		$getres=mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."symbols WHERE id=".$_REQUEST['nrid']);
  		if ($getrec=mysqli_fetch_assoc ($getres)) {
  			header('Content-Type: image/jpg');
  			header('Content-Disposition: inline; filename="symbol'.$_REQUEST['nrid'].'.jpg"');
  			header('Expires: 0');
  			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  			header('Pragma: public');
  			$getf=FOpen ("./files/symbols/".$getrec['symbol'],"r");
  			FPassThru ($getf);
  		}
  	}