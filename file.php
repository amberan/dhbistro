<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

if (isset($URL[2])) {
    $fileType = $URL[2];
}
if (isset($URL[3]) and is_numeric($URL[3])) {
    $fileId = $URL[3];
}

if (isset($fileType)) {
    $requestedFile = fileIdentify($fileType,$fileId);

    if (strlen($requestedFile['fileName']) != 0 or strlen($requestedFile['fileHash']) != 0) {
        fileGet($requestedFile);
    } else {
        filePlaceholder($fileType);
    }
}
