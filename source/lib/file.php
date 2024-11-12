<?php

/**
 * Converts bytes to a human-readable file size.
 *
 * @param  int    $bytes    the number of bytes to convert
 * @param  int    $decimals the number of decimal places to include in the result
 * @return string the human-readable file size
 */
function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor(log($bytes, 1024));

    if ($factor >= count($size)) {
        $factor = count($size) - 1;
    }

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . $size[$factor];
}

/**
 * Returns an array of files and their sizes in a specified folder.
 *
 * @param  string $folder the path to the folder to scan
 * @return array  an array of files and their sizes
 */ function fileList($folder)
{
    $files = (array_diff(scandir($folder), ['.', '..', '.holder', '.htaccess']));
    $fileList = [];
    foreach (($files) as $value) {
        $fileList[] = [
            basename($value),
            human_filesize(filesize($folder . $value)),
        ];
    }

    return $fileList;
}

/**
 * Identifies a file based on its type and object ID.
 *
 * @param  string      $type     the type of file (portrait/symbol/attachement/backup)
 * @param  int         $objectId the ID of the object associated with the file
 * @return array|false the file information as an associative array, or false if the file is not found
 */ function fileIdentify($type, $objectId = 0)
{
    global $config,$database,$user;
    switch ($type) {
        case 'portrait':
            $sql = 'SELECT id, portrait, portrait as `file` FROM ' . DB_PREFIX . 'person WHERE deleted <=' . $user['aclRoot'] . ' AND secret <=' . $user['aclSecret'] . ' AND id=' . $objectId;
            $folder = $config['folder_portrait'];

            break;
        case 'symbol':
            $sql = 'SELECT id, symbol, symbol as `file` FROM ' . DB_PREFIX . 'symbol WHERE deleted <=' . $user['aclRoot'] . ' AND secret <=' . $user['aclSecret'] . ' AND id=' . $objectId;
            $folder = $config['folder_symbol'];

            break;
        case 'attachement':
            $sql = 'SELECT *, uniquename AS soubor, originalname AS nazev, size, originalname as `file` FROM ' . DB_PREFIX . 'file WHERE secret <=' . $user['aclSecret'] . ' AND id=' . $objectId;
            $folder = $config['folder_attachement'];

            break;
        case 'backup':
            $sql = 'SELECT `file`  FROM ' . DB_PREFIX . 'backup where id=' . $objectId;
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
    $file['fullPath'] = $folder . $file['fileHash'];
    //set mimetype
    if (!isset($file['mime'])) {
        $file['mime'] = 'application/octet-stream';
    }
    //get size of file
    $file['fileSize'] = filesize($file['fullPath']);

    return $file;
}

/**
 * Displays the content of a file.
 *
 * @param array $object an associative array describing the file
 */ function fileGet($object): void
{
    $imageMime = ['image/png', 'image/jpeg', 'image/gif', 'image/bmp', 'image/vnd.microsoft.icon', 'image/tiff', 'image/svg+xml'];
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    header('Content-Type: ' . $object['mime']);
    if (!in_array($object['mime'], $imageMime, true)) {
        header('Content-Disposition: attachment; filename=' . $object['fileName']);
    }
    header('Content-Length: ' . $object['fileSize']);
    if (file_exists($object['fullPath'])) {
        //fpassthru(fopen($object['fullPath'],"r"));
        readfile($object['fullPath']);
    } else {
        DebuggerLog("unable to locate file: " . $object['fullPath'], "W");
    }
}

/**
 * Returns a generic placeholder image based on the file type.
 *
 * @param string $fileType the type of file (portrait/symbol/logo)
 */
function filePlaceholder($fileType = 'logo'): void
{
    switch ($fileType) {
        case 'portrait': $placeholder = SERVER_ROOT . "images/placeholder.jpg";

            break;
        case 'symbol': $placeholder = SERVER_ROOT . "images/nosymbol.png";

            break;
        default: $placeholder = SERVER_ROOT . "images/placeholder.jpg";

            break;
    }
    header("Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    header('Content-Type: ' . mime_content_type($placeholder));
    $placeholderFile = fopen($placeholder, "r");
    fpassthru($placeholderFile);
}

/**
 * Returns only files that contain a specific string in their names.
 *
 * @param  string $directory the path to the directory to scan
 * @param  string $string    the string to search for in the file names
 * @return array  an array of files that contain the specified string in their names
 */
function filterDirectory($directory, $string)
{
    $scandir = array_diff(scandir($directory), ['.', '..']);
    $filteredArray = array_filter($scandir, function ($value) use ($string) {
        return strpos($value, $string) !== false;
    });
    natsort($filteredArray);

    return $filteredArray;
}
