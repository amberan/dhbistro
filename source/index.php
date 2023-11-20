<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
require_once SERVER_ROOT . 'vendor/autoload.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DEVELOPMENT, $config['folder_logs']);

$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latte->addExtension(new Latte\Essential\RawPhpExtension());
$latteParameters = [];
require_once $config['folder_custom'] . 'text.php';
require_once SERVER_ROOT . 'lib/audit.php';
require_once SERVER_ROOT . 'lib/case.php';
require_once SERVER_ROOT . 'lib/database.php';
require_once SERVER_ROOT . 'lib/backup.php';
require_once SERVER_ROOT . 'lib/file.php';
require_once SERVER_ROOT . 'lib/filters.php';
require_once SERVER_ROOT . 'lib/gui.php';
require_once SERVER_ROOT . 'lib/image.php';
require_once SERVER_ROOT . 'lib/news.php';
require_once SERVER_ROOT . 'lib/person.php';
require_once SERVER_ROOT . 'lib/report.php';
require_once SERVER_ROOT . 'lib/security.php';
require_once SERVER_ROOT . 'lib/update.php';
require_once SERVER_ROOT . "lib/user.php";
require_once SERVER_ROOT . "lib/notes.php";
require_once SERVER_ROOT . "lib/symbols.php";

$URL = explode('/', $_SERVER['REQUEST_URI']); // for THE LOOP
$URL[0] = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . "/";

if (!file_exists($config['platformConfig']) || isset($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])) {
    // no .env.php file OR form posted
    bistroEnvConvert();
}
require_once $config['platformConfig'];

if (isset($configDB) && DBTest($configDB)) {
    $database = DBconnect($configDB);
    if ($database && !sizeof(DBListTables()) > 0 && isset($_POST['backupFile']) && strlen($_POST['backupFile']) > 0) {
        restoreDB($_POST['backupFile']);
    } elseif ($database && !sizeof(DBListTables()) > 0) {
        restoreDB();
    } elseif (!$database) {
        bistroEnvConvert();
    }
} else {
    bistroEnvConvert();
}

if (isset($config['themeCustom']) && file_exists($config['folder_custom'] . '/text-' . $config['themeCustom'] . '.php')) {
    require_once $config['folder_custom'] . '/text-' . $config['themeCustom'] . '.php';
}

require_once SERVER_ROOT . 'inc/session.php';
require_once SERVER_ROOT . 'inc/unread.php';
$_REQUEST = escape_array($_REQUEST);
$_POST = escape_array($_POST);
$_GET = escape_array($_GET);
bistroBackup();
// ! THE LOOP
if ($URL[1] == 'file' && isset($user)) { // GET FILE type:  attachement,portrait,symbol,backup
    authorizedAccess('file', 'read', $URL[2]);
    require_once SERVER_ROOT . 'pages/file.php';
    exit;
}
//TODO add templates to queue and render at the end of everything #306
//TODO generic latte for listing objects and sorting
//TODO generic latte for filters #307 #296
latteDrawTemplate('headerMD');
if (isset($user)) {
    $latteParameters['user'] = $user;
    require_once SERVER_ROOT . "pages/menu.php";
    $latteParameters['menu'] = $menu;
    $latteParameters['menuSub'] = $menuSub;
    $latteParameters['menuLinks'] = $menuLinks;
    latteDrawTemplate('menu');

    switch ($URL[1]) {
        case 'search':
            $latteParameters['title'] = 'Vyhledávání';
            authorizedAccess('search', 'read', 0);
            $latteParameters['actions'][] = ["/symbol_search.php", $text['searchSymbol']];
            require_once SERVER_ROOT . 'pages/search.php';
            break;
        case 'backup':
            $latteParameters['title'] = $text['menuBackups'];
            if ($user['aclRoot']) {
                authorizedAccess('backup', 'read', 0);
                require_once SERVER_ROOT . 'pages/backup.php';
            } else {
                unauthorizedAccess('backup', 'read', 0);
            }
            break;
        case 'board':
            $latteParameters['title'] = $text['menuDashboard'];
            if (isset($URL[3]) && $user['aclBoard'] < 1) {
                unauthorizedAccess('dashboard', 'edit', 0);
            } elseif (isset($URL[2]) && $URL[2] == 'edit') {
                authorizedAccess('dashboard', 'edit', 0);
                $latteParameters['subtitle'] = $text['subMenuActionDashboardEdit'];
                $latteParameters['actions'][] = ["/board", $text['zobrazitnastenku']];
                require_once SERVER_ROOT . 'pages/board_edit.php';
            } else {
                authorizedAccess('dashboard', 'read', 0);
                if ($user['aclBoard'] > 0) {
                    $latteParameters['actions'][] = ["/board/edit", $text['subMenuActionDashboardEdit']];
                }
                require_once SERVER_ROOT . 'pages/dashboard.php';
                require_once SERVER_ROOT . 'pages/board.php';
            }
            break;
        case 'cases':
            $latteParameters['title'] = $text['menuCases'];
            authorizedAccess('case', 'read', 0);
            if ($user['aclCase']) {
                $latteParameters['actions'][] = ["/newcase.php", $text['pridatpripad']];
            }
            //TODO view case, edit case, new case
            require_once SERVER_ROOT . 'pages/cases.php';
            break;
        case 'groups':
            $latteParameters['title'] = $text['menuGroups'];
            authorizedAccess('group', 'read', 0);
            if ($user['aclGroup']) {
                $latteParameters['actions'][] = ["/newgroup.php", $text['pridatskupinu']];
            }
            //TODO view group, edit group, new group
            require_once SERVER_ROOT . 'pages/groups.php';
            break;
        case 'persons':
            $latteParameters['title'] = $text['menuPersons'];
            authorizedAccess('person', 'read', 0);
            $latteParameters['actions'][] = ["/persons", $text['menuPersons']];
            if ($user['aclPerson']) {
                $latteParameters['actions'][] = ["/newperson.php", $text['pridatosobu']];
            }
            require_once SERVER_ROOT . 'pages/persons.php';
            break;
        case 'reports':
            $latteParameters['title'] = $text['menuReports'];
            if (isset($URL[2],$URL[3]) && is_numeric($URL[2]) && $URL[3] == 'edit' && $user['aclReport']) {
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                $latteParameters['actions'][] = ["/reports/$URL[2]", $text['zobrazitreport']];
                require_once SERVER_ROOT . 'pages/report_edit.php';
            } elseif (isset($URL[2],$URL[3]) && is_numeric($URL[2]) && $URL[3] == 'link' && $user['aclReport']) {
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                $latteParameters['actions'][] = ["/reports/$URL[2]", $text['zobrazitreport']];
                $latteParameters['actions'][] = ["/reports/$URL[2]/edit", $text['upravitreport']];
            // TODO linking
            } elseif (isset($URL[2]) && $URL[2] == 0 && $user['aclReport']) {
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                $latteParameters['actions'][] = ["/reports/$URL[2]", $text['zobrazitreport']];
                require_once SERVER_ROOT . 'pages/report_edit.php';
            } elseif (isset($URL[2],$URL[3]) && is_numeric($URL[2]) && $URL[3] == 'edit' && $user['aclReport']) {
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                $latteParameters['actions'][] = ["/reports/$URL[2]", $text['zobrazitreport']];
                require_once SERVER_ROOT . 'pages/report_view.php';
            } elseif (isset($URL[2]) && is_numeric($URL[2]) && !isset($URL[3])) {
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                if ($user['aclReport']) {
                    $latteParameters['actions'][] = ["/reports/0", $text['zalozithlaseni']];
                    $latteParameters['actions'][] = ["/reports/$URL[2]/edit", $text['upravitreport']];
                }
                require_once SERVER_ROOT . 'pages/report_view.php';
            } else { //also archive/unarchive/delete/restore
                if ($user['aclReport']) {
                    $latteParameters['actions'][] = ["/reports/0", $text['zalozithlaseni']];
                }
                require_once SERVER_ROOT . 'pages/reports.php';
            }
            break;
        case 'settings':
            $latteParameters['title'] = $text['menuSettings'];
            authorizedAccess('settings', 'read', $user['userId']);
            require_once SERVER_ROOT . 'pages/settings.php';
            break;
        case 'symbols':
            $latteParameters['title'] = $text['menuSymbols'];
            authorizedAccess('symbol', 'read', @$_GET['rid']);
            //TODO view report, edit report, new report
            if ($user['aclSymbol']) {
                $latteParameters['actions'][] = ["/newsymbol.php", $text['newSymbol']];
            }
            $latteParameters['actions'][] = ["/symbol_search.php", $text['searchSymbol']];
            require_once SERVER_ROOT . 'pages/symbols.php';
            break;
        case 'users':
            if ($user['aclUser'] || $user['aclGamemaster'] || $user['aclRoot']) {
                $latteParameters['title'] = $text['menuUsers'];
                if (isset($URL[2]) && $URL[2] == 'new') {
                    authorizedAccess('user', 'new', 0);
                    $latteParameters['actions'][] = ["/users", $text['menuUsers']];
                    $latteParameters['subtitle'] = $text['subMenuActionUsersAdd'];
                    require_once SERVER_ROOT . 'pages/user_add.php';
                } elseif (isset($URL[2]) && $URL[2] == 'edit') {
                    authorizedAccess('user', 'edit', $URL[3]);
                    $latteParameters['actions'][] = ["/users", $text['menuUsers']];
                    $latteParameters['actions'][] = ["/users/new", $text['subMenuActionUsersAdd']];
                    $latteParameters['subtitle'] = $text['subMenuActionUsersEdit'];
                    require_once SERVER_ROOT . 'pages/user_edit.php';
                } else {
                    authorizedAccess('user', 'read', 0);
                    $latteParameters['actions'][] = ["/users/new", $text['subMenuActionUsersAdd']];
                    require_once SERVER_ROOT . 'pages/users.php';
                }
            } else {
                unauthorizedAccess('user', 'read', $URL[3]);
            }
            break;
        case 'news':
        default:
            $latteParameters['title'] = $text['menuNews'];
            if ($user['aclNews'] || $user['aclRoot']) {
                $latteParameters['actions'][] = ["/news", $text['subMenuActionNewsList']];
                $latteParameters['actions'][] = ["/news/0/new", $text['subMenuActionNewsAdd']];
            }
            authorizedAccess('news', 'read', 0);
            require_once SERVER_ROOT . 'pages/dashboard.php';
            require_once SERVER_ROOT . 'pages/news.php';
            break;
    }
} else {
    require_once SERVER_ROOT . 'pages/login.php';
}

DebuggerDump('session', $_SESSION);
DebuggerDump('latte', $latteParameters);
latteDrawTemplate('footerMD');

mysqli_close($database);
