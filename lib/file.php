<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

/**
 * get fileName based on type.
 *
 * @param string portrait/symbol/attachement/backup
 * @param int fileID
 * @param mixed $type
 * @param mixed $objectId
 */
function fileIdentify($type,$objectId = '0')
{
    global $config,$database,$user;
    switch ($type) {
        case 'portrait':
            $sql = 'SELECT portrait FROM '.DB_PREFIX.'person WHERE '.$user['sqlDeleted'].' AND '.$user['sqlSecret'].' AND id='.$objectId;
            $folder = $config['folder_portrait'];
            break;
        case 'symbol':
            $sql = 'SELECT symbol FROM '.DB_PREFIX.'symbol WHERE '.$user['sqlDeleted'].' AND '.$user['sqlSecret'].' AND id='.$objectId;
            $folder = $config['folder_symbol'];
            break;
        case 'attachement':
            $sql = 'SELECT *, uniquename AS soubor, originalname AS nazev, size FROM '.DB_PREFIX.'file WHERE '.$user['sqlSecret'].' AND id='.$objectId;
            $folder = $config['folder_attachement'];
            break;
        case 'backup':
            $sql = 'SELECT file FROM '.DB_PREFIX.'backup where id='.$objectId;
            $folder = $config['folder_backup'];
            break;
        default:
            break;
    }
    $query = mysqli_query($database,$sql);
    $file = mysqli_fetch_assoc($query);
    //set real path to file, and filename
    $file['fileHash'] = $file['fileName'] = $file['file'] = end(explode("/",$file['file']));
    if ($file['originalname']) {
        $file['fileHash'] = $file['uniquename'];
        $file['fileName'] = $file['originalname'];
    }
    if ($file['portrait']) {
        $file['fileHash'] = $file['fileName'] = $file['portrait'];
        $file['fileName'] = $file['id'];
        $file['mime'] = 'image/jpg';
    }
    if ($file['symbol']) {
        $file['fileHash'] = $file['fileName'] = $file['symbol'];
        $file['fileName'] = $file['id'];
        $file['mime'] = 'image/jpg';
    }
    $file['fullPath'] = $folder.$file['fileHash'];
    //set mimetype
    if (!$file['mime']) {
        $file['mime'] = 'application/octet-stream';
    }
    //get size of file
    $file['fileSize'] = filesize($file['fullPath']);

    return $file;
}

/**
 * show file content.
 *
 * @param array SQL query describing the file
 * @param mixed $object
 */
function fileGet($object): void
{
    $imageMime = ['image/png', 'image/jpeg', 'image/gif', 'image/bmp', 'image/vnd.microsoft.icon', 'image/tiff', 'image/svg+xml'];
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    header('Content-Type: '.$object['mime']);
    if (!in_array($object['mime'],$imageMime, true)) {
        header('Content-Disposition: attachment; filename='.$object['fileName']);
    }
    header('Content-Length: '.$object['filesize']);
    if (file_exists($object['fullPath'])) {
        fpassthru(fopen($object['fullPath'],"r"));
    } else {
        Debugger::log("DEBUG: unable to locate file: ".$object['fullPath']);
    }
}

/**
 * return generic placeholder image based on type.
 *
 * @param string fileType portrait/symbol/logo
 * @param mixed $fileType
 */
function filePlaceholder($fileType = 'logo'): void
{
    switch ($fileType) {
        case 'portrait': $placeholder = SERVER_ROOT."/images/placeholder.jpg";
            break;
        case 'symbol': $placeholder = SERVER_ROOT."/images/nosymbol.jpg";
            break;
        default: $placeholder = SERVER_ROOT."/images/placeholder.jpg";
            break;
        }
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    header('Content-Type: '.mime_content_type($placeholder));
    $placeholderFile = fopen($placeholder,"r");
    fpassthru($placeholderFile);
}
