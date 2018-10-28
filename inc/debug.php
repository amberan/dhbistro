<?php


function xmp($object) {
    echo "<xmp>\n";
    $return = print_r ($object);
    echo "\n</xmp>\n";
    return $return;
}



?>