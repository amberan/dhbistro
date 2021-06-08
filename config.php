<?php

$config['version'] = '1.8.0';  // version
$config['dbpass'] = SERVER_ROOT.'/inc/important.php'; // database password - second line of the file
$config['page_prefix'] = ''; // uri cesta mezi domenou a adresarem bistra
$config['page_free'] = ['', 'favicon.ico']; // to be removed
$config['folder_backup'] = SERVER_ROOT.'/files/backups/';
$config['folder_portrait'] = SERVER_ROOT.'/files/portraits/';
$config['folder_symbol'] = SERVER_ROOT.'/files/symbols/';
$config['folder_attachement'] = SERVER_ROOT.'/files/';
$config['folder_logs'] = SERVER_ROOT.'/log/';
$config['folder_custom'] = SERVER_ROOT.'/custom/'; // customisations (dh, nh, enigma....)
$config['folder_templates'] = SERVER_ROOT.'/templates/'; // Latte templates
$config['folder_cache'] = SERVER_ROOT.'/cache/'; // Latte cache
$config['mime-image'] = ['image/jpeg', 'image/pjpeg', 'image/png'];
