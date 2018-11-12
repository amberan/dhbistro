<?php

//auditni stopa
function auditTrail ($record_type,$operation_type,$idrecord) {
	global $database,$usrinfo;
	$sql_check="SELECT * FROM ".DB_PREFIX."audit_trail WHERE iduser='".$usrinfo['id']."' AND time='".time()."'";
	$res_check=mysqli_query ($database,$sql_check);
	if (mysqli_num_rows ($res_check)) {
	} else {
		if (!$usrinfo['currip']) {
			$currip=$_SERVER['REMOTE_ADDR'];
		} else {
			$currip=$usrinfo['currip'];
		}
		$sql_au="INSERT INTO ".DB_PREFIX."audit_trail VALUES('','".$usrinfo['id']."','".time()."','".$operation_type."','".$record_type."','".$idrecord."','".$currip."','".$usrinfo['right_org']."')";
		mysqli_query ($database,$sql_au);
	}
}

//pokus o pristup k tajnemu, soukromemu nebo smazanemu zaznamu
function unauthorizedAccess ($record_type,$secret,$deleted,$idrecord) {
	global $database,$usrinfo;
        switch ($record_type) {
            case 1:
                $link='<a href="./persons.php">osoby</a>';
                break;
            case 2:
                $link='<a href="./groups.php">skupiny</a>';
                break;
            case 3:
                $link='<a href="./cases.php">případy</a>';
                break;
            case 4:
                $link='<a href="./reports.php">hlášení</a>';
                break;
            case 8:
                $link='A ven!';
                break;
            case 11:
                $link='A ven!';
                break;
        }
        if ($deleted==1) {
            auditTrail($record_type, 13, $idrecord);
        } else {
            auditTrail($record_type, 12, $idrecord);
        }
        //pageStart ('Neautorizovaný přístup');
        //mainMenu (5);
        //sparklets ($link.' &raquo; <strong>neautorizovaný přístup</strong>');
//		echo '<div id="obsah"><p>Tady nemáš co dělat.</p></div>';
		$_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
		Header ('location: index.php');
		//pageEnd ();
		//exit();

}



?>