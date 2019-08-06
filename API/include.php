<?php
session_start();
define ('DB_PREFIX','nw_'); // prefix tabulek
$config['dbpass'] = "/inc/important.php"; // soubor s heslem k databazi - na druhem radku
$config['folder_portrait'] = "/files/portraits/"; // adresar s portrety
$config['folder_symbol'] = "/files/symbols/"; // adresar se symboly
$config['folder_logs'] = $_SERVER['DOCUMENT_ROOT'].'/log/'; // adresar pro tracy logy

require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/platform.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/database.php'); #contains SQL injection mitigation

require_once($_SERVER['DOCUMENT_ROOT'].'/processing/person.php'); //operace s objektem osoby

function session_validation($sessionID) {
	global $database;
	$usersql = "SELECT *
	FROM ".DB_PREFIX."users 
	WHERE deleted=0 AND suspended=0 AND sid ='".$sessionID."' AND user_agent='".$_SERVER['HTTP_USER_AGENT']."'";
    $userresult=mysqli_fetch_assoc(mysqli_query($database,$usersql));
    # TODO doresit TTL 
    if ($userresult['lastlogin'] + $userresult['timeout'] < time()) { 
        return $userresult;
    } else {
        return false;
    }
}
?>