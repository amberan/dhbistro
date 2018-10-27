<?php

define ('DB_PREFIX','nw_'); //prefix tabulek
$dbpasswordfile = $_SERVER['DOCUMENT_ROOT']."/inc/important.php";    // soubor s heslem k databazi
  
switch ($_SERVER["SERVER_NAME"]) {
    case 'localhost':
        $dbusr=$dbname='dhbistrocz';
        $verze=0;
        $point='zlobod';
        $barva='local';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
        break;
    case 'www.dhbistro.cz':
        $dbusr=$dbname='dhbistrocz';
        $verze=1;
        $point='zlobod';
        $barva='dh';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
        break;
    case 'nh.dhbistro.cz':
        $dbusr=$dbname='nhbistro';
        $verze=2;
        $point='bludišťák';
        $barva='nh';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
        break;
    case 'test.dhbistro.cz':
        $dbusr=$dbname='testbistro';
        $verze=3;
        $point='zlobod';
        $barva='test';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
        break;
    case 'org.dhbistro.cz':
        $dbusr=$dbname='orgbistro';
        $verze=4;
        $point='zlobod';
        $barva='org';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
        break;
    case 'enigma.dhbistro.cz':
        $dbusr=$dbname='enigmabistro';
        $verze=5;
        $point='zlobod';
        $barva='enigma';
                $hlaseniV='Zakázka';
                $hlaseniM='zakázka';
        break;
    case 'nhtest.dhbistro.cz':
        $dbusr=$dbname='nhtestbistro';
        $verze=2;
        $point='bludišťák';
        $barva='test';
                $hlaseniV='Hlášení';
                $hlaseniM='hlášení';
        break;
    case 'saga':
        $dbusr='root';
        $dbname='NHBistro';
        $verze=2;
        $point='bludišťák';
        $barva='nh';
            $hlaseniV='Hlášení';
            $hlaseniM='hlášení';
          break;
    case 'nhtestbistro.talmahera.eu':
        $dbusr=$dbname='nhtestbistro';
        $verze=2;
        $point='bludišťák';
        $barva='nh';
            $hlaseniV='Hlášení';
            $hlaseniM='hlášení';
}
    
$lines = file($dbpasswordfile,FILE_IGNORE_NEW_LINES) or die("fail pwd");;
$password = $lines[2];
$database = mysqli_connect ('localhost',$dbusr,$password,$dbname) or die (mysqli_connect_errno()." ".mysqli_connect_error());
  
mysqli_query ($database,"SET NAMES 'utf8'");

?>
