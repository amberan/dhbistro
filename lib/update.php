<?php

    use League\HTMLToMarkdown\HtmlConverter;
    use Tracy\Debugger;

    Debugger::enable(Debugger::DEVELOPMENT, $config['folder_logs']);

/**
 * returns all update*php files in sql that are never than last backup but at most current version
 */
function bistroUpdatesList($updateFiles, $lastBackup)
{
    global $config;
    $files = array();
    foreach ($updateFiles as $file) {
        if (preg_match('/update-[0-9.]{1,}php/', $file) != null && version_compare($lastBackup, substr($file, 7, -4)) < 0
        && version_compare($config['version'], substr($file, 7, -4)) >= 0) {
            $files[] = $file;
        }
    }
    return $files;
}


function bistroUpdate($updatesToRun)
{
    bistroMyisamToInnodb();
    foreach ($updatesToRun as $file) {
        // require_once 'lib/update.php';
        require_once $_SERVER['DOCUMENT_ROOT']."/sql/".$file;
        if (isset($tableCreate)) {
            bistroDBTableCreate($tableCreate, substr($file, 7, -4));
        }
        if (isset($tableRename)) {
            bistroDBTableRename($tableRename, substr($file, 7, -4));
        }
        if (isset($columnAdd)) {
            bistroDBColumnAdd($columnAdd, substr($file, 7, -4));
        }
        if (isset($columnAlter)) {
            bistroDBColumnAlter($columnAlter, substr($file, 7, -4));
        }
        if (isset($columnToMD)) {
            bistroDBColumnMarkdown($columnToMD, substr($file, 7, -4));
        }
        if (isset($columnAddFulltext)) {
            bistroDBFulltextAdd($columnAddFulltext, substr($file, 7, -4));
        }
        if (DBcolumnExist("user", "userPassword")) {
            bistroDBPasswordEncrypt();
        }
        if (isset($rightsToUpdate)) {
            bistroMigratePermissions($rightsToUpdate, substr($file, 7, -4));
        }
        if (isset($convertTime)) {
            bistroIntToTimestamp($convertTime, substr($file, 7, -4));
        }
        if (isset($columnDrop)) {
            bistroDBColumnDrop($columnDrop, substr($file, 7, -4));
        }
        if (isset($tableDrop)) {
            bistroDBTableDrop($tableDrop, substr($file, 7, -4));
        }
        unset($tableCreate,$tableRename,$columnAdd,$columnAlter,$columnAddFulltext,$columnToMD,$rightsToUpdate,$convertTime,$columnDrop,$tableDrop);
    }
}


/**
 * converts configuration from password file and platform definition to one
 */
function bistroEnvConvert()
{
    global $config,$latteParameters,$_POST,$latteParameters,$latte;
    $latteParameters['title'] = 'INSTALLER';
    if (isset($_POST['dbHost'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbDatabase'])
    && DBTest($_POST)) {
        // installer form posted and db connection valid
        bistroEnvFile($_POST);
        require_once $config['platformConfig'];
    } elseif (file_exists(SERVER_ROOT.'/inc/platform.php')) {
        // convert old files
        require_once SERVER_ROOT.'/inc/platform.php';
        $config['dbHost'] = 'localhost';
        $config['dbPrefix'] = DB_PREFIX;
        if (file_exists($config['dbpass'])) {
            $lines = file($config['dbpass'], FILE_IGNORE_NEW_LINES) or die("fail pwd");
            $config['dbPassword'] = $lines[2];
        }
        if (isset($config['dbHost'], $config['dbUser'], $config['dbPassword'], $config['dbDatabase'])
        && DBTest($config)) {
            //converted and tested
            bistroEnvFile($config);
        } else {
            $latteParameters['config'] = $config;
            $latteParameters['backupList'] = fileList($config['folder_backup']);
            latteDrawTemplate('installer');
            exit;
        }
    } else {
        $latteParameters['config'] = $_POST;
        latteDrawTemplate('installer');
        exit;
    }
}

/**
 * save instance configuration in file
 */
function bistroEnvFile($post)
{
    global $config;
    $newConfigFile = fopen($config['platformConfig'], "w") or die("Unable to write configuration file!");
    $configList = '<?php
        define(\'DB_PREFIX\', \''.$post['dbPrefix'].'\');
        $'.'configDB[\'dbHost\']            = \''.$post['dbHost'].'\';
        $'.'configDB[\'dbUser\']            = \''.$post['dbUser'].'\';
        $'.'configDB[\'dbPassword\']          = \''.$post['dbPassword'].'\';
        $'.'configDB[\'dbDatabase\']        = \''.$post['dbDatabase'].'\';
        $'.'config[\'themeColor\']             = \''.$post['themeColor'].'\';
        $'.'config[\'themeCustom\']            = \''.$post['themeCustom'].'\';
        $'.'config[\'themeBg\']          = \''.$post['themeBg'].'\';
        $'.'config[\'themeNavbar\']      = \''.$post['themeNavbar'].'\';
        ';
    fwrite($newConfigFile, $configList);
    fclose($newConfigFile);
}

/**
 * if user.password != 32
 * UPDATE database.user set pwd=md5(password) where id=user_id);.
 *
 * @return int of changed items
 */
function bistroDBPasswordEncrypt(): int
{
    global $database,$configDB,$config;
    $alterPassword = $alter = 0;
    $passwordSql = "SELECT userPassword FROM ".$configDB['dbDatabase'].".".DB_PREFIX."user";
    $passwordQuery = mysqli_query($database, $passwordSql);
    if (mysqli_num_rows($passwordQuery) > 0) {
        while ($passwordData = mysqli_fetch_array($passwordQuery)) {
            if (mb_strlen($passwordData['userPassword']) != 32) {
                $alterPassword++;
            }
        }
    }
    unset($passwordSql);
    if ($alterPassword > 0) {
        $passwordSql = "SELECT userPassword,userId,userName FROM ".$configDB['dbDatabase'].".".DB_PREFIX."user";
        $passwordQuery = mysqli_query($database, $passwordSql);
        while ($passwordData = mysqli_fetch_array($passwordQuery)) {
            mysqli_query($database, "UPDATE ".$configDB['dbDatabase'].".".DB_PREFIX."user set userPassword=md5('".$passwordData['userPassword']."') where userId=".$passwordData['userId']);
            Debugger::log('UPDATER '.$config['version'].': Hashing '.$passwordData['userName'].'.userPassword');
            $alter++;
        }
    }

    return $alter;
}

/**
 * UPDATE database.table SET columnMarkdown='contentMarkdown' WHERE id = id;.
 *
 * @param array $data data[] = ['table','id','htmlColumn','markdownColumn'];
 *
 * @return int of changed items
 */
function bistroDBColumnMarkdown($data, $file = null): int
{
    global $database,$configDB;
    $alter = 0;
    $converter = new HtmlConverter(['strip_tags' => true]); //https://github.com/thephpleague/html-to-markdown
    foreach ($data as $value) {
        $preMarkdownSql = "SELECT ".$value[1].", ".$value[2]." FROM ".$configDB['dbDatabase'].".".DB_PREFIX.$value[0]." WHERE (length(".$value[3].") = 0  or length(".$value[3].") is null) and length(".$value[2].") > 0";
        if (DBcolumntNotEmpty($value[0], $value[3]) == 0 && DBcolumnExist($value[0], $value[2]) > 0 && DBcolumnExist($value[0], $value[3]) > 0) {
            $preMarkdownQuery = mysqli_query($database, $preMarkdownSql);
            while ($preMarkdown = mysqli_fetch_array($preMarkdownQuery)) {
                $markdownColumn = $converter->convert(str_replace('\'', '', $preMarkdown[$value[2]]));
                Debugger::log('UPDATER '.$file.' CONVERTING '.DB_PREFIX.$value[0].'.'.$value[2].'.'.$preMarkdown[$value[1]].' TO MARKDOWN '.$value[3]);
                mysqli_query($database, "UPDATE ".DB_PREFIX.$value[0]." SET ".$value[3]."='".$markdownColumn."' WHERE ".$value[1]."=".$preMarkdown[$value[1]]);
                $alter++;
            }
        }
    }

    return $alter;
}

/**
 * MIGRATE ACCESS RIGHTS.
 *
 * @param mixed $data
 */
function bistroMigratePermissions($data, $file = null): int
{
    global $database,$configDB;
    $alter = 0;
    foreach (array_keys($data) as $old) {
        foreach ($data[$old] as $new) {
            if (DBcolumnExist('user', $new) && DBcolumnExist('user', $old)) {
                $alterSql = "UPDATE ".$configDB['dbDatabase'].".".DB_PREFIX."user SET $new=$old;";
                mysqli_query($database, $alterSql);
                if (mysqli_affected_rows($database) > 0) {
                    Debugger::log('UPDATER '.$file.': PERMISSIONS '.$old.' => '.$new);
                    $alter++;
                }
            }
        }
    }

    return $alter;
}

/**
 * CONVERT int to timestamp
 */
function bistroIntToTimestamp($data, $file = null): int
{
    global $database,$configDB;
    $alter = 0;
    foreach ($data as $change) {
        if (DBcolumnExist($change[0], $change[1]) && DBcolumnExist($change[0], $change[2])) {
            $alterSql = "UPDATE ".$configDB['dbDatabase'].".".DB_PREFIX.$change[0]." SET ".$change[2]."=FROM_UNIXTIME(".$change[1].") where ".$change[1].">0 ;";
            mysqli_query($database, $alterSql);
            if (mysqli_affected_rows($database) > 0) {
                Debugger::log('UPDATER '.$file.': TIME CONVERSION nw'.$change[0].':  '.$change[1].' => '.$change[2]);
                $alter++;
            } else {
                Debugger::log('ERROR '.$file.': '.$alterSql);
            }
        }
    }
    return $alter;
}
