<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once SERVER_ROOT.'/vendor/autoload.php';

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latteParameters = [];
require_once $config['folder_custom'].'text.php';
require_once SERVER_ROOT.'/lib/audit_trail.php';
require_once SERVER_ROOT."/lib/case.php";
require_once SERVER_ROOT.'/lib/database.php';
require_once SERVER_ROOT.'/lib/file.php';
require_once SERVER_ROOT.'/lib/filters.php';
require_once SERVER_ROOT.'/lib/formatter.php';
require_once SERVER_ROOT.'/lib/gui.php';
require_once SERVER_ROOT.'/lib/image.php';
require_once SERVER_ROOT.'/lib/news.php';
require_once SERVER_ROOT.'/lib/person.php';
require_once SERVER_ROOT."/lib/report.php";
require_once SERVER_ROOT.'/lib/security.php';
require_once SERVER_ROOT."/lib/task.php";
require_once SERVER_ROOT.'/lib/update.php';
require_once SERVER_ROOT."/lib/user.php";

if (!file_exists($config['platformConfig']) || isset($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])) {
    bistroConvertPlatform();
}
require_once $config['platformConfig'];
if (isset($config['themeCustom'])) {
    require_once $config['folder_custom'].'/text-'.$config['themeCustom'].'.php';
}

if (DBTest($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase'])) {
    $database = mysqli_connect($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase']);
    mysqli_query($database, "SET NAMES 'utf8'");
}

$database = mysqli_connect($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase']) or die($_SERVER["SERVER_NAME"].":".mysqli_connect_errno()." ".mysqli_connect_error());
mysqli_query($database, "SET NAMES 'utf8'");

$URL = explode('/', $_SERVER['REQUEST_URI']); // for THE LOOP
require_once SERVER_ROOT.'/inc/installer.php';
require_once SERVER_ROOT.'/inc/backup.php';
require_once SERVER_ROOT.'/inc/session.php';
require_once SERVER_ROOT.'/inc/unread.php';
$_REQUEST = escape_array($_REQUEST);
$_POST = escape_array($_POST);
$_GET = escape_array($_GET);
require_once SERVER_ROOT."/pages/menu.php";


$latteParameters['current_location'] = $_SERVER["SCRIPT_URI"];
$latteParameters['menu'] = $menu;
$latteParameters['menuSub'] = $menuSub;
$latteParameters['menuLinks'] = $menuLinks;
$latteParameters['URL'] = $URL;
$latteParameters['text'] = $text;
$latteParameters['config'] = $config;
if (isset($user)) {
    $latteParameters['user'] = $user;
}


$latteParameters['text'] = $text;
$latteParameters['config'] = $config;
if (isset($user)) {
    $latteParameters['user'] = $user;
}


//echo "<xmp>"; print_r ($_SERVER); echo "</xmp>";
/*
 * THE LOOP
 * */
if ($URL[1] == 'file' && isset($user)) { // GET FILE type:  attachement,portrait,symbol,backup
    //TODO auditTrail
    require_once SERVER_ROOT.'/file.php';
    exit;
}
latteDrawTemplate('headerMD');
if (isset($user)) {
    latteDrawTemplate('menu');
    if ($URL[1] == 'settings') { // SETTINGS
        $latteParameters['title'] = $text['nastaveni'];
        require_once SERVER_ROOT.'/pages/settings.php';
    } elseif ($user['aclRoot'] > 0 && $URL[1] == 'backup') { // BACKUP
        $latteParameters['title'] = $text['zalohovani'];
        require_once SERVER_ROOT.'/pages/backup.php';
    } elseif ($URL[1] == 'users') { // USER MANAGEMENT
        if ($user['aclUser'] < 1 && $user['aclGamemaster'] < 1) {
            unauthorizedAccess(8, 1, 0, 0);
        } else {
            $latteParameters['title'] = $text['spravauzivatelu'];
            auditTrail(8, 1, 0);
            if (isset($URL[2]) && $URL[2] == 'new') { // USER MANAGEMENT > ADD USER
                $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                $latteParameters['subtitle'] = $text['vytvorituzivatele'];
                require_once SERVER_ROOT.'/pages/user_add.php';
            } elseif (isset($URL[2]) && $URL[2] == 'edit') { // USER MANAGEMENT >EDIT USER
                $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                $latteParameters['subtitle'] = $text['upravituzivatele'];
                require_once SERVER_ROOT.'/pages/user_edit.php';
            } else { // USER MANAGEMENT > LIST USERS
                $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                require_once SERVER_ROOT.'/pages/users.php';
            }
        }
    } elseif ($URL[1] == 'board') { // BOARD
        auditTrail(6, 1, 0);
        $latteParameters['title'] = $text['nastenka'];
        if (isset($URL[2]) && $URL[2] == 'edit' && $user['aclBoard'] < 1) {
            unauthorizedAccess(6, 2, 0, 0);
        } elseif (isset($URL[2]) && $URL[2] == 'edit' && $user['aclBoard'] > 0) { // BOARD > EDIT
            $latteParameters['subtitle'] = $text['upravitnastenku'];
            $latteParameters['actions'][] = ["/board", $text['zobrazitnastenku']];
            require_once SERVER_ROOT.'/pages/board_edit.php';
        } else { // BOARD > SHOW
            if ($user['aclBoard'] > 0) {
                $latteParameters['actions'][] = ["/board/edit", $text['upravitnastenku']];
            }
            require_once SERVER_ROOT.'/pages/dashboard.php';
            require_once SERVER_ROOT.'/pages/board.php';
        }
    } elseif ($URL[1] == 'cases') { // CASES
        auditTrail(3, 1, 0);
        $latteParameters['title'] = $text['pripady'];
        $latteParameters['actions'][] = ["/newcase.php", $text['pridatpripad']];
        //TODO view case, edit case, new case
        require_once SERVER_ROOT.'/pages/cases.php';
    } elseif ($URL[1] == 'groups') { // GROUPS
        auditTrail(3, 1, 0);
        $latteParameters['title'] = $text['skupiny'];
        $latteParameters['actions'][] = ["/newgroup.php", $text['pridatskupinu']];
        //TODO view group, edit group, new group
        require_once SERVER_ROOT.'/pages/groups.php';
    } else { // NEWS - DEFAULT
        auditTrail(5, 1, 0);
        $latteParameters['title'] = 'Aktuality';
        if (isset($URL[2]) && $URL[2] == 'new' && ($user['aclNews'] > 0) && $URL[1] == 'news') { // NEWS > NEW
            $latteParameters['subtitle'] = $text['pridataktualitu'];
            $latteParameters['actions'][] = ["/news", $text['zobrazitaktuality']];
            require_once SERVER_ROOT.'/pages/news_add.php';
        } else { // NEWS > SHOW
            if ($user['aclNews'] > 0) {
                $latteParameters['actions'][] = ["/news/new", $text['pridataktualitu']];
            }
            require_once SERVER_ROOT.'/pages/dashboard.php';
            require_once SERVER_ROOT.'/pages/news.php';
        }
    }
} else {
    require_once SERVER_ROOT.'/pages/login.php';
}

    Debugger::barDump($_SESSION, 'session');
    Debugger::barDump($latteParameters, 'latte');
    latteDrawTemplate('footerMD');
