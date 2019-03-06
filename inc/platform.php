<?php
switch ($_SERVER["SERVER_NAME"]) {
    case 'localhost':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
		$config['verze']=0;
		$config['custom']='DH';
        $config['barva']='local';
        break;
    case 'www.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
        $config['verze']=1;
        $config['barva']='dh';
		$config['custom']='DH';
		break;
    case 'nh.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhbistro';
        $config['verze']=2;
        $config['barva']='nh';
		$config['custom']='DH';
        break;
    case 'test.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='testbistro';
        $config['verze']=3;
        $config['barva']='test';
		$config['custom']='DH';
    break;
    case 'org.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='orgbistro';
        $config['verze']=4;
        $config['barva']='org';
		$config['custom']='DH';
        break;
    case 'enigma.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='enigmabistro';
        $config['verze']=5;
		$config['barva']='enigma';
		$config['custom']='enigma';
        break;
    case 'nhtest.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['verze']=2;
		$config['barva']='test';
		$config['custom']='NH';;
        break;
    case 'saga':
        $config['dbuser']='bistro';
        $config['dbdatabase']='NHBistro';
        $config['verze']=2;
        $config['barva']='local';
		$config['custom']='NH';
	    break;
    case 'bistro.alembiq.net':
		$config['dbuser']='aliunde';
		$config['dbdatabase']='test';
		$config['verze']=2;
		$config['barva']='test';
		$config['custom']='NH';
		break;
    case 'nhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['verze']=2;
        $config['barva']='test';
		$config['custom']='NH';
		break;
    case 'bistro':
        $config['dbuser']='root';
        $config['dbdatabase']='bistro';
        $config['verze']=2;
        $config['barva']='test';
		$config['custom']='NH';
		break;
    case 'dhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='dhtestbistro';
        $config['verze']=3;
        $config['barva']='test';
		$config['custom']='DH';
}

if ($config['custom'] != null) { //prepsani defaultnich textu
	require_once($config['folder_custom'].'/text-'.$config['custom'].'.php');
}

?>
