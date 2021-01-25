<?php
/**
 * INITialisatin
 * parts commented out until removal od func_main.php.
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
//session_start();
//define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT']);
//require_once SERVER_ROOT."/config.php";
//require_once SERVER_ROOT.'/vendor/autoload.php';
//require_once SERVER_ROOT."/lib/gui.php";
//require_once SERVER_ROOT."/lib/security.php";
//require_once SERVER_ROOT.'/lib/file.php';
//require_once SERVER_ROOT."/lib/image.php";
//require_once SERVER_ROOT."/lib/formatter.php";
//require_once SERVER_ROOT."/lib/filters.php";
//require_once SERVER_ROOT."/lib/session.php";
require_once SERVER_ROOT."/lib/user.php";
require_once SERVER_ROOT."/lib/report.php";
require_once SERVER_ROOT."/lib/case.php";
require_once SERVER_ROOT."/lib/task.php";
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);
//$latte = new Latte\Engine();
//$latte->setTempDirectory($config['folder_cache']);

require_once SERVER_ROOT."/pages/menu.php";

$latteParameters['current_location'] = $_SERVER["SCRIPT_URI"];
$latteParameters['menu'] = $menu;
$latteParameters['menu2'] = $menu2;
$latteParameters['URL'] = $URL;

latteDrawTemplate('headerMD');

//echo "<xmp>"; print_r ($_SERVER); echo "</xmp>";
/*
 * THE LOOP
 * */
if (isset($user)) {
    latteDrawTemplate('menu');
    if ($URL[1] === 'settings') { // SETTINGS
        $latteParameters['title'] = $text['nastaveni'];
        require_once SERVER_ROOT.'/pages/settings.php';
    } elseif ($user['aclRoot'] > 0 and $URL[1] === 'backup') { // BACKUP
        $latteParameters['title'] = $text['zalohovani'];
        require_once SERVER_ROOT.'/pages/backup.php';
    } elseif ($URL[1] === 'users') { // USER MANAGEMENT
        if ($user['aclDirector'] < 1) {
            unauthorizedAccess(8, 1, 0, 0);
        } else {
            $latteParameters['title'] = $text['spravauzivatelu'];
            auditTrail(8, 1, 0);
            if (isset($URL[2]) and $URL[2] === 'new') { // USER MANAGEMENT > ADD USER
                $latteParameters['actions'][] = ["/users", $text['spravauzivatelu']];
                $latteParameters['subtitle'] = $text['vytvorituzivatele'];
                require_once SERVER_ROOT.'/pages/user_add.php';
            } elseif (isset($URL[2]) and $URL[2] === 'edit') { // USER MANAGEMENT >EDIT USER
                $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                $latteParameters['subtitle'] = $text['upravituzivatele'];
                require_once SERVER_ROOT.'/pages/user_edit.php';
            } else { // USER MANAGEMENT > LIST USERS
                $latteParameters['actions'][] = ["/users/new", $text['vytvorituzivatele']];
                require_once SERVER_ROOT.'/pages/users.php';
            }
        }
    } elseif ($URL[1] === 'board') { // BOARD (nastenka)
        auditTrail(6, 1, 0);
        $latteParameters['title'] = $text['nastenka'];
        $latteParameters['actions'][] = ["/news", $text['zobrazitaktuality']];
        if (isset($URL[2]) and $URL[2] === 'edit' and $user['aclDeputy'] < 1 and $user['aclDirector'] < 1) {
            unauthorizedAccess(6, 2, 0, 0);
        } elseif ((isset($URL[2]) and $URL[2] === 'edit' and ($user['aclDeputy'] > 0 or $user['aclDirector'] > 0))) { // BOARD > EDIT
            $latteParameters['subtitle'] = $text['upravitnastenku'];
            $latteParameters['actions'][] = ["/board", $text['zobrazitnastenku']];
            require_once SERVER_ROOT.'/pages/board_edit.php';
        } else { // BOARD > SHOW
            if ($user['aclDeputy'] > 0 or $user['aclDirector'] > 0) {
                $latteParameters['actions'][] = ["/board/edit", $text['upravitnastenku']];
            }
            require_once SERVER_ROOT.'/pages/dashboard.php';
            require_once SERVER_ROOT.'/pages/board.php';
        }
    } elseif ($URL[1] === 'cases') { // CASES (nastenka)
        auditTrail(3, 1, 0);
        $latteParameters['title'] = $text['pripady'];
        $latteParameters['actions'][] = ["/newcase.php", $text['pridatpripad']];
        //TODO view case, edit case
        require_once SERVER_ROOT.'/pages/cases.php';
    } elseif ($URL[1] === 'file') { // GET FILE type:  attachement,portrait,symbol,backup
        //TODO auditTrail
        require_once SERVER_ROOT.'file.php';
    } else { // NEWS - DEFAULT
        auditTrail(5, 1, 0);
        $latteParameters['title'] = 'Aktuality';
        $latteParameters['actions'][] = ["/board", $text['zobrazitnastenku']];
        if (isset($URL[2]) and $URL[2] === 'new' and ($user['aclDeputy'] > 0 or $user['aclDirector'] > 0) and $URL[1] === 'news') { // NEWS > NEW
            $latteParameters['subtitle'] = $text['pridataktualitu'];
            $latteParameters['actions'][] = ["/news", $text['zobrazitaktuality']];
            require_once SERVER_ROOT.'/pages/news_add.php';
        } else { // NEWS > SHOW
            if ($user['aclDeputy'] > 0 or $user['aclDirector'] > 0) {
                $latteParameters['actions'][] = ["/news/new", $text['pridataktualitu']];
            }
            require_once SERVER_ROOT.'/pages/dashboard.php';
            require_once SERVER_ROOT.'/pages/news.php';
        }
    }
} else {
    require_once SERVER_ROOT.'/pages/login.php';
}

//show tracy bar unless it's a sending a file (picture) to the user
if ('getportrait.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?')) and
    'getfile.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?')) and
//    'file.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?')) AND
    $URL[1] !== 'file') {
    Debugger::barDump($_SESSION, 'session');
    Debugger::barDump($latteParameters, 'latte');
    latteDrawTemplate('footerMD');
}
