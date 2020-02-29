<?php
// default
define('DB_PREFIX', 'nw_'); // prefix tabulek
$config['theme_bg'] = 'dark';
$config['theme_navbar'] = 'dark';


switch ($_SERVER["SERVER_NAME"]) {
    case 'bistro':
        $config['dbuser'] = 'bistro';
        $config['dbdatabase'] = 'bistro';
        $config['barva'] = 'local';
        $config['custom'] = 'NH';
        $config['theme_bg'] = 'light';
        $config['theme_navbar'] = 'light';
        break;
    case 'localhost':
        $config['dbuser'] = $config['dbdatabase'] = 'dhbistrocz';
        $config['custom'] = 'DH';
        $config['barva'] = 'local';
        break;
    case 'www.dhbistro.cz':
        $config['dbuser'] = $config['dbdatabase'] = 'dhbistrocz';
        $config['barva'] = 'dh';
        $config['custom'] = 'DH';
        break;
    case 'nh.dhbistro.cz':
        $config['dbuser'] = $config['dbdatabase'] = 'nhbistro';
        $config['barva'] = 'nh';
        $config['custom'] = 'NH';
        break;
    case 'test.dhbistro.cz':
        $config['dbuser'] = $config['dbdatabase'] = 'testbistro';
        $config['barva'] = 'test';
        $config['custom'] = 'DH';
    break;
    case 'org.dhbistro.cz':
        $config['dbuser'] = $config['dbdatabase'] = 'orgbistro';
        $config['barva'] = 'org';
        $config['custom'] = 'DH';
        break;
    case 'enigma.dhbistro.cz':
        $config['dbuser'] = $config['dbdatabase'] = 'enigmabistro';
        $config['barva'] = 'enigma';
        $config['custom'] = 'enigma';
        break;
    case 'nhtest.dhbistro.cz':
        $config['dbuser'] = $config['dbdatabase'] = 'nhtestbistro';
        $config['barva'] = 'test';
        $config['custom'] = 'NH';
        break;
    case 'bistro.alembiq.net':
        $config['dbuser'] = 'alembiq_bistro';
        $config['dbdatabase'] = 'alembiq_bistro';
        $config['barva'] = 'test';
        $config['custom'] = 'NH';
        break;
    case 'nhtestbistro.talmahera.eu':
        $config['dbuser'] = $config['dbdatabase'] = 'nhtestbistro';
        $config['barva'] = 'test';
        $config['custom'] = 'NH';
        break;
    case 'dbp.talmahera.eu':
        $config['dbuser'] = 'dbp';
    $config['dbdatabase'] = 'dbpbistro';
        $config['barva'] = 'test';
        $config['custom'] = 'DB';
        break;
    case 'dhtestbistro.talmahera.eu':
        $config['dbuser'] = $config['dbdatabase'] = 'dhtestbistro';
        $config['barva'] = 'test';
        $config['custom'] = 'DH';
}



?>
