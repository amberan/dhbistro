<?php

session_start();

$config['version'] = '1.6.2';  // verze bistra
define('DB_PREFIX', 'nw_'); // prefix tabulek
$config['dbpass'] = '/inc/important.php'; // soubor s heslem k databazi - na druhem radku
$config['page_prefix'] = ''; // uri cesta mezi domenou a adresarem bistra
$config['page_free'] = ['login.php', 'logout.php']; // stranky dostupne bez prihlaseni
$config['folder_backup'] = '/files/backups/'; // adresar pro generovani zaloh
$config['folder_portrait'] = '/files/portraits/'; // adresar s portrety
$config['folder_symbol'] = '/files/symbols/'; // adresar se symboly
$config['mime-image'] = ['image/jpeg', 'image/pjpeg', 'image/png'];
$config['folder_logs'] = $_SERVER['DOCUMENT_ROOT'].'/log/'; // adresar pro tracy logy
$config['folder_custom'] = $_SERVER['DOCUMENT_ROOT'].'/custom/'; // adresar pro customizace (dh, nh, enigma....)
$config['folder_templates'] = $_SERVER['DOCUMENT_ROOT'].'/templates/'; // adresar pro latte templaty
$config['folder_cache'] = $_SERVER['DOCUMENT_ROOT'].'/cache/'; // adresar pro latte cache
require_once $config['folder_custom'].'text.php'; // defaultni texty - nasledne pretizeno hodnotami nactenymi v ramci inc/database.php
$URL = explode('/', $_SERVER['REQUEST_URI']);

// *** TECHNICAL LIBRARIES
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/platform.php';
    if (null !== $config['custom']) { //prepsani defaultnich textu
        require_once $config['folder_custom'].'/text-'.$config['custom'].'.php';
    }
    require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
        use Tracy\Debugger;

        Debugger::enable(Debugger::DETECT, $config['folder_logs']);
        //Debugger::log("alert: ".$_SESSION['message']);
        $latte = new Latte\Engine();
        $latte->setTempDirectory($config['folder_cache']);
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/database.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/backup.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/session.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/audit_trail.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/image.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/unread.php';
// *** PROCESSING
    require_once $_SERVER['DOCUMENT_ROOT'].'/processing/person.php'; //operace s objektem osoby
    require_once $_SERVER['DOCUMENT_ROOT'].'/processing/news.php';
// *** GENERAL ALERT - overit, ze funguje s odlasovanim nahore - asi bude potreba prenaset message prez session destroy
    if (isset($_SESSION['message']) && null !== $_SESSION['message']) {
        echo "\n<script>window.onload = alert('".$_SESSION['message']."')</script>\n";
        unset($_SESSION['message']);
    }
// *** LIBRARIES FOR DISPLAYING DATA
    require_once $_SERVER['DOCUMENT_ROOT'].'/inc/menu.php';
    $latteParameters['text'] = $text;
    $latteParameters['config'] = $config;
    if (isset($usrinfo)) {
        $latteParameters['usrinfo'] = $usrinfo;
    }

// timestamp konvertovan do podoby pro web
function webDate($date)
{
    if ($date < '1') {
        $value = 'někdy dávno';
    } else {
        $value = date('d. m. Y', $date);
    }

    return $value;
}
function webDateTime($date)
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
    if (1 === $trn) {
        $sql_ga = 'SELECT '.DB_PREFIX."person.name as 'name', ".DB_PREFIX."person.surname as 'surname', ".DB_PREFIX."user.login as 'nick' FROM ".DB_PREFIX.'person, '.DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id='.$recid.' AND '.DB_PREFIX.'person.id='.DB_PREFIX.'user.idperson';
        $res_ga = mysqli_query($database, $sql_ga);
        if (mysqli_num_rows($res_ga)) {
            while ($rec_ga = mysqli_fetch_assoc($res_ga)) {
                $name = stripslashes($rec_ga['surname']).', '.stripslashes($rec_ga['name']);

                return $name;
            }
        } else {
            $name = 'Uživatel není přiřazen.';

            return $name;
        }
    } else {
        $sql_ga = 'SELECT '.DB_PREFIX."user.login as 'nick' FROM ".DB_PREFIX.'user WHERE '.DB_PREFIX.'user.id='.$recid;
        $res_ga = mysqli_query($database, $sql_ga);
        if (mysqli_num_rows($res_ga)) {
            while ($rec_ga = mysqli_fetch_assoc($res_ga)) {
                $name = stripslashes($rec_ga['nick']);

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

function nocs($pol)
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

    return strtr($pol, $table);
}

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = mb_strlen($alphabet) - 1;
    for ($i = 0; $i < 8; ++$i) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }

    return implode('', $pass);
}

function check_mail($addr)
{
    if (!mb_strpos($addr, '@')) {
        return false;
    }
    list($local, $domain) = explode('@', $addr);
    $pattern_local = '^([0-9a-z]+([-|_]?[0-9a-z]+)*)(([-|_]?)\.([-|_]?)[0-9a-z]*([-|_]?[0-9a-z]+)+)*([-|_]?)$';
    $pattern_domain = '^([0-9a-z]+([-]?[0-9a-z]+)*)(([-]?)\.([-]?)[0-9a-z]*([-]?[0-9a-z]+)+)*\.[a-z]{2,4}$';
    $match_local = eregi($pattern_local, $local);
    $match_domain = eregi($pattern_domain, $domain);

    return $match_local && $match_domain ? true : false;
    //	if (!eregi('^[+]?[a-z0-9]+([-_.]?[a-z0-9]*)*@[a-z0-9]+([-_.]?[a-z0-9])*\.[a-z]{2,4}$',$addr)){
}

//show debug bar unless it's a sending a file (picture) to the user
if ('getportrait.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?')) && 'getfile.php' !== mb_substr(basename($_SERVER['REQUEST_URI']), 0, mb_strpos(basename($_SERVER['REQUEST_URI']), '?'))) {
    Debugger::barDump($_SESSION, 'session');
}
