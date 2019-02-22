<?php
// *** ADD COLUMN
$add_column['backups']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$add_column['users']['sid'] = "VARCHAR(32) NOT NULL AFTER `id`";
$add_column['users']['user_agent'] = "VARCHAR(256) NOT NULL AFTER `ip`";
$add_column['users']['suspended'] = "INT NOT NULL DEFAULT '0' AFTER `deleted`";
$add_column['news']['deleted'] = "INT NOT NULL DEFAULT '0' AFTER `kategorie`";
$add_column['cases']['contents_md'] = "TEXT NULL";
$add_column['dashboard']['content_md'] = "TEXT NULL";
$add_column['groups']['contents_md'] = "TEXT NULL";
$add_column['news']['obsah_md'] = "TEXT NULL";
$add_column['notes']['note_md'] = "TEXT NULL";
$add_column['persons']['contents_md'] = "TEXT NULL";
$add_column['reports']['summary_md'] = "TEXT NULL";
$add_column['reports']['impacts_md'] = "TEXT NULL";
$add_column['reports']['details_md'] = "TEXT NULL";
$add_column['reports']['energy_md'] = "TEXT NULL";
$add_column['reports']['inputs_md'] = "TEXT NULL";
$add_column['symbols']['desc_md'] = "TEXT NULL";
// *** ADD FULLTEXT INDEX
$add_fulltext['cases'] = ['contents_md'];
$add_fulltext['dashboard'] = ['content_md'];
$add_fulltext['groups'] = ['contents_md'];
$add_fulltext['news'] = ['obsah_md'];
$add_fulltext['notes'] = ['note_md'];
$add_fulltext['persons'] = ['contents_md'];
$add_fulltext['reports'] = ['summary_md', 'impacts_md', 'details_md', 'energy_md', 'inputs_md'];
$add_fulltext['symbols'] = ['desc_md'];
// IF EXISTS RENAME TABLE `NHBistro`.`nw_loggedin` TO `NHBistro`.`nw_loggedin_deleted`;   
// IF EXISTS RENAME TABLE `NHBistro`.`nw_map` TO `NHBistro`.`nw_map_deleted`;   

// ALTER TABLE `nw_unread` ADD INDEX(`iduser`)
// ALTER TABLE "table_name" DROP "column_name";
// ALTER TABLE table CHANGE oldcolumn newcolumn char(50); prejmenovani sloupce
// ALTER TABLE Employees MODIFY COLUMN empName VARCHAR(50) AFTER department;  presunuti slopce po tabulce

$alter = $alter_password = 0;

// ADD COLUMN
foreach(array_keys($add_column) as $table) {
    foreach(array_keys($add_column[$table]) as $column) {
		$check_sql=mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
        if(mysqli_num_rows($check_sql)== 0) {
			$alter_sql = "ALTER TABLE ".DB_PREFIX."$table ADD COLUMN `$column` ".$add_column[$table][$column];
			mysqli_query($database,$alter_sql);
			$alter++;
        }
    }
}

// ADD FULLTEXT
foreach(array_keys($add_fulltext) as $table) {
	foreach($add_fulltext[$table] as $key => $value ) {
		$check_sql=mysqli_query($database,"SHOW INDEX FROM ".DB_PREFIX."$table WHERE index_type = 'FULLTEXT' and column_name='$value'");
		if(mysqli_num_rows($check_sql)== 0) {
			$alter_sql = "ALTER TABLE ".DB_PREFIX."$table ADD FULLTEXT (`$value`)";
			mysqli_query($database,$alter_sql);
			$alter++;
		}
	}
}

//md5 passwords
$password_sql=mysqli_query($database,"SELECT pwd FROM ".DB_PREFIX."users");
while($password_data = mysqli_fetch_array($password_sql)) {
	if(strlen($password_data['pwd']) != 32) {
		$alter_password++;
	}
}
unset ($password_sql);
if($alter_password > 0) {
	$password_sql=mysqli_query($database,"SELECT pwd,id FROM ".DB_PREFIX."users");
	while($password_data = mysqli_fetch_array($password_sql)) {
		mysqli_query($database,"UPDATE ".DB_PREFIX."users set pwd=md5('".$password_data['pwd']."') where id=".$password_data['id']);
		$alter++;
	}
}

//pokud zmeny probehly, prejmenovat tento soubor 
if ($alter > 0) { 	
    rename(__FILE__,__FILE__.".old");
}

?>