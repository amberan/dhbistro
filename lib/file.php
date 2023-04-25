<?php

function human_filesize($bytes, $decimals = 2)
{
    $size = 'BKMGTP';
    $factor = floor((mb_strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

/**
 * returns array files and size
 */
function fileList($folder)
{
    $files = (array_diff(scandir($folder), ['.', '..', '.holder', '.htaccess']));
    foreach (($files) as $value) {
        $fileList[] = [
            basename($value),
            human_filesize(filesize($folder.$value)),
        ];
    }
    return $fileList;
}

/**
 * get fileName based on type.
 *
 * @param string portrait/symbol/attachement/backup
 * @param int fileID
 * @param mixed $type
 * @param mixed $objectId
 */
function fileIdentify($type, $objectId = 0)
{
    global $config,$database,$user;
    if (isset($objectId)) {
        switch ($type) {
            case 'portrait':
                $sql = 'SELECT id, portrait, portrait as `file` FROM '.DB_PREFIX.'person WHERE deleted <='.$user['aclRoot'].' AND secret <='.$user['aclSecret'].' AND id='.$objectId;
                $folder = $config['folder_portrait'];
                break;
            case 'symbol':
                $sql = 'SELECT id, symbol, symbol as `file` FROM '.DB_PREFIX.'symbol WHERE deleted <='.$user['aclRoot'].' AND secret <='.$user['aclSecret'].' AND id='.$objectId;
                $folder = $config['folder_symbol'];
                break;
            case 'attachement':
                $sql = 'SELECT *, uniquename AS soubor, originalname AS nazev, size, originalname as `file` FROM '.DB_PREFIX.'file WHERE secret <='.$user['aclSecret'].' AND id='.$objectId;
                $folder = $config['folder_attachement'];
                break;
            case 'backup':
                $sql = 'SELECT `file`  FROM '.DB_PREFIX.'backup where id='.$objectId;
                $folder = $config['folder_backup'];
                break;
            default:
                break;
        }
        $query = mysqli_query($database, $sql);
        $file = mysqli_fetch_assoc($query);
        //set real path to file, and filename
        $tmp = explode("/", $file['file']);
        $file['fileHash'] = $file['fileName'] = $file['file'] = end($tmp);
        if (isset($file['originalname']) && strlen($file['originalname']) && file_exists($folder . $file['soubor'])) {
            $file['fileHash'] = $file['uniquename'];
            $file['fileName'] = $file['originalname'];
        } elseif (isset($file['portrait']) && strlen($file['portrait']) && file_exists($folder . $file['fileHash'])) {
            $file['fileHash'] = $file['fileName'] = $file['portrait'];
            $file['fileName'] = $file['id'];
            $file['mime'] = 'image/jpg';
        } elseif (isset($file['symbol']) && strlen($file['symbol']) && file_exists($folder . $file['fileHash'])) {
            $file['fileHash'] = $file['fileName'] = $file['symbol'];
            $file['fileName'] = $file['id'];
            $file['mime'] = 'image/jpg';
        } else {
            return false;
        }
        $file['fullPath'] = $folder.$file['fileHash'];
        //set mimetype
        if (!isset($file['mime'])) {
            $file['mime'] = 'application/octet-stream';
        }
        //get size of file
        $file['fileSize'] = filesize($file['fullPath']);

        return $file;
    } else {
        return false;
    }
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
    if (!in_array($object['mime'], $imageMime, true)) {
        header('Content-Disposition: attachment; filename='.$object['fileName']);
    }
    header('Content-Length: '.$object['fileSize']);
    if (file_exists($object['fullPath'])) {
        //fpassthru(fopen($object['fullPath'],"r"));
        readfile($object['fullPath']);
    } else {
        DebuggerLog("unable to locate file: ".$object['fullPath'],"W");
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
        case 'symbol': $placeholder = SERVER_ROOT."/images/nosymbol.png";
            break;
        default: $placeholder = SERVER_ROOT."/images/placeholder.jpg";
            break;
    }
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    header('Content-Type: '.mime_content_type($placeholder));
    $placeholderFile = fopen($placeholder, "r");
    fpassthru($placeholderFile);
}
