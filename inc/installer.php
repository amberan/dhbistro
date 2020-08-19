<?php

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT,$config['folder_logs']);
/** 
 * If DB doesn't exist, create new, populate, set admin
 * TODO: extract DATA populating
 */
$dbExist = mysqli_query ($database,"show tables");
    if (mysqli_num_rows($dbExist) == 0) {
        $dbScriptFileList = glob(SERVER_ROOT.'/sql/default*.sql');
        $dbScriptFilename = end ($dbScriptFileList);
        $dbScriptQuery = explode(";",file_get_contents($dbScriptFilename));
        foreach ($dbScriptQuery as $query) {
            mysqli_query($database,$query);
            print_r(mysqli_error_list($database));
        }
        $dbScriptFilename = explode ("/",$dbScriptFilename);
        Debugger::log("DEBUG: Database EMPTY, populating based on ".end($dbScriptFilename));
        $adminPassword = randomPassword();
        Debugger::log("DEBUG: creating admin : ".$adminPassword);
        $adminpassword_sql = "UPDATE ".DB_PREFIX."user SET pwd=md5('$adminPassword')";
        mysqli_query($database,$adminpassword_sql);
        
        $_SESSION['message'] = $text['databazenenalezena'].end($dbScriptFilename).$text['vytvorenadmin'].$adminPassword;
    }
