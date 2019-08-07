<?php 
session_start();
	
$config['version']='1.6.0';  // verze bistra TODO hotfix pro vyvoj 1.6.0
define ('DB_PREFIX','nw_'); // prefix tabulek
$config['dbpass'] = "/inc/important.php"; // soubor s heslem k databazi - na druhem radku
$config['page_prefix']=''; // uri cesta mezi domenou a adresarem bistra
$config['page_free']=array('login.php','logout.php'); // stranky dostupne bez prihlaseni
$config['folder_backup'] = "/files/backups/"; // adresar pro generovani zaloh
$config['folder_portrait'] = "/files/portraits/"; // adresar s portrety
$config['folder_symbol'] = "/files/symbols/"; // adresar se symboly
$config['mime-image'] = array("image/jpeg","image/pjpeg", "image/png");
$config['folder_logs'] = $_SERVER['DOCUMENT_ROOT'].'/log/'; // adresar pro tracy logy
$config['folder_custom'] = $_SERVER['DOCUMENT_ROOT'].'/custom/'; // adresar pro customizace (dh, nh, enigma....)
$config['folder_templates'] = $_SERVER['DOCUMENT_ROOT'].'/templates/'; // adresar pro latte templaty
$config['folder_cache'] = $_SERVER['DOCUMENT_ROOT'].'/cache/'; // adresar pro latte cache
require_once($config['folder_custom'].'text.php'); // defaultni texty - nasledne pretizeno hodnotami nactenymi v ramci inc/database.php

// *** TECHNICAL LIBRARIES
    require_once($_SERVER['DOCUMENT_ROOT'].'/inc/platform.php');
    if ($config['custom'] != null) { //prepsani defaultnich textu
        require_once($config['folder_custom'].'/text-'.$config['custom'].'.php');
    }
	require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
		use Tracy\Debugger;
		Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
		//Debugger::log("alert: ".$_SESSION['message']);
		$latte = new Latte\Engine;
		$latte->setTempDirectory($config['folder_cache']);
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/database.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/backup.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/session.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/audit_trail.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/image.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/unread.php');
// *** PROCESSING
	require_once($_SERVER['DOCUMENT_ROOT'].'/processing/person.php'); //operace s objektem osoby
	require_once($_SERVER['DOCUMENT_ROOT'].'/processing/news.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/processing/users.php'); //zpracovani uzivatele, vcetne zmen sama sebe
// *** GENERAL ALERT - overit, ze funguje s odlasovanim nahore - asi bude potreba prenaset message prez session destroy
	if (isset($_SESSION['message']) and $_SESSION['message'] != null) { 
		echo "\n<script>window.onload = alert('".$_SESSION['message']."')</script>\n";
		unset($_SESSION['message']);}
// *** LIBRARIES FOR DISPLAYING DATA
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/menu.php');
	$latteParameters = [ //pole promeny pro latte
		'text' => $text, //textove pole ./custom/text-*.php
		'config' => $config, //skupiny parametry ./inc/func_main.php
	];

// timestamp konvertovan do podoby pro web
function webDate($date) {
	if ($date < "1") { 
		$value = "někdy dávno";
	} else {
		$value = Date ('d. m. Y',$date);
	}
	return $value;
}	
function webDateTime($date) {
	if ($date < "1") { 
		$value = "někdy dávno";
	} else {
		$value = Date ('d. m. Y - H:i:s',$date);
	}
	return $value;
}	


// ziskani autora zaznamu - audit, dashboard, edituser, index, readcase, readperson, readsymbol, tasks
function getAuthor ($recid,$trn) {
	global $database;
	if ($trn==1) {
		$sql_ga="SELECT ".DB_PREFIX."person.name as 'name', ".DB_PREFIX."person.surname as 'surname', ".DB_PREFIX."user.login as 'nick' FROM ".DB_PREFIX."person, ".DB_PREFIX."user WHERE ".DB_PREFIX."user.id=".$recid." AND ".DB_PREFIX."person.id=".DB_PREFIX."user.idperson";
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
		$sql_ga="SELECT ".DB_PREFIX."user.login as 'nick' FROM ".DB_PREFIX."user WHERE ".DB_PREFIX."user.id=".$recid;
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

// funkce pro ukládání fitru do databáza a načítání filtru z databáze        
function custom_Filter ($idtable, $idrecord = 0) {
	global $database,$usrinfo;
	switch ($idtable) {
		case 1: $table="person"; break;
		case 2: $table="group"; break;
		case 3: $table="case"; break;
		case 4: $table="report"; break;
		case 8: $table="user"; break;
		case 9: $table="evilpts"; break;
		case 10: $table="task"; break;
		case 11: $table="audit"; break;
		case 13: $table="search"; break;
		case 14: $table="group".$idrecord; break;
		case 15: $table="p2c"; break;   //person 2 case
		case 16: $table="c2ar"; break;  //case 2 action report
		case 17: $table="p2ar"; break;  //person 2 action report
		case 18: $table="ar2c"; break;  //action report 2 case
		case 19: $table="p2g"; break;   //person 2 group
		case 20: $table="sy2p"; break;  //symbol 2 person
		case 21: $table="sy2c"; break;  //symbol 2 case
		case 22: $table="sy2ar"; break; //symbol 2 action report 
	}
	$sql_cf = "SELECT filter FROM ".DB_PREFIX."user WHERE id = ".$usrinfo['id'];
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
		$sql_scf = "UPDATE ".DB_PREFIX."user SET filter='".$sfilters."' WHERE id=".$usrinfo['id'];
		mysqli_query ($database,$sql_scf);
	// v opačném případě zkontroluj, zda existuje odpovídající filtr v databázi, a pokud ano, načti jej    
	} else {
		if ($res_cf) {
			$rec_cf=mysqli_fetch_assoc ($res_cf);
            $filters = unserialize($rec_cf['filter']);
			if (!empty($filters)) {
				if (array_key_exists($table, $filters)) {
                    $filter = $filters[$table];
                    //print_r($filter);
				}
			}
		}
	}
	return $filter;
}

//show debug bar unless it's a sending a file (picture) to the user
if (substr(basename($_SERVER['REQUEST_URI']), 0, strpos(basename($_SERVER['REQUEST_URI']), '?')) != "getportrait.php" AND substr(basename($_SERVER['REQUEST_URI']), 0, strpos(basename($_SERVER['REQUEST_URI']), '?')) != "getfile.php") { 
	Debugger::barDump($_SESSION,"session");
}

?>