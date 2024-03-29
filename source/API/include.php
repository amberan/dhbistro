<?php

session_start();

require_once SERVER_ROOT . "/config.php";

require_once SERVER_ROOT . '/vendor/autoload.php';
require_once SERVER_ROOT . '/inc/platform.php'; // server identification/configuration
require_once SERVER_ROOT . '/lib/database.php'; //db connector, injection mitigation

require_once $config['folder_custom'] . 'text.php';
if ($config['themeCustom'] != null) {
    include_once $config['folder_custom'] . '/text-' . $config['themeCustom'] . '.php';
}
require_once SERVER_ROOT . '/lib/person.php';

function session_validation($sessionID)
{
    global $database;
    $usersql = "SELECT *
	FROM " . DB_PREFIX . "user
	WHERE deleted=0 AND suspended=0 AND sid ='" . $sessionID . "' AND user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "'";
    $userresult = mysqli_fetch_assoc(mysqli_query($database, $usersql));
    // TODO doresit TTL ? updatovat session nebo db na posledni akci
    if ($userresult['lastlogon'] + $userresult['timeout'] > time()) {
        return $userresult;
    } else {
        return false;
    }
}
