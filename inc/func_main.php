<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
if (!file_exists($config['platformConfig'])) {
    Header('location: index.php');
    die();
}
require_once SERVER_ROOT.'/vendor/autoload.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);

require_once $config['folder_custom'].'text.php';
require_once $config['platformConfig'];
if (isset($config['themeCustom'])) {
    require_once $config['folder_custom'].'/text-'.$config['themeCustom'].'.php';
}
$URL = explode('/', $_SERVER['REQUEST_URI']);

require_once SERVER_ROOT.'/lib/security.php';
require_once SERVER_ROOT.'/lib/database.php';

if (DBTest($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase'])) {
    $database = mysqli_connect($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase']);
    mysqli_query($database, "SET NAMES 'utf8'");
}

$database = mysqli_connect($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase']) or die($_SERVER["SERVER_NAME"].":".mysqli_connect_errno()." ".mysqli_connect_error());
mysqli_query($database, "SET NAMES 'utf8'");

require_once SERVER_ROOT.'/lib/gui.php';
require_once SERVER_ROOT.'/lib/filters.php';
require_once SERVER_ROOT.'/lib/file.php';
require_once SERVER_ROOT.'/lib/audit_trail.php';
require_once SERVER_ROOT.'/lib/image.php';
require_once SERVER_ROOT.'/lib/person.php';
require_once SERVER_ROOT.'/lib/news.php';
require_once SERVER_ROOT.'/lib/user.php';
require_once SERVER_ROOT.'/inc/backup.php';
require_once SERVER_ROOT.'/inc/session.php';
require_once SERVER_ROOT.'/inc/unread.php';
require_once SERVER_ROOT.'/inc/menu.php';

$_REQUEST = escape_array($_REQUEST);
$_POST = escape_array($_POST);
$_GET = escape_array($_GET);


$latteParameters['text'] = $text;
$latteParameters['config'] = $config;
if (isset($user)) {
    $latteParameters['user'] = $user;
}

// funkce pro ukládání fitru do databáza a načítání filtru z databáze
function custom_Filter($idtable, $idrecord = 0)
{
    global $database,$user;
    switch ($idtable) {
       case 1: $table = 'person';

break;
       case 2: $table = 'group';

break;
       case 3: $table = 'case';

break;
        case 4: $table = 'report';

break;
        case 8: $table = 'user';

break;
        case 9: $table = 'evilpts';

break;
        case 10: $table = 'task';

break;
        case 11: $table = 'audit';

break;
        case 13: $table = 'search';

break;
        case 14: $table = 'group'.$idrecord;

break;
        case 15: $table = 'p2c';

break;   //person 2 case
        case 16: $table = 'c2ar';

break;  //case 2 action report
        case 17: $table = 'p2ar';

break;  //person 2 action report
        case 18: $table = 'ar2c';

break;  //action report 2 case
        case 19: $table = 'p2g';

break;   //person 2 group
        case 20: $table = 'sy2p';

break;  //symbol 2 person
        case 21: $table = 'sy2c';

break;  //symbol 2 case
        case 22: $table = 'sy2ar';

break; //symbol 2 action report
        default:
break;
    }
    $sqlCf = 'SELECT filter FROM '.DB_PREFIX.'user WHERE userId = '.$user['userId'];
    $resCf = mysqli_query($database, $sqlCf);
    $filter = $_REQUEST;
    // pokud přichází nový filtr a nejedná se o zadání úkolu či přidání zlobodů, případně pokud se jedná o konkrétní záznam a je nově filtrovaný, !$_GET['sort']
    // použij nový filtr a ulož ho do databáze && !isset($_GET['sort'])
    if ((!empty($filter)  && !isset($_POST['inserttask']) && !isset($_POST['addpoints']) && !isset($filter['rid'])) || (isset($filter['sort'], $filter['rid']))) {
        if ($resCf) {
            $recCf = mysqli_fetch_assoc($resCf);
            $filters = unserialize($recCf['filter']);
            $filters[$table] = $filter;
        } else {
            $filters[$table] = $filter;
        }
        $sfilters = serialize($filters);
        $sqlScf = 'UPDATE '.DB_PREFIX."user SET filter='".$sfilters."' WHERE userId=".$user['userId'];
        mysqli_query($database, $sqlScf);
    // v opačném případě zkontroluj, zda existuje odpovídající filtr v databázi, a pokud ano, načti jej
    } else {
        if ($resCf) {
            $recCf = mysqli_fetch_assoc($resCf);
            $filters = unserialize($recCf['filter']);
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
