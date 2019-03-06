<?php
// *** ADD COLUMN
$add_column['backups']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$add_column['users']['sid'] = "VARCHAR(32) NOT NULL AFTER `id`";
$add_column['users']['user_agent'] = "VARCHAR(256) NOT NULL AFTER `ip`";
$add_column['users']['suspended'] = "INT NOT NULL DEFAULT '0' AFTER `deleted`";
$add_column['news']['deleted'] = "INT(3) NOT NULL DEFAULT '0' AFTER `kategorie`";
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
//$add_column['data']['deleted'] = "int(3) NOT NULL DEFAULT '0'";
// *** ADD FULLTEXT INDEX
$add_fulltext['cases'] = ['contents_md'];
$add_fulltext['dashboard'] = ['content_md'];
$add_fulltext['groups'] = ['contents_md'];
$add_fulltext['news'] = ['obsah_md'];
$add_fulltext['notes'] = ['note_md'];
$add_fulltext['persons'] = ['contents_md'];
$add_fulltext['reports'] = ['summary_md', 'impacts_md', 'details_md', 'energy_md', 'inputs_md'];
$add_fulltext['symbols'] = ['desc_md'];
// *** CONVERT TO MD
//$to_MD[] = ['cases','id','contents','contents_md'];
//$to_MD[] = ['dashboard','id','content','content_md'];
//$to_MD[] = ['groups','id','contents','contents_md'];
//$to_MD[] = ['news','id','obsah','obsah_md'];
//$to_MD[] = ['notes','id','note','note_md'];
//$to_MD[] = ['persons','id','contents','contents_md'];
//$to_MD[] = ['reports','id','summary','summary_md'];
//$to_MD[] = ['reports','id','impacts','impacts_md'];
//$to_MD[] = ['reports','id','details','details_md'];
//$to_MD[] = ['reports','id','energy','energy_md'];
//$to_MD[] = ['reports','id','inputs','inputs_md'];
//$to_MD[] = ['symbols','id','desc','desc_md'];
// *** RENAME COLUMN
// ALTER TABLE table CHANGE oldcolumn newcolumn char(50); prejmenovani sloupce
/*$rename_column['audit_trail']['id'] = "auditid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['audit_trail']['time'] = "timestamp int(11) NOT NULL";
$rename_column['backups']['id'] = "backupid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['backups']['time'] = "timestamp int(11) NOT NULL";
$rename_column['cases']['id'] = "caseid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['cases']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['cases']['time'] = "timestamp int(11) NOT NULL";
$rename_column['cases']['contents_md'] = "content_md text NULL";
$rename_column['dashboard']['id'] = "dashboardid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['dashboard']['created'] = "timestamp int(11) NOT NULL";
$rename_column['data']['id'] = "fileid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['data']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['doodle']['id'] = "doodleid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['doodle']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['groups']['id'] = "groupid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['groups']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['groups']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['cases']['contents_md'] = "content_md text NULL";
$rename_column['news']['id'] = "newsid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['news']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['news']['kategorie'] = "category int(11) NOT NULL";
$rename_column['news']['nadpis'] = "title varchar(255) NOT NULL";
$rename_column['news']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['news']['obsah_md'] = "content_md text NOT NULL";
$rename_column['notes']['id'] = "noteid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['notes']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['notes']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['operation_type']['id'] = "operationtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['persons']['id'] = "personid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['persons']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['persons']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['persons']['contents_md'] = "content_md text NULL";
$rename_column['recordytype']['id'] = "recordtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['reports']['id'] = "reportid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['reports']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['reports']['datum'] = "timestamp int(11) NOT NULL";
$rename_column['reports']['impacts_md'] = "impact_md text NULL";
$rename_column['reports']['details_md'] = "detail_md text NULL";
$rename_column['reports']['inputs_md'] = "input_md text NULL";
$rename_column['symbols']['id'] = "symbolid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['symbols']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['symbols']['desc'] = "description text NULL";
$rename_column['symbols']['desc_md'] = "description_md text NULL";
$rename_column['tasks']['id'] = "taskid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['unread']['id'] = "unreadid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['users']['id'] = "userid int(11) NOT NULL AUTO_INCREMENT FIRST";
$rename_column['users']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$rename_column['users']['login'] = "username varchar(255) NOT NULL";
*/

// *** MOFIDY COLUMN
// ALTER TABLE nw_backups MODIFY COLUMN `timestamp` timestamp INT NOT NULL DEFAULT UNIX_TIMESTAMP() AFTER `id`;  modifikace slopce

// *** RENAME TABLE
// IF EXISTS RENAME TABLE `NHBistro`.`nw_loggedin` TO `NHBistro`.`nw_loggedin_deleted`;   
// IF EXISTS RENAME TABLE `NHBistro`.`nw_map` TO `NHBistro`.`nw_map_deleted`;   
// IF EXISTS RENAME TABLE `NHBistro`.`nw_backups` TO `NHBistro`.`nw_map_deleted`;   
// IF EXISTS RENAME TABLE `NHBistro`.`nw_data` TO `NHBistro`.`nw_file`;   
// IF EXISTS RENAME TABLE `NHBistro`.`nw_cases` TO `NHBistro`.`nw_cases';
// IF EXISTS RENAME TABLE `NHBistro`.`nw_groups` TO `NHBistro`.`nw_group';
// IF EXISTS RENAME TABLE `NHBistro`.`nw_persons` TO `NHBistro`.`nw_person';
// IF EXISTS RENAME TABLE `NHBistro`.`nw_reports` TO `NHBistro`.`nw_report';
// IF EXISTS RENAME TABLE `NHBistro`.`nw_symbols` TO `NHBistro`.`nw_symbol';
// IF EXISTS RENAME TABLE `NHBistro`.`nw_users` TO `NHBistro`.`nw_identity';

// *** ADD INDEX
// ALTER TABLE `nw_unread` ADD INDEX(`iduser`)
// ALTER TABLE "table_name" DROP "column_name";

// *** DATABASE TIMESTAMP
// `first_touch_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP >> select UNIX_TIMESTAMP(first_touch_time)  from exec_log


use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

$alter = $alter_password = 0;

// ADD COLUMN
foreach(array_keys($add_column) as $table) {
    foreach(array_keys($add_column[$table]) as $column) {
		$check_sql=mysqli_query($database,"SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='".$config['dbdatabase']."' AND table_name='".DB_PREFIX."$table' and column_name='$column'");
        if(mysqli_num_rows($check_sql)== 0) {
			$alter_sql = "ALTER TABLE ".DB_PREFIX."$table ADD COLUMN `$column` ".$add_column[$table][$column];
			Debugger::log('DB CHANGE: '.$alter_sql);
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
			Debugger::log('DB CHANGE: '.$alter_sql);
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
	$password_sql=mysqli_query($database,"SELECT pwd,id,'login' FROM ".DB_PREFIX."users");
	while($password_data = mysqli_fetch_array($password_sql)) {
		mysqli_query($database,"UPDATE ".DB_PREFIX."users set pwd=md5('".$password_data['pwd']."') where id=".$password_data['id']);
		Debugger::log('Hashing password for userid: '.$password_data['login']);
		$alter++;
	}
}


// CONVERT TO MARKDOWN
use League\HTMLToMarkdown\HtmlConverter;
$converter = new HtmlConverter(array('strip_tags' => true)); //https://github.com/thephpleague/html-to-markdown
foreach($to_MD as $key  => $value) {
	//echo "TABLE: ".$value[0]." ID: ".$value[1]." SOURCE: ".$value[2]." TARGET: ".$value[3]."<br>";
		$preMD_sql = mysqli_query($database,"SELECT ".$value[1].", ".$value[2]." FROM ".DB_PREFIX.$value[0]." WHERE ".$value[3]." = ''");
		while($preMD = mysqli_fetch_array($preMD_sql)) {
			$MDcolumn = $converter->convert( str_replace('\'', '', $preMD[$value[2]]));
			Debugger::log('Markdown conversion ['.DB_PREFIX.$value[0].'.'.$preMD[$value[1]].']: '.$preMD[$value[2]].' ##### TO ##### '.$MDcolumn);
			mysqli_query($database,"UPDATE ".DB_PREFIX.$value[0]." SET ".$value[3]."='".$MDcolumn."' WHERE ".$value[1]."=".$preMD[$value[1]]);
			$alter++;
		}
}


//pokud zmeny probehly, prejmenovat tento soubor 
if ($alter > 0) { 	
    rename(__FILE__,__FILE__.".old");
}

?>