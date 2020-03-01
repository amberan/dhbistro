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
require_once SERVER_ROOT.'/inc/backup.php';
require_once SERVER_ROOT.'/inc/session.php';
require_once SERVER_ROOT.'/inc/audit_trail.php';
//require_once SERVER_ROOT.'/lib/image.php';
require_once SERVER_ROOT.'/inc/unread.php';
// *** FUNCTIONS for objects
require_once SERVER_ROOT.'/lib/person.php';
require_once SERVER_ROOT.'/lib/news.php';
// *** GENERAL ALERT - to be removed
if (isset($_SESSION['message']) && null !== $_SESSION['message']) {
    echo "\n<script>window.onload = alert('".$_SESSION['message']."')</script>\n";
    unset($_SESSION['message']);
}
require_once SERVER_ROOT.'/inc/menu.php';
$latteParameters['text'] = $text;
$latteParameters['config'] = $config;
if (isset($usrinfo)) {
    $latteParameters['usrinfo'] = $usrinfo;
}


function siteURL()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';

    return $protocol.$domainName;
}

$latteParameters['website_link'] = siteURL();


function latteHeader($latteParameters)
{
    global $latte,$config;
    $latte->render($config['folder_templates'].'header.latte', $latteParameters);
}
function latteFooter($latteParameters)
{
    global $latte,$config;
    $latte->render($config['folder_templates'].'footer.latte', $latteParameters);
    ;
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

/**
* timestamp to date
* @param int date timestamp
* @return string d. m. Y
*/
function webDate($date): string
{
    if ($date < '1') {
        $value = 'někdy dávno';
    } else {
        $value = date('d. m. Y', $date);
    }

    return $value;
}

/**
* timestamp to date and time
* @param int date timestamp
* @return string d. m. Y - H:i:s
*/
function webDateTime($date): string
{
    if ($date < '1') {
        $value = 'někdy dávno';
    } else {
        $value = date('d. m. Y - H:i:s', $date);
    }

    return $value;
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
    $sql_cf = 'SELECT filter FROM '.DB_PREFIX.'user WHERE id = '.$usrinfo['id'];
    $res_cf = mysqli_query($database, $sql_cf);
    $filter = $_REQUEST;
    // pokud přichází nový filtr a nejedná se o zadání úkolu či přidání zlobodů, případně pokud se jedná o konkrétní záznam a je nově filtrovaný,
    // použij nový filtr a ulož ho do databáze
    if ((!empty($filter) && !isset($_POST['inserttask']) && !isset($_POST['addpoints']) && !isset($filter['rid'])) || (isset($filter['sort'], $filter['rid']))) {
        if ($res_cf) {
            $rec_cf = mysqli_fetch_assoc($res_cf);
            $filters = unserialize($rec_cf['filter']);
            $filters[$table] = $filter;
        } else {
            $filters[$table] = $filter;
        }
        $sfilters = serialize($filters);
        $sql_scf = 'UPDATE '.DB_PREFIX."user SET filter='".$sfilters."' WHERE id=".$usrinfo['id'];
        mysqli_query($database, $sql_scf);
    // v opačném případě zkontroluj, zda existuje odpovídající filtr v databázi, a pokud ano, načti jej
    } else {
        if ($res_cf) {
            $rec_cf = mysqli_fetch_assoc($res_cf);
            $filters = unserialize($rec_cf['filter']);
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


/**
* remove diacritics for search purposes
* @param string string
* @return string string without diacritics
*/
function nocs($string): string
{
    $table = [
        'Š' => 'S',
        'š' => 's',
        'Đ' => 'Dj',
        'đ' => 'dj',
        'Ž' => 'Z',
        'ž' => 'z',
        'Č' => 'C',
        'č' => 'c',
        'Ć' => 'C',
        'ć' => 'c',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ý' => 'y',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
        'Ŕ' => 'R',
        'ŕ' => 'r',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'ť' => 't',
        'ů' => 'u',
        'ü' => 'u',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Ť' => 'T',
        'Ů' => 'U',
    ];

    return strtr($string, $table);
}
/**
* password generator [A-Za-z0-9]
* @param integer lenght lenght of password, default 8
* @return string randomized string 
*/
function randomPassword($lenght = 8): string
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = mb_strlen($alphabet) - 1;
    for ($lenghtTarget = 0; $lenghtTarget < $lenght; ++$lenghtTarget) {
        $randomCharacter = rand(0, $alphaLength);
        $pass[] = $alphabet[$randomCharacter];
    }

    return implode('', $pass);
}

/**
* validate email
* @param string addr email to verify
* @return bool is it a valid email
*/
function validate_mail($addr): bool
{
    if (!mb_strpos($addr, '@')) {
        return false;
    } else {
        list($username, $domain) = explode('@', $addr);
        $patternUsername = '^([0-9a-z]+([-|_]?[0-9a-z]+)*)(([-|_]?)\.([-|_]?)[0-9a-z]*([-|_]?[0-9a-z]+)+)*([-|_]?)$';
        $patternDomain = '^([0-9a-z]+([-]?[0-9a-z]+)*)(([-]?)\.([-]?)[0-9a-z]*([-]?[0-9a-z]+)+)*\.[a-z]{2,4}$';
        $matchUsername = mb_ereg($patternUsername, $username);
        $matchDomain = mb_ereg($patternDomain, $domain);

        return $matchUsername && $matchDomain ? true : false;
    }
    //	if (!eregi('^[+]?[a-z0-9]+([-_.]?[a-z0-9]*)*@[a-z0-9]+([-_.]?[a-z0-9])*\.[a-z]{2,4}$',$addr)){
}

//show tracy bar unless it's a sending a file (picture) to the user
if ('getportrait.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?')) &&
    'getfile.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?')) &&
    'file.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?'))) {
    Debugger::barDump($_SESSION, 'session');
}
