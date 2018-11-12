<?php
function pageStart ($title) {
    global $database, $usrinfo, $config;
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: -1");
?><!DOCTYPE html>
<html lang="cs-CZ">
  <head>
    <meta charset="UTF-8" />
	<meta name="Author" content="Karel Křemel, David Ambeřan Maleček, Jarda Ernedar Fišer, Jakub Ethan Kraft" />
	<meta name="Copyright" content="2006 - 2018" />
	<meta name="description" content="city larp management system" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo (($usrinfo)?$usrinfo['login'].' @ ':'')?>BIStro <?php echo $config['version'];?> | <?php echo $title;?></title>
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<!--[if lt IE 7]><style type="text/css">body {behavior: url('./inc/csshover.htc');}</style><![endif]-->
	<link media="all" rel="stylesheet" type="text/css" href="./css/styly.css" />
	<link media="print" rel="stylesheet" type="text/css" href="./css/print.css" />
</head>
<body>
	<div id="wrapper">
<?php } ?>