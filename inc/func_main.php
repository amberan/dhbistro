<?php 
$time = $starttime = microtime(true);
$mem = memory_get_usage();
session_start();
//$sid = session_id();
	
//global $database,$text;
	
define ('DB_PREFIX','nw_'); // prefix tabulek
$config['dbpass'] = "/inc/important.php"; // soubor s heslem k databazi
$config['page_prefix']=''; // uri cesta mezi domenou a adresarem bistra
$config['page_free']=array('login.php','logout.php'); // stranky dostupne bez prihlaseni
$config['version']='1.5.7';  // verze bistra
$config['folder_backup'] = "/files/backups/"; // adresar pro generovani zaloh
$config['folder_portrait'] = "/files/portraits/"; // adresar s portrety
$config['folder_symbol'] = "/files/symbols/"; // adresar se symboly
$config['mime-image'] = array("image/jpeg","image/pjpeg", "image/png");
$config['folder_logs'] = $_SERVER['DOCUMENT_ROOT'].'/log/'; // adresar pro tracy logy
$config['folder_custom'] = $_SERVER['DOCUMENT_ROOT'].'/custom/'; // adresar pro customizace (dh, nh, enigma....)
$config['folder_templates'] = $_SERVER['DOCUMENT_ROOT'].'/templates/'; // adresar pro latte templaty
$config['folder_cache'] = $_SERVER['DOCUMENT_ROOT'].'/cache/'; // adresar pro latte cache
$config['text'] = 'text-DH.php'; // defaultni texty - pretizeno hodnotami nactenymi v ramci inc/database.php

// *** TECHNICAL LIBRARIES
	require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
		use Tracy\Debugger;
		Debugger::enable(Debugger::DETECT,$config['folder_logs']);
		$latte = new Latte\Engine;
		$latte->setTempDirectory($config['folder_cache']);
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/platform.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/database.php');
	require_once($config['folder_custom'].$config['text']);
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/backup.php');
		backupDB();
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/session.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/audit_trail.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/image.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/unread.php');
// *** PROCESSING
	require_once($_SERVER['DOCUMENT_ROOT'].'/processing/settings.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/processing/news.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/processing/users.php');
// *** GENERAL ALERT - overit, ze funguje s odlasovanim nahore - asi bude potreba prenaset message prez session destroy
	if (isset($_SESSION['message']) and $_SESSION['message'] != null) { 
		echo "\n<script>window.onload = function(){alert('".$_SESSION['message']."');}</script>\n";
		unset($_SESSION['message']);}
// *** LIBRARIES FOR DISPLAYING DATA
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/footer.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/header.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/inc/menu.php');


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

if (substr(basename($_SERVER['REQUEST_URI']), 0, strpos(basename($_SERVER['REQUEST_URI']), '?')) != "getportrait.php" AND substr(basename($_SERVER['REQUEST_URI']), 0, strpos(basename($_SERVER['REQUEST_URI']), '?')) != "getfile.php") { 
	Debugger::barDump($_SESSION,"session");
}

/* LATTE
$parameters = [
    'text' => $text,
];

$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'test.latte', $parameters);

<h1>{$text[point]|capitalize}</h1>
*/
?>