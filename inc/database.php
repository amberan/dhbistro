<?php

switch ($_SERVER["SERVER_NAME"]) {
    case 'localhost':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
        $config['verze']=0;
        $config['barva']='local';

	$text['point']='zlobod';
        $text['hlaseniV']='Hlášení';
        $text['hlaseniM']='hlášení';
        $config['debug']=true;
        break;
    case 'www.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
        $config['verze']=1;
        $config['barva']='dh';

	$text['point']='zlobod';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
        break;
    case 'nh.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhbistro';
        $config['verze']=2;
        $config['barva']='nh';

	$text['point']='bludišťák';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
        break;
    case 'test.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='testbistro';
        $config['verze']=3;
        $config['barva']='test';
	$config['debug']=true;

	$text['point']='zlobod';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
        break;
    case 'org.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='orgbistro';
        $config['verze']=4;
        $config['barva']='org';

	$text['point']='zlobod';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
        break;
    case 'enigma.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='enigmabistro';
        $config['verze']=5;
        $config['barva']='enigma';

	$text['point']='zlobod';
	$text['hlaseniV']='Zakázka';
	$text['hlaseniM']='zakázka';
        break;
    case 'nhtest.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['verze']=2;
	$config['barva']='test';
	$config['debug']=true;

	$text['point']='bludišťák';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
        break;
    case 'saga':
        $config['dbuser']='root';
        $config['dbdatabase']='NHBistro';
        $config['verze']=2;
        $config['barva']='local';
	$config['debug']=true;

	$text['point']='bludišťák';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
        break;
    case 'bistro.alembiq.net':
	$config['dbuser']='aliunde';
	$config['dbdatabase']='test';
	$config['verze']=2;
	$config['barva']='test';
	$config['debug']=true;

	$text['point']='bludišťák';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
	break;
    case 'nhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['verze']=2;
        $config['barva']='test';
	$config['debug']=true;

	$text['point']='bludišťák';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
	break;
    case 'dhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='dhtestbistro';
        $config['verze']=3;
        $config['barva']='test';
	$config['debug']=true;

	$text['point']='zlobod';
	$text['hlaseniV']='Hlášení';
	$text['hlaseniM']='hlášení';
}


$lines = file($_SERVER['DOCUMENT_ROOT'].$config['dbpass'],FILE_IGNORE_NEW_LINES) or die("fail pwd");;
$password = $lines[2];

$database = mysqli_connect ('localhost',$config['dbuser'],$password,$config['dbdatabase']) or die (mysqli_connect_errno()." ".mysqli_connect_error());

mysqli_query ($database,"SET NAMES 'utf8'");

//SQL injection  mitigation
foreach ($_REQUEST as $key => $value) {
    $_REQUEST[$key] = mysqli_real_escape_string($database,$value);
}
foreach ($_POST as $key => $value) {
    $_POST[$key] = mysqli_real_escape_string($database,$value);
}
foreach ($_GET as $key => $value) {
    $_GET[$key] = mysqli_real_escape_string($database,$value);
}

?>
