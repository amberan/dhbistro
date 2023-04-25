<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';

if (isset($URL[2])) {
    $fileType = $URL[2];
}
if (isset($fileType)) {
    if (isset($URL[3]) and is_numeric($URL[3])) {
        $fileId = $URL[3];
        $requestedFile = fileIdentify($fileType,$fileId);
    }
    if (isset($requestedFile) && $requestedFile) {
        fileGet($requestedFile);
    } else {
        filePlaceholder($fileType);
    }
}
