<?php

// vypis zacatku stranky
function pageStart ($title,$infotext='') {
    global $database,$loggedin, $usrinfo, $mazzarino_version;
      echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Content-language" content="cs" />
  <meta http-equiv="Cache-control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="robots" content="index, follow" />
  <meta name="Author" content="Karel Křemel, David Ambeřan Maleček, Jakub Ethan Kraft" />
  <meta name="Copyright" content="2006 - 2018" />
  
  <title><?php echo (($loggedin)?$usrinfo['login'].' @ ':'')?>BIStro <?php echo $mazzarino_version;?> | <?php echo $title;?></title>
  <meta name="description" content="city larp management system" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  
  <!--[if lt IE 7]><style type="text/css">body {behavior: url('./inc/csshover.htc');}</style><![endif]-->
  <link media="all" rel="stylesheet" type="text/css" href="./inc/styly.css" />
  <link media="print" rel="stylesheet" type="text/css" href="./css/print.css" />
  
  <!-- <script type="text/javascript" src="./js/jquery-min.js"></script> -->
      <script src="http://code.jquery.com/jquery-1.12.2.min.js"></script>
      <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
      <script src="js/jquery.ui.autocomplete.html.js"></script>
      <script type="text/javascript" src="./js/tinymce/tinymce.min.js"></script>
      <script type="text/javascript">
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
      
  <script type="text/javascript" src="./js/mrFixit.js"></script>
</head>
<body>
<div id="wrapper">
<?php
  }
?>