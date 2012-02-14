<?php
header('Content-Type: text/html; charset=utf-8');
$pspell_link = pspell_new("cs",'','','utf-8');

if (!pspell_check($pspell_link, "jarnimi")) {
   $suggestions = pspell_suggest($pspell_link, "jarnimi");

   foreach ($suggestions as $suggestion) {
       echo "Possible spelling: $suggestion<br />";
   }
}
?>