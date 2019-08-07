<?php
session_start();
define ('DB_PREFIX','nw_'); // table prefix
$config['dbpass'] = "/inc/important.php"; // put db password to second line of this file
$config['folder_portrait'] = "/files/portraits/"; // portrait file folder
$config['folder_symbol'] = "/files/symbols/"; // symbol file folder
$config['folder_logs'] = $_SERVER['DOCUMENT_ROOT'].'/log/'; // logging folder
$config['folder_custom'] = $_SERVER['DOCUMENT_ROOT'].'/custom/'; // instance specifics

require_once($config['folder_custom'].'text.php'); // default text, overloaded below
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/platform.php'); // server identification/configuration
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/database.php'); //db connector, injection mitigation

if ($config['custom'] != null) { //default text overload - by customization
    require_once($config['folder_custom'].'/text-'.$config['custom'].'.php');
}
require_once($_SERVER['DOCUMENT_ROOT'].'/processing/person.php'); //person 

function session_validation($sessionID) {
	global $database;
	$usersql = "SELECT *
	FROM ".DB_PREFIX."user 
	WHERE deleted=0 AND suspended=0 AND sid ='".$sessionID."' AND user_agent='".$_SERVER['HTTP_USER_AGENT']."'";
    $userresult=mysqli_fetch_assoc(mysqli_query($database,$usersql));
    # TODO doresit TTL ? updatovat session nebo db na posledni akci
    if ($userresult['lastlogon'] + $userresult['timeout'] > time()) { 
        return $userresult;
    } else {
        return false;
    }
}
?>