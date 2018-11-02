<?php
//ALTER TABLE       $1 ADD COLUMN $2        $3
$definition_column['backups']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$definition_column['users']['sid'] = "VARCHAR(32) NOT NULL AFTER `id`";
$definition_column['users']['user_agent'] = "VARCHAR(256) NOT NULL AFTER `ip`";
// IF EXISTS RENAME TABLE `NHBistro`.`nw_loggedin` TO `NHBistro`.`nw_loggedin_old`;   
// ALTER TABLE `nw_unread` ADD INDEX(`iduser`)

foreach(array_keys($definition_column) as $table) {
    foreach(array_keys($definition_column[$table]) as $column) {
		$check_sql=mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
        if(mysqli_num_rows($check_sql)== 0) {
            $alter_sql = "ALTER TABLE ".DB_PREFIX."$table ADD COLUMN `$column` ".$definition_column[$table][$column];
			debug("<br><br>".$alter_sql);
	            mysqli_query($database,$alter_sql);
			$alter++;
        }
    }
}

//pokud zmeny probehly, prejmenovat tento soubor 
if (isset($alter)) { 	
    rename(__FILE__,__FILE__.".old");
}

?>