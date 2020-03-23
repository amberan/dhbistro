<?php


session_start();
define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT']);
require_once SERVER_ROOT."/config.php";
require_once SERVER_ROOT.'/vendor/autoload.php';

$latte = new Latte\Engine();
$latte->setTempDirectory($config['folder_cache']);

$URL = explode('/', $_SERVER['REQUEST_URI']); // for THE LOOP
require_once $config['folder_custom'].'text.php'; // defaultni text might be overloaded from inc/platform.php
require_once SERVER_ROOT.'/inc/platform.php';  //platform setup based on server/link
if (null !== $config['custom']) {
    require_once $config['folder_custom'].'/text-'.$config['custom'].'.php';
}

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT, $config['folder_logs']);
require_once SERVER_ROOT.'/inc/database.php';
require_once SERVER_ROOT."/lib/security.php";
require_once SERVER_ROOT."/lib/gui.php";
require_once SERVER_ROOT."/lib/formatter.php";
require_once SERVER_ROOT."/lib/filters.php";
require_once SERVER_ROOT.'/inc/backup.php';
// lib/user
require_once SERVER_ROOT.'/lib/session.php';
require_once SERVER_ROOT.'/inc/audit_trail.php';
require_once SERVER_ROOT.'/lib/image.php';
require_once SERVER_ROOT.'/inc/unread.php';
// *** FUNCTIONS for objects
require_once SERVER_ROOT.'/lib/person.php';
require_once SERVER_ROOT.'/lib/news.php';
require_once SERVER_ROOT.'/inc/menu.php';
$latteParameters['text'] = $text;
$latteParameters['config'] = $config;
if (isset($usrinfo)) {
    $latteParameters['usrinfo'] = $usrinfo;
}

function date_picker($name, $startyear = NULL, $endyear = NULL)
{
    global $aday,$amonth,$ayear,$usrinfo;
    if ($usrinfo['right_org'] == 1) {
        if ($startyear == NULL) {
            $startyear = date("Y") - 40;
        }
    } else {
        if ($startyear == NULL) {
            $startyear = date("Y") - 10;
        }
    }
    if ($endyear == NULL) {
        $endyear = date("Y") + 5;
    }

    $months = array('', 'Leden', 'Únor', 'Březen', 'Duben', 'Květen',
			'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec');

    // roletka dnů
    $html = "<select class=\"day\" name=\"".$name."day\">";
    for ($i = 1;$i <= 31;$i++) {
        $html .= "<option ".(($i == $aday) ? ' selected' : '')." value='$i'>$i</option>";
    }
    $html .= "</select> ";

    // roletka měsíců
    $html .= "<select class=\"month\" name=\"".$name."month\">";

    for ($i = 1;$i <= 12;$i++) {
        $html .= "<option ".(($i == $amonth) ? ' selected' : '')." value='$i'>$months[$i]</option>";
    }
    $html .= "</select> ";

    // roletka let
    $html .= "<select class=\"year\" name=\"".$name."year\">";

    for ($i = $startyear;$i <= $endyear;$i++) {
        $html .= "<option ".(($i == $ayear) ? ' selected' : '')." value='$i'>$i</option>";
    }
    $html .= "</select> ";

    return $html;
}



// ziskani autora zaznamu - audit, dashboard, edituser, index, readcase, readperson, readsymbol, tasks
function getAuthor($recid, $trn)
{
    global $database;
    if (1 === $trn) { //person
        $getAuthorSql = 'SELECT '.DB_PREFIX."person.name as 'name', ".DB_PREFIX."person.surname as 'surname', ".DB_PREFIX."user.login as 'nick' FROM ".DB_PREFIX.'person, '.DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id='.$recid.' AND '.DB_PREFIX.'person.id='.DB_PREFIX.'user.idperson';
        $getAuthorQuery = mysqli_query($database, $getAuthorSql);
        if (mysqli_num_rows($getAuthorQuery)) {
            while ($getAuthorResult = mysqli_fetch_assoc($getAuthorQuery)) {
                $name = stripslashes($getAuthorResult['surname']).', '.stripslashes($getAuthorResult['name']);

                return $name;
            }
        } else {
            $name = 'Uživatel není přiřazen.';

            return $name;
        }
    } else { //user
        $getAuthorSql = 'SELECT '.DB_PREFIX."user.login as 'nick' FROM ".DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id='.$recid;
        $getAuthorQuery = mysqli_query($database, $getAuthorSql);
        if (mysqli_num_rows($getAuthorQuery)) {
            while ($getAuthorResult = mysqli_fetch_assoc($getAuthorQuery)) {
                $name = stripslashes($getAuthorResult['nick']);

                return $name;
            }
        } else {
            $name = 'Neznámo.';

            return $name;
        }
    }
}

// funkce pro ukládání fitru do databáza a načítání filtru z databáze
function custom_Filter($idtable, $idrecord = 0)
{
    global $database,$usrinfo;
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
    }
    $sqlCf = 'SELECT filter FROM '.DB_PREFIX.'user WHERE id = '.$usrinfo['id'];
    $resCf = mysqli_query($database, $sqlCf);
    $filter = $_REQUEST;
    // pokud přichází nový filtr a nejedná se o zadání úkolu či přidání zlobodů, případně pokud se jedná o konkrétní záznam a je nově filtrovaný,
    // použij nový filtr a ulož ho do databáze
    if ((!empty($filter) && !isset($_POST['inserttask']) && !isset($_POST['addpoints']) && !isset($filter['rid'])) || (isset($filter['sort'], $filter['rid']))) {
        if ($resCf) {
            $recCf = mysqli_fetch_assoc($resCf);
            $filters = unserialize($recCf['filter']);
            $filters[$table] = $filter;
        } else {
            $filters[$table] = $filter;
        }
        $sfilters = serialize($filters);
        $sqlScf = 'UPDATE '.DB_PREFIX."user SET filter='".$sfilters."' WHERE id=".$usrinfo['id'];
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

