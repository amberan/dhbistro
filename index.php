<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);

$latteParameters['current_location'] = $_SERVER["PHP_SELF"];;
$latteParameters['menu'] = $menu;
$latteParameters['menu2'] = $menu2;

//echo "<xmp>"; print_r ($URL); echo "</xmp>";
/**
 * THE LOOP 
 * */
if ($URL[1] == 'settings') { // SETTINGS
    $latteParameters['title'] = $text['nastaveni'];
    require_once ( SERVER_ROOT.'/processing/settings.php');
} elseif ($usrinfo['right_super'] > 0 and $URL[1] == 'backup') { // BACKUP
    $latteParameters['title'] = $text['zalohovani'];
    require_once ( SERVER_ROOT.'/processing/backup.php');
} elseif ($URL[1] == 'users') { // USER MANAGEMENT
    if ($usrinfo['right_power'] < 1) {
        unauthorizedAccess(8, 1, 0, 0);
    } else {
        $latteParameters['title'] = $text['spravauzivatelu'];
        auditTrail(8, 1, 0);
        $latteParameters['actions'][] = array("/users/new", $text['vytvorituzivatele']);
        if (isset($URL[2]) AND $URL[2] == 'new') { // USER MANAGEMENT > ADD USER
            $latteParameters['subtitle'] = $text['vytvorituzivatele'];
            require_once ( SERVER_ROOT.'/processing/user_add.php');
        } elseif (isset($URL[2]) AND $URL[2] == 'edit') { // USER MANAGEMENT >EDIT USER
            $latteParameters['subtitle'] = $text['upravituzivatele'];
            require_once ( SERVER_ROOT.'/processing/user_edit.php');
        } else { // USER MANAGEMENT > LIST USERS
            require_once ( SERVER_ROOT.'/processing/users.php');
        }
    }
} elseif ($URL[1] == 'board') { // BOARD (nastenka)
    auditTrail(6, 1, 0);
    $latteParameters['title'] = $text['nastenka'];
    $latteParameters['actions'][] = array("/news", $text['zobrazitaktuality']);
    $latteParameters['actions'][] = array("/board", $text['zobrazitnastenku']);
    if ($usrinfo['right_power'] > 0) {
        $latteParameters['actions'][] = array("/board/edit", $text['upravitnastenku']);
    }
    if (isset($URL[2]) AND $URL[2] == 'edit' AND $usrinfo['right_power'] < 0) {
        unauthorizedAccess(6, 2, 0, 0);
    } elseif (isset($URL[2]) AND $URL[2] == 'edit' AND $usrinfo['right_power'] > 0) { // BOARD > EDIT
        $latteParameters['subtitle'] = $text['upravitnastenku'];
        require_once ( SERVER_ROOT.'/processing/board_edit.php');
    } else { // BOARD > SHOW
        require_once ( SERVER_ROOT.'/processing/dashboard.php');
        require_once ( SERVER_ROOT.'/processing/board.php');
    }
} else { // NEWS - DEFAULT
    auditTrail(5, 1, 0);
    $latteParameters['title'] = 'Aktuality';
    $latteParameters['actions'][] = array("/news", $text['zobrazitaktuality']);
    $latteParameters['actions'][] = array("/board", $text['zobrazitnastenku']);
    if ($usrinfo['right_power'] > 0) {
        $latteParameters['actions'][] = array("/news/new", $text['pridataktualitu']);
    }
    if (isset($URL[2]) AND $URL[2] == 'new' AND $usrinfo['right_power'] > 0 AND $URL[1] == 'news') { // NEWS > NEW
        $latteParameters['subtitle'] = $text['pridataktualitu'];
        require_once (SERVER_ROOT.'/processing/news_add.php');
    } else { // NEWS > SHOW
        require_once (SERVER_ROOT.'/processing/dashboard.php');
        require_once (SERVER_ROOT.'/processing/news.php');
    }
}

$latte->render($config['folder_templates'].'footerMD.latte', $latteParameters);
