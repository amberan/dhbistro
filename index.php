<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once SERVER_ROOT.'/vendor/autoload.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);
$latte->addExtension(new Latte\Essential\RawPhpExtension());
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
require_once SERVER_ROOT."/lib/notes.php";
require_once SERVER_ROOT."/lib/symbols.php";

$URL = explode('/', $_SERVER['REQUEST_URI']); // for THE LOOP
$URL[0] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/";

if (!file_exists($config['platformConfig']) || isset($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])) {
    bistroEnvConvert();
}
require_once $config['platformConfig'];

if (DBTest($configDB)) {
    $database = DBconnect($configDB);
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
// ! THE LOOP
if ($URL[1] == 'file' && isset($user)) { // GET FILE type:  attachement,portrait,symbol,backup
    authorizedAccess(13, 1, $URL[2]);
    require_once SERVER_ROOT.'/file.php';
    exit;
}
//TODO add templates to queue and render at the end of everything
//TODO generic latte for listing objects and sorting
//TODO generic latte for filters
latteDrawTemplate('headerMD');
if (isset($user)) {
    $latteParameters['user'] = $user;
    require_once SERVER_ROOT . "/pages/menu.php";
    $latteParameters['menu'] = $menu;
    $latteParameters['menuSub'] = $menuSub;
    $latteParameters['menuLinks'] = $menuLinks;
    latteDrawTemplate('menu');

    switch ($URL[1]) {
        case 'backup':
            if ($user['aclRoot'] < 1) {
                unauthorizedAccess(14, 1, 0);
            } else {
                $latteParameters['title'] = $text['zalohovani'];
                authorizedAccess(14, 1, 0);
                require_once SERVER_ROOT . '/pages/backup.php';
            }
            break;
        case 'board':
            $latteParameters['title'] = $text['nastenka'];
            if (isset($URL[2]) && $URL[2] == 'edit' && $user['aclBoard'] < 1) {
                unauthorizedAccess(6, 2, 0);
            } elseif (isset($URL[2]) && $URL[2] == 'edit') {
                $latteParameters['subtitle'] = $text['upravitnastenku'];
                $latteParameters['actions'][] = ["/board", $text['zobrazitnastenku']];
                authorizedAccess(6, 2, 0);
                require_once SERVER_ROOT . '/pages/board_edit.php';
            } else {
                authorizedAccess(6, 1, 0);
                if ($user['aclBoard'] > 0) {
                    $latteParameters['actions'][] = ["/board/edit", $text['upravitnastenku']];
                }
                require_once SERVER_ROOT . '/pages/dashboard.php';
                require_once SERVER_ROOT . '/pages/board.php';
            }
            break;
        case 'cases':
            authorizedAccess(3, 1, 0);
            $latteParameters['title'] = $text['pripady'];
            $latteParameters['actions'][] = ["/newcase.php", $text['pridatpripad']];
            //TODO view case, edit case, new case
            require_once SERVER_ROOT . '/pages/cases.php';
            break;
        case 'groups':
            authorizedAccess(2, 1, 0);
            $latteParameters['title'] = $text['skupiny'];
            $latteParameters['actions'][] = ["/newgroup.php", $text['pridatskupinu']];
            //TODO view group, edit group, new group
            require_once SERVER_ROOT . '/pages/groups.php';
            break;
        case 'persons':
            authorizedAccess(1, 1, 0);
            $latteParameters['title'] = $text['osoby'];
            $latteParameters['actions'][] = ["/persons", $text['osoby']];
            $latteParameters['actions'][] = ["/newperson.php", $text['pridatosobu']];
            $latteParameters['actions'][] = ["/symbols", $text['neprirazenesymboly']];
            $latteParameters['actions'][] = ["/symbol_search.php", $text['vyhledatsymbol']];
            require_once SERVER_ROOT . '/pages/persons.php';
            break;
        case 'reports':

            $latteParameters['title'] = $text['hlaseni'];
            if (isset($URL[2]) && is_numeric($URL[2])) {
                // $latteParameters['actions'][] = ["/reports/$URL[2]/names", $text['zobrazitjmena']];
                // $latteParameters['actions'][] = ["/reports/$URL[2]/symbols", $text['zobrazitsymboly']];
                // $latteParameters['actions'][] = ["/reports/$URL[2]/notes", $text['zobrazitpoznamky']];
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                if (isset($URL[3]) && $URL[3] == 'edit') {
                    authorizedAccess(4, 2, $URL[2]);
                    //$latteParameters['actions'][] = ["/symbols", $text['priraditsymboly']];
                    $latteParameters['actions'][] = ["/reports/$URL[2]", $text['zobrazitreport']];
                    require_once SERVER_ROOT . '/pages/report_edit.php';
                } else {
                    authorizedAccess(4, 1, $URL[2]);
                    $latteParameters['actions'][] = ["/reports/$URL[2]/edit", $text['upravitreport']];
                    require_once SERVER_ROOT . '/pages/report_view.php';
                }
            } elseif (isset($URL[2]) && $URL[2] == 'new') {
                authorizedAccess(4, 3, 0);
                $latteParameters['actions'][] = ["/reports", $text['vypishlaseni']];
                require_once SERVER_ROOT . '/pages/report_edit.php';
            } else {
                authorizedAccess(4, 1, @$URL[2]);
                $latteParameters['actions'][] = ["/reports/new", $text['zalozithlaseni']];
                require_once SERVER_ROOT . '/pages/reports.php';
            }
            break;
        case 'settings':
            authorizedAccess(15, 1, $user['userId']);
            $latteParameters['title'] = $text['nastaveni'];
            require_once SERVER_ROOT . '/pages/settings.php';
            break;
        case 'symbols':
            authorizedAccess(7, 1, @$_GET['rid']);
            //TODO view report, edit report, new report
            $latteParameters['title'] = $text['symboly'];
            $latteParameters['actions'][] = ["/newsymbol.php", $text['newSymbol']];
            $latteParameters['actions'][] = ["/symbol_search.php", $text['searchSymbol']];
            require_once SERVER_ROOT . '/pages/symbols.php';
            break;
        case 'users':
            if ($user['aclUser'] < 1 && $user['aclGamemaster'] < 1 && $user['aclRoot'] < 1) {
                unauthorizedAccess(8, 1, $URL[3]);
            } else {
                $latteParameters['title'] = $text['spravauzivatelu'];
                if (isset($URL[2]) && $URL[2] == 'new') {
                    authorizedAccess(8, 3, 0);
                    $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                    $latteParameters['subtitle'] = $text['vytvorituzivatele'];
                    require_once SERVER_ROOT . '/pages/user_add.php';
                } elseif (isset($URL[2]) && $URL[2] == 'edit') {
                    authorizedAccess(8, 2, $URL[3]);
                    $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                    $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                    $latteParameters['subtitle'] = $text['upravituzivatele'];
                    require_once SERVER_ROOT . '/pages/user_edit.php';
                } else {
                    authorizedAccess(8, 1, 0);
                    $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                    require_once SERVER_ROOT . '/pages/users.php';
                }
            }
            break;
        case 'news':
        default:
            $latteParameters['title'] = $text['aktuality'];
            if ($user['aclNews'] || $user['aclRoot']) {
                $latteParameters['actions'][] = ["/news", $text['subMenuListNews']];
                $latteParameters['actions'][] = ["/news/0/new", $text['subtitleNewsAdd']];
            }
            authorizedAccess('news', 'read', 0);
            require_once SERVER_ROOT . '/pages/dashboard.php';
            require_once SERVER_ROOT . '/pages/news.php';
            break;
    }
} else {
    require_once SERVER_ROOT.'/pages/login.php';
}

Debugger::barDump($_SESSION, 'session');
Debugger::barDump($latteParameters, 'latte');
latteDrawTemplate('footerMD');

mysqli_close($database);
