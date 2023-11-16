<?php

// default
define('DB_PREFIX', 'nw_'); // prefix tabulek
$config['themeBg'] = 'dark';
$config['themeNavbar'] = 'dark';

switch ($_SERVER["SERVER_NAME"]) {
    case 'bistro':
        $config['dbUser'] = 'bistro';
        $config['dbDatabase'] = 'bistro';
        $config['themeColor'] = 'local';
        $config['themeCustom'] = 'NH';
        $config['themeBg'] = 'warning';
        $config['themeNavbar'] = 'light';
        break;
    case 'dhbistro.cz':
    case 'www.dhbistro.cz':
        $config['dbUser'] = $config['dbDatabase'] = 'dhbistrocz';
        $config['themeColor'] = 'dh';
        $config['themeCustom'] = 'DH';
        $config['themeBg'] = 'secondary';
        break;
    case 'nh.dhbistro.cz':
        $config['dbUser'] = $config['dbDatabase'] = 'nhbistro';
        $config['themeColor'] = 'nh';
        $config['themeCustom'] = 'NH';
        break;
    case 'test.dhbistro.cz':
        $config['dbUser'] = $config['dbDatabase'] = 'testbistro';
        $config['themeColor'] = 'test';
        $config['themeCustom'] = 'DH';
        $config['themeBg'] = 'warning';
        $config['themeNavbar'] = 'light';
        break;
    case 'org.dhbistro.cz':
        $config['dbUser'] = $config['dbDatabase'] = 'orgbistro';
        $config['themeColor'] = 'org';
        $config['themeCustom'] = 'DH';
        $config['themeBg'] = 'dark';
        $config['themeNavbar'] = 'light';
        break;
    case 'enigma.dhbistro.cz':
        $config['dbUser'] = $config['dbDatabase'] = 'enigmabistro';
        $config['themeColor'] = 'enigma';
        $config['themeCustom'] = 'enigma';
        $config['themeBg'] = 'dark';
        $config['themeNavbar'] = 'light';
        break;
    case 'nhtest.dhbistro.cz':
        $config['dbUser'] = $config['dbDatabase'] = 'nhtestbistro';
        $config['themeColor'] = 'test';
        $config['themeCustom'] = 'NH';
        break;
    case 'bistro.alembiq.net':
        $config['dbUser'] = 'alembiq_bistro';
        $config['dbDatabase'] = 'alembiq_bistro';
        $config['themeColor'] = 'test';
        $config['themeCustom'] = 'NH';
        $config['themeBg'] = 'warning';
        $config['themeNavbar'] = 'light';
        break;
    case 'nhtestbistro.talmahera.eu':
        $config['dbUser'] = $config['dbDatabase'] = 'nhtestbistro';
        $config['themeColor'] = 'test';
        $config['themeCustom'] = 'NH';
        break;
    case 'dbp.talmahera.eu':
        $config['dbUser'] = 'dbp';
        $config['dbDatabase'] = 'dbpbistro';
        $config['themeColor'] = 'test';
        $config['themeCustom'] = 'DB';
        $config['themeBg'] = 'dark';
        $config['themeNavbar'] = 'light';
        break;
    case 'dhtestbistro.talmahera.eu':
        $config['dbUser'] = $config['dbDatabase'] = 'dhtestbistro';
        $config['themeColor'] = 'test';
        $config['themeCustom'] = 'DH';
        $config['themeBg'] = 'secondary';
        break;
    default:
        $config['dbUser'] = $config['dbDatabase'] = 'dhbistrocz';
        $config['themeCustom'] = 'DH';
        $config['themeColor'] = 'local';
        $config['themeBg'] = 'warning';
        $config['themeNavbar'] = 'light';
        break;
}
