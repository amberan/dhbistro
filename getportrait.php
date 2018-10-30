<?php
	require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
	header('Content-Type: image/jpg');
	header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: -1");
	if (isset($_REQUEST['rid']) && is_numeric ($_REQUEST['rid'])) { //portret
    	$getres=mysqli_query ($database,"SELECT portrait FROM ".DB_PREFIX."persons WHERE ".(($usrinfo['right_power'])?'':' secret=0 AND ')." id=".$_REQUEST['rid']);
    	if ($getrec=mysqli_fetch_assoc ($getres)) {
      		header('Content-Disposition: inline; filename="portrait'.$_REQUEST['rid'].'.jpg"');
     		if (file_exists($_SERVER['DOCUMENT_ROOT'].$config['folder_portrait'].$getrec['portrait']) and !is_dir(($_SERVER['DOCUMENT_ROOT'].$config['folder_portrait'].$getrec['portrait']))) {
				$getf=FOpen ($_SERVER['DOCUMENT_ROOT'].$config['folder_portrait'].$getrec['portrait'],"r");
				FPassThru ($getf);
			} else { 
				$getf=FOpen ($_SERVER['DOCUMENT_ROOT']."/images/placeholder.jpg","r");
				FPassThru ($getf);}
			}
  	} else if (isset($_REQUEST['srid']) && is_numeric ($_REQUEST['srid'])) { //symbol
  		$getres=mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."persons WHERE ".(($usrinfo['right_power'])?'':' secret=0 AND ')." id=".$_REQUEST['srid']);
  		if ($getrec=mysqli_fetch_assoc ($getres)) {
  			header('Content-Disposition: inline; filename="symbol'.$_REQUEST['srid'].'.jpg"');
		  if (file_exists($_SERVER['DOCUMENT_ROOT'].$config['folder_symbol'].$getrec['symbol'])) {
				$getf=FOpen ($_SERVER['DOCUMENT_ROOT'].$config['folder_symbol'].$getrec['symbol'],"r");
				  FPassThru ($getf);}
  		}
  	} else if (isset($_REQUEST['nrid']) && is_numeric ($_REQUEST['nrid'])) { //symbol neprirazeny
  		$getres=mysqli_query ($database,"SELECT symbol FROM ".DB_PREFIX."symbols WHERE id=".$_REQUEST['nrid']);
  		if ($getrec=mysqli_fetch_assoc ($getres)) {
  			header('Content-Disposition: inline; filename="symbol'.$_REQUEST['nrid'].'.jpg"');
			  if (file_exists($_SERVER['DOCUMENT_ROOT'].$config['folder_symbol'].$getrec['symbol'])) {
				$getf=FOpen ($_SERVER['DOCUMENT_ROOT'].$config['folder_symbol'].$getrec['symbol'],"r");
			  FPassThru ($getf);}
			  
		  }
	} else { 
		$getf=FOpen ($_SERVER['DOCUMENT_ROOT']."/images/placeholder.jpg","r");
		FPassThru ($getf);
	}

	
	  