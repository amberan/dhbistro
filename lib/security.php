<?php
/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */

 
/**
* SQL injection mitigation
* @param array array 
* @return array escaped/slashed array
*/
function escape_array($array): array
{
    global $database;
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            escape_array($value);
        } else {
            $array[$key] = mysqli_real_escape_string($database, addslashes( $value));
        }
    }

    return $array;
}

 $_REQUEST = escape_array ($_REQUEST);
 $_POST = escape_array ($_POST);
 $_GET = escape_array ($_GET);

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

/**
 * generate full domain name for this appliacation
 * @return string protocol://domainname
 */
function siteURL(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';

    return $protocol.$domainName;
}

$latteParameters['website_link'] = siteURL();

?>