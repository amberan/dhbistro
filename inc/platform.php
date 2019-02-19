<?php
switch ($_SERVER["SERVER_NAME"]) {
    case 'localhost':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
        $config['verze']=0;
        $config['barva']='local';
		$config['text'] = 'text-DH.php';
        break;
    case 'www.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
        $config['verze']=1;
        $config['barva']='dh';
		$config['text'] = 'text-DH.php';
		break;
    case 'nh.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhbistro';
        $config['verze']=2;
        $config['barva']='nh';
		$config['text'] = 'text-DH.php';
        break;
    case 'test.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='testbistro';
        $config['verze']=3;
        $config['barva']='test';
		$config['text'] = 'text-DH.php';
    break;
    case 'org.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='orgbistro';
        $config['verze']=4;
        $config['barva']='org';
		$config['text'] = 'text-DH.php';
        break;
    case 'enigma.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='enigmabistro';
        $config['verze']=5;
		$config['barva']='enigma';
		$config['text'] = 'text-enigma.php';
        break;
    case 'nhtest.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['verze']=2;
		$config['barva']='test';
		$config['text'] = 'text-NH.php';
        break;
    case 'saga':
        $config['dbuser']='bistro';
        $config['dbdatabase']='NHBistro';
        $config['verze']=2;
        $config['barva']='local';
		$config['text'] = 'text-NH.php';
	    break;
    case 'bistro.alembiq.net':
		$config['dbuser']='aliunde';
		$config['dbdatabase']='test';
		$config['verze']=2;
		$config['barva']='test';
		$config['text'] = 'text-NH.php';
		break;
    case 'nhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['verze']=2;
        $config['barva']='test';
		$config['text'] = 'text-NH.php';
		break;
    case 'dhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='dhtestbistro';
        $config['verze']=3;
        $config['barva']='test';
		$config['text'] = 'text-DH.php';
}

?>
