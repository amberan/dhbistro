<?php $starttime = microtime(true);// (array_sum(explode(" ",microtime())));
	
	global $database,$point;
	
	$config['page_prefix']='';
	$config['version']='1.5.2'; 
	$config['backup_folder'] = "files/backups/";
	$config['timeout']=600;

	require_once($_SERVER['DOCUMENT_ROOT'].'inc/audit_trail.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/backup.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/database.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/footer.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/header.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/menu.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/session.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'inc/unread.php');
  
// vyhledani zaznamu v neprectenych zaznamech - cases, groups, persons, reports, symbols
function searchRecord ($tablenum, $recordnum) {
	global $database,$unread;
	foreach ($unread as $record) {
            if ($record['idtable'] == $tablenum && $record['idrecord'] == $recordnum) {
            return true;
        }
    }
	return false;
}

// ziskani autora zaznamu - audit, dashboard, edituser, index, readcase, readperson, readsymbol, tasks
function getAuthor ($recid,$trn) {
	global $database;
	if ($trn==1) {
		$sql_ga="SELECT ".DB_PREFIX."persons.name as 'name', ".DB_PREFIX."persons.surname as 'surname', ".DB_PREFIX."users.login as 'nick' FROM ".DB_PREFIX."persons, ".DB_PREFIX."users WHERE ".DB_PREFIX."users.id=".$recid." AND ".DB_PREFIX."persons.id=".DB_PREFIX."users.idperson";
		$res_ga=mysqli_query ($database,$sql_ga);
		if (mysqli_num_rows ($res_ga)) {
			while ($rec_ga=mysqli_fetch_assoc ($res_ga)) {
				$name=StripSlashes ($rec_ga['surname']).', '.StripSlashes ($rec_ga['name']);
				return $name;
			}
		} else {
			$name='Uživatel není přiřazen.';
			return $name;
		}
	} else {
		$sql_ga="SELECT ".DB_PREFIX."users.login as 'nick' FROM ".DB_PREFIX."users WHERE ".DB_PREFIX."users.id=".$recid;
		$res_ga=mysqli_query ($database,$sql_ga);
		if (mysqli_num_rows ($res_ga)) {
			while ($rec_ga=mysqli_fetch_assoc ($res_ga)) {
				$name=StripSlashes ($rec_ga['nick']);
				return $name;
			}
		} else {
			$name='Neznámo.';
			return $name;
		}
	}
}

function safeInput ($input) {
	$replaced=Array ('"');
	$replacers=Array ('&quot;');
	$output=str_replace ($replaced,$replacers,$input);
	return $output;
}
	
function resize_Image ($img,$max_width,$max_height) {
	$size=GetImageSize($img);
	$width=$size[0];
	$height=$size[1];
	$x_ratio=$max_width/$width;
	$y_ratio=$max_height/$height;
	if (($width<=$max_width) && ($height<=$max_height)) {
		$tn_width=$width;
		$tn_height=$height;
	} else if (($x_ratio * $height) < $max_height) {
		$tn_height=ceil($x_ratio * $height);
		$tn_width=$max_width;
	} else {
		$tn_width=ceil($y_ratio * $width);
		$tn_height=$max_height;
	}
	if ($size[2]==1) {
		$src=ImageCreateFromGIF($img);
	}
	if ($size[2]==2) {
		$src=ImageCreateFromJPEG($img);
	}
	if ($size[2]==3) {
		$src=ImageCreateFromPNG($img);
	}
	$dst=ImageCreateTrueColor($tn_width,$tn_height);
	ImageCopyResampled ($dst,$src,0,0,0,0,$tn_width,$tn_height,$width,$height);
	Imageinterlace($dst, 1);
	ImageDestroy($src);
	return $dst;
}
        
// funkce pro ukládání fitru do databáza a načítání filtru z databáze        
function custom_Filter ($idtable, $idrecord = 0) {
	global $database,$usrinfo;
	switch ($idtable) {
		case 1: $table="persons"; break;
		case 2: $table="groups"; break;
		case 3: $table="cases"; break;
		case 4: $table="reports"; break;
		case 8: $table="users"; break;
		case 9: $table="evilpts"; break;
		case 10: $table="tasks"; break;
		case 11: $table="audit"; break;
		case 13: $table="search"; break;
		case 14: $table="group".$idrecord; break;
		case 15: $table="p2c"; break;
		case 16: $table="c2ar"; break;
		case 17: $table="p2ar"; break;
		case 18: $table="ar2c"; break;
		case 19: $table="p2g"; break;
		case 20: $table="sy2p"; break;
		case 21: $table="sy2c"; break;
		case 22: $table="sy2ar"; break;
	}
	$sql_cf = "SELECT filter FROM ".DB_PREFIX."users WHERE id = ".$usrinfo['id'];
	$res_cf=mysqli_query ($database,$sql_cf);
	$filter = $_REQUEST;
	// pokud přichází nový filtr a nejedná se o zadání úkolu či přidání zlobodů, případně pokud se jedná o konkrétní záznam a je nově filtrovaný,
	// použij nový filtr a ulož ho do databáze
	if ((!empty($filter) && !isset($_POST['inserttask']) && !isset($_POST['addpoints']) && !isset($filter['rid'])) || (isset($filter['sort']) && isset($filter['rid']))) {
		if ($res_cf) {
			$rec_cf = mysqli_fetch_assoc ($res_cf);
			$filters = unserialize($rec_cf['filter']);
			$filters[$table] = $filter;
		} else {
			$filters[$table] = $filter;
		}
		$sfilters = serialize($filters);
		$sql_scf = "UPDATE ".DB_PREFIX."users SET filter='".$sfilters."' WHERE id=".$usrinfo['id'];
		mysqli_query ($database,$sql_scf);
	// v opačném případě zkontroluj, zda existuje odpovídající filtr v databázi, a pokud ano, načti jej    
	} else {
		if ($res_cf) {
			$rec_cf=mysqli_fetch_assoc ($res_cf);
			$filters = unserialize($rec_cf['filter']);
			if (!empty($filters)) {
				if (array_key_exists($table, $filters)) {
					$filter = $filters[$table];
				}
			}
		}
	}
	return $filter;
}
?>