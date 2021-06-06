<?php
/**
 * @SuppressWarnings(PHPMD.Superglobals)
 *
 * @param mixed $lenght
 */

/**
 * password generator [A-Za-z0-9].
 *
 * @param int $lenght lenght of password, default 8
 *
 * @return string randomized string
 */
function randomPassword($lenght = 8): string
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = mb_strlen($alphabet) - 1;
    for ($lenghtTarget = 0; $lenghtTarget < $lenght; ++$lenghtTarget) {
        $randomCharacter = random_int(0, $alphaLength);
        $pass[] = $alphabet[$randomCharacter];
    }

    return implode('', $pass);
}

/**
 * validate email.
 *
 * @param string addr email to verify
 * @param mixed $addr
 *
 * @return bool is it a valid email
 */
function validate_mail($addr): bool
{
    if (!mb_strpos($addr, '@')) {
        return false;
    }
    [$username, $domain] = explode('@', $addr);
    $patternUsername = '^([0-9a-z]+([-|_]?[0-9a-z]+)*)(([-|_]?)\.([-|_]?)[0-9a-z]*([-|_]?[0-9a-z]+)+)*([-|_]?)$';
    $patternDomain = '^([0-9a-z]+([-]?[0-9a-z]+)*)(([-]?)\.([-]?)[0-9a-z]*([-]?[0-9a-z]+)+)*\.[a-z]{2,4}$';
    $matchUsername = mb_ereg($patternUsername, $username);
    $matchDomain = mb_ereg($patternDomain, $domain);

    return $matchUsername && $matchDomain ? true : false;

    //	if (!eregi('^[+]?[a-z0-9]+([-_.]?[a-z0-9]*)*@[a-z0-9]+([-_.]?[a-z0-9])*\.[a-z]{2,4}$',$addr)){
}

/**
 * generate full domain name for this appliacation.
 *
 * @return string protocol://domainname
 */
function siteURL(): string
{
    $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';

    return $protocol.$domainName;
}

$latteParameters['website_link'] = siteURL();
