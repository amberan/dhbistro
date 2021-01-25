<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT,$config['folder_logs']);

if (isset($_GET['type'])) {
    $_GET['type'] = $fileType;
}
if (isset($_GET['id'])) {
    $_GET['id'] = $fileId;
}
if (isset($fileId) and isset($fileType)) {
    $requestedFile = fileIdentify($fileType,$fileId);
    if ($requestedFile['fileSize'] > 0) {
        fileGet($requestedFile);
    } elseif ($fileType !== 'attachement') {
        filePlaceholder($fileType);
    }
}
