<?php

use Tracy\Debugger;

function DebuggerDump($name,$variable)
{
    Debugger::barDump($variable, $name);
}

function DebuggerLog($value,$logLevel = null)
{
    global $config;
    $logLevelDefinition = [
        'E' => 'Error',
        'W' => 'Warning',
        'D' => 'Debug',
    ];
    if (!isset($config['logLevel'])) {
        $config['logLevel'] = ['E', 'W'];
    }
    if (in_array($logLevel,$config['logLevel'])) {
        Debugger::log($config['version'].": ".$logLevelDefinition[$logLevel]." ".$value);
    }
}

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

    //    if (!eregi('^[+]?[a-z0-9]+([-_.]?[a-z0-9]*)*@[a-z0-9]+([-_.]?[a-z0-9])*\.[a-z]{2,4}$',$addr)){
}

/**
 * SQL injection mitigation.
 *
 * @param array array
 * @param mixed $array
 *
 * @return array escaped/slashed array
 */
function escape_array($array): array
{
    global $database;
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            escape_array($value);
        } else {
            $array[$key] = mysqli_real_escape_string($database, addslashes($value));
        }
    }

    return $array;
}
