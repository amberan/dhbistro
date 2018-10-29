<?php
function pageStart ($title) {
    global $database, $usrinfo, $config;
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: -1");
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="index, follow" />
        <meta name="Author" content="Karel Křemel, David Ambeřan Maleček, Jakub Ethan Kraft" />
        <meta name="Copyright" content="2006 - 2018" />
        <meta name="description" content="city larp management system" />
        <title><?php echo (($usrinfo)?$usrinfo['login'].' @ ':'')?>BIStro <?php echo $config['version'];?> | <?php echo $title;?></title>
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <!--[if lt IE 7]><style type="text/css">body {behavior: url('<?php echo $_SERVER['DOCUMENT_ROOT']; ?>/inc/csshover.htc');}</style><![endif]-->
        <link media="all" rel="stylesheet" type="text/css" href="./inc/styly.css" />
        <link media="print" rel="stylesheet" type="text/css" href="./css/print.css" />
        <script src="http://code.jquery.com/jquery-1.12.2.min.js"></script>
        <script src="./js/mrFixit.js"></script>
        <script src="./js/tinymce/tinymce.min.js"></script>
        <script>
        tinymce.init({
            selector: "textarea",
            theme: "modern",
            entity_encoding: "raw",
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality template paste textcolor"
            ],
            toolbar: "undo redo | styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor table removeformat",
            menubar: false,
            toolbar_items_size: 'small',
        });
        </script>
    </head>
    <body>
        <div id="wrapper">
<?php } ?>