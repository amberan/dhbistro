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
require_once SERVER_ROOT.'/lib/audit.php';
require_once SERVER_ROOT.'/lib/case.php';
require_once SERVER_ROOT.'/lib/database.php';
require_once SERVER_ROOT.'/lib/backup.php';
require_once SERVER_ROOT.'/lib/file.php';
require_once SERVER_ROOT.'/lib/filters.php';
require_once SERVER_ROOT.'/lib/gui.php';
require_once SERVER_ROOT.'/lib/image.php';
require_once SERVER_ROOT.'/lib/news.php';
require_once SERVER_ROOT.'/lib/person.php';
require_once SERVER_ROOT.'/lib/report.php';
require_once SERVER_ROOT.'/lib/security.php';
require_once SERVER_ROOT.'/lib/update.php';
require_once SERVER_ROOT."/lib/user.php";


$URL = explode('/', $_SERVER['REQUEST_URI']); // for THE LOOP
$URL[0] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";

if (!file_exists($config['platformConfig']) || isset($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])) {
    bistroEnvConvert();
}
require_once $config['platformConfig'];

if (DBTest($configDB)) {
    $database = DBconnect($configDB);
    mysqli_query($database, "SET NAMES 'utf8'");
    if ($database && !sizeof(DBListTables()) > 0 && isset($_POST['backupFile'])) {
        restoreDB($config['folder_backup'].$_POST['backupFile']);
    } elseif ($database && !sizeof(DBListTables()) > 0 && !isset($_POST['backupFile'])) {
        restoreDB();
    } elseif (!$database) {
        bistroEnvConvert();
    }
} else {
    bistroEnvConvert();
}

if (isset($config['themeCustom']) && file_exists($config['folder_custom'].'/text-'.$config['themeCustom'].'.php')) {
    require_once $config['folder_custom'].'/text-'.$config['themeCustom'].'.php';
}

// require_once SERVER_ROOT.'/inc/backup.php';
require_once SERVER_ROOT.'/inc/session.php';
require_once SERVER_ROOT.'/inc/unread.php';
$_REQUEST = escape_array($_REQUEST);
$_POST = escape_array($_POST);
$_GET = escape_array($_GET);
bistroBackup();
/*
 * THE LOOP
 * */
//TODO auditTrail
if ($URL[1] == 'file' && isset($user)) { // GET FILE type:  attachement,portrait,symbol,backup

    require_once SERVER_ROOT.'/file.php';
    exit;
}
//TODO add templates to queue and render at the end of everything
//TODO generic latte for listing objects and sorting
//TODO generic latte for filters
latteDrawTemplate('headerMD');
if (isset($user)) {
    $latteParameters['user'] = $user;
    require_once SERVER_ROOT."/pages/menu.php";
    $latteParameters['menu'] = $menu;
    $latteParameters['menuSub'] = $menuSub;
    $latteParameters['menuLinks'] = $menuLinks;
    latteDrawTemplate('menu');
    if ($URL[1] == 'settings') {
        $latteParameters['title'] = $text['nastaveni'];
        require_once SERVER_ROOT.'/pages/settings.php';
    } elseif ($user['aclRoot'] > 0 && $URL[1] == 'backup') {
        $latteParameters['title'] = $text['zalohovani'];
        require_once SERVER_ROOT.'/pages/backup.php';
    } elseif ($URL[1] == 'persons') {
        $latteParameters['title'] = $text['osoby'];
        $latteParameters['actions'][] = ["/persons", $text['osoby']];
        $latteParameters['actions'][] = ["/newperson.php", $text['pridatosobu']];
        $latteParameters['actions'][] = ["/symbols.php", $text['neprirazenesymboly']];
        $latteParameters['actions'][] = ["/symbol_search.php", $text['vyhledatsymbol']];
        require_once SERVER_ROOT.'/pages/persons.php';
    } elseif ($URL[1] == 'users') {
        if ($user['aclUser'] < 1 && $user['aclGamemaster'] < 1) {
            unauthorizedAccess(8, 1, $URL[3]);
        } else {
            $latteParameters['title'] = $text['spravauzivatelu'];
            if (isset($URL[2]) && $URL[2] == 'new') {
                $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                $latteParameters['subtitle'] = $text['vytvorituzivatele'];
                require_once SERVER_ROOT.'/pages/user_add.php';
            } elseif (isset($URL[2]) && $URL[2] == 'edit') {
                $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                $latteParameters['subtitle'] = $text['upravituzivatele'];
                authorizedAccess(8, 2, $URL[3]);
                require_once SERVER_ROOT.'/pages/user_edit.php';
            } else {
                $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                authorizedAccess(8, 1, 0);
                require_once SERVER_ROOT.'/pages/users.php';
            }
        }
    } elseif ($URL[1] == 'board') {
        $latteParameters['title'] = $text['nastenka'];
        if (isset($URL[2]) && $URL[2] == 'edit' && $user['aclBoard'] < 1) {
            unauthorizedAccess(6, 2, 0);
        } elseif (isset($URL[2]) && $URL[2] == 'edit') {
            $latteParameters['subtitle'] = $text['upravitnastenku'];
            $latteParameters['actions'][] = ["/board", $text['zobrazitnastenku']];
            authorizedAccess(6, 2, 0);
            require_once SERVER_ROOT.'/pages/board_edit.php';
        } else {
            authorizedAccess(6, 1, 0);
            if ($user['aclBoard'] > 0) {
                $latteParameters['actions'][] = ["/board/edit", $text['upravitnastenku']];
            }
            require_once SERVER_ROOT.'/pages/dashboard.php';
            require_once SERVER_ROOT.'/pages/board.php';
        }
    } elseif ($URL[1] == 'cases') {
        authorizedAccess(3, 1, 0);
        $latteParameters['title'] = $text['pripady'];
        $latteParameters['actions'][] = ["/newcase.php", $text['pridatpripad']];
        //TODO view case, edit case, new case
        require_once SERVER_ROOT.'/pages/cases.php';
    } elseif ($URL[1] == 'groups') {
        authorizedAccess(2, 1, 0);
        $latteParameters['title'] = $text['skupiny'];
        $latteParameters['actions'][] = ["/newgroup.php", $text['pridatskupinu']];
        //TODO view group, edit group, new group
        require_once SERVER_ROOT.'/pages/groups.php';
    } elseif ($URL[1] == 'reports') {
        authorizedAccess(4, 1, 0);
        $latteParameters['title'] = $text['hlaseni'];
        if (is_numeric($URL[2])) {
            // $latteParameters['actions'][] = ["/reports/$URL[2]/names", $text['zobrazitjmena']];
            // $latteParameters['actions'][] = ["/reports/$URL[2]/symbols", $text['zobrazitsymboly']];
            // $latteParameters['actions'][] = ["/reports/$URL[2]/notes", $text['zobrazitpoznamky']];
            $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
            if (isset($URL[3]) && $URL[3] == 'edit') {
                $latteParameters['actions'][] = ["/symbols.php", $text['priraditsymboly']];

                require_once SERVER_ROOT.'/pages/report_edit.php';
            } else {
                require_once SERVER_ROOT.'/pages/report_view.php';
            }
        } elseif ($URL[2] == 'new') {
            $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
            require_once SERVER_ROOT.'/pages/report_edit.php';
        } else {
            $latteParameters['actions'][] = ["/reports/new", $text['zalozithlaseni']];
            //TODO view report, edit report, new report
            require_once SERVER_ROOT.'/pages/reports.php';
        }
    } else { // NEWS - DEFAULT
        $latteParameters['title'] = $text['aktuality'];
        if (isset($URL[2]) && $URL[2] == 'new' && ($user['aclNews'] < 1) && $URL[1] == 'news') {
            unauthorizedAccess(5, 3, 0);
        } elseif (isset($URL[2]) && $URL[2] == 'new' && ($user['aclNews'] > 0) && $URL[1] == 'news') { // NEWS > NEW
            $latteParameters['subtitle'] = $text['pridataktualitu'];
            $latteParameters['actions'][] = ["/news", $text['zobrazitaktuality']];
            authorizedAccess(5, 3, 0);
            require_once SERVER_ROOT.'/pages/news_add.php';
        } else { // NEWS > SHOW
            if ($user['aclNews'] > 0) {
                $latteParameters['actions'][] = ["/news/new", $text['pridataktualitu']];
            }
            authorizedAccess(5, 1, 0);
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

    mysqli_close($database);
