<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);

/** 
 * get fileName based on type
 * @param string type portrait/symbol/attachement/backup
 * @param int id ID
 */
function fileIdentify($type,$objectId)
{
    global $config,$database;
    //user privilegies > sql modifiers
    switch ($fileType) {
        case 'portrait': echo "profilovka";
            // "SELECT portrait FROM ".DB_PREFIX."person WHERE ".(($user['aclDirector']) ? '' : ' secret=0 AND ')." id=".$_REQUEST['rid']);
            break;
        case 'symbol': echo "symbol";
            // "SELECT symbol FROM ".DB_PREFIX."symbol WHERE ".(($user['aclDirector']) ? '' : ' secret=0 AND ')." id=".$_REQUEST['nrid']
            break;
        case 'attachement': echo "priloha";
            // "SELECT mime, uniquename AS 'soubor', originalname AS 'nazev', size FROM ".DB_PREFIX."file WHERE id=".$_REQUEST['idfile']." AND secret=0"
            break;
        case 'backup': echo "backup";
    }

    return $fileName;
}

/**
 * show file content
 * @param string fileName path to the file
 */
function fileGet($fileName)
{
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    //attachement header('Content-Type: '.$getrec['mime']);
    //attachement header('Content-Type: application/octet-stream');
    //? attachement header('Content-Disposition: attachment; filename="'.$getrec['nazev'].'";');
    //? attachement header('Content-Length: '.$getrec['size']);
    $fileHandle = FOpen ($fileName,"r");
    FPassThru ($fileHandle);
}

/**
 * return generic placeholder image based on type
 * @param string fileType portrait/symbol/logo
 */
function filePlaceholder($fileType = 'logo')
{
    global $config;
    switch ($fileType) {
        case 'portrait': fileGet(SERVER_ROOT."/images/placeholder.jpg");
            break;
        case 'symbol': fileGet(SERVER_ROOT."/images/nosymbol.png");
            break;
        case 'logo': fileGet(SERVER_ROOT."/images/placeholder.jpg");
            break;
    }
}


if (isset($_GET['type'])) {
    $_GET['type'] = $fileType;
}
if (isset($_GET['id'])) {
    $_GET['id'] = $fileId;
}

if (isset($fileId) and isset($fileType)) {
    $fileName = fileIdentify($fileType,$fileId);
    if (file_exists($fileName)) {
        fileGet($fileName);
    }
} else {
    filePlaceholder($fileType);
}
