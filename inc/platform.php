<?php
switch ($_SERVER["SERVER_NAME"]) {
    case 'localhost':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
		$config['custom']='DH';
        $config['barva']='local';
        break;
    case 'www.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='dhbistrocz';
        $config['barva']='dh';
		$config['custom']='DH';
		break;
    case 'nh.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhbistro';
        $config['barva']='nh';
		$config['custom']='NH';
        break;
    case 'test.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='testbistro';
        $config['barva']='test';
		$config['custom']='DH';
    break;
    case 'org.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='orgbistro';
        $config['barva']='org';
		$config['custom']='DH';
        break;
    case 'enigma.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='enigmabistro';
		$config['barva']='enigma';
		$config['custom']='enigma';
        break;
    case 'nhtest.dhbistro.cz':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
		$config['barva']='test';
		$config['custom']='NH';;
        break;
    case 'bistro':
        $config['dbuser']='bistro';
        $config['dbdatabase']='bistro';
        $config['barva']='local';
		$config['custom']='NH';
	    break;
    case 'bistro.alembiq.net':
		$config['dbuser']='aliunde';
		$config['dbdatabase']='test';
		$config['barva']='test';
		$config['custom']='NH';
		break;
    case 'nhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='nhtestbistro';
        $config['barva']='test';
		$config['custom']='NH';
		break;
    case 'dbp.talmahera.eu':
        $config['dbuser']='dbp';
	$config['dbdatabase']='dbpbistro';
        $config['barva']='test';
		$config['custom']='DB';
		break;
    case 'dhtestbistro.talmahera.eu':
        $config['dbuser']=$config['dbdatabase']='dhtestbistro';
        $config['barva']='test';
		$config['custom']='DH';
}



?>
