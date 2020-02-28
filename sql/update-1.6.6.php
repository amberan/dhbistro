<?php

// *** RENAME TABLE
$rename_table['loggedin'] = "loggedin_deleted";
$rename_table['map'] = "map_deleted";
$rename_table['backups'] = "backup";
$rename_table['cases'] = "case";
$rename_table['data'] = "file";
$rename_table['groups'] = "group";
$rename_table['notes'] = "note";
$rename_table['persons'] = "person";
$rename_table['reports'] = "report";
$rename_table['symbols'] = "symbol";
$rename_table['tasks'] = "task";
$rename_table['users'] = "user";

// *** ADD COLUMN
$add_column['backup']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$add_column['user']['sid'] = "VARCHAR(32) NOT NULL AFTER `id`";
$add_column['user']['user_agent'] = "VARCHAR(256) NULL AFTER `ip`";
$add_column['user']['suspended'] = "INT NOT NULL DEFAULT '0' AFTER `deleted`";
$add_column['news']['deleted'] = "INT(3) NOT NULL DEFAULT '0' AFTER `kategorie`";
$add_column['case']['contents_md'] = "TEXT NULL";
$add_column['dashboard']['content_md'] = "TEXT NULL";
$add_column['group']['contents_md'] = "TEXT NULL";
$add_column['news']['obsah_md'] = "TEXT NULL";
$add_column['note']['note_md'] = "TEXT NULL";
$add_column['person']['contents_md'] = "TEXT NULL";
$add_column['report']['summary_md'] = "TEXT NULL";
$add_column['report']['impacts_md'] = "TEXT NULL";
$add_column['report']['details_md'] = "TEXT NULL";
$add_column['report']['energy_md'] = "TEXT NULL";
$add_column['report']['inputs_md'] = "TEXT NULL";
$add_column['symbol']['desc_md'] = "TEXT NULL";
$add_column['user']['plan_md'] = "TEXT NULL";
$add_column['task']['task_md'] = "TEXT NULL";
$add_column['user']['email'] = "VARCHAR(256) NULL AFTER `pwd`";
//$add_column['file']['deleted'] = "int(3) NOT NULL DEFAULT '0'";

// *** ADD FULLTEXT INDEX
$add_fulltext['case'] = ['contents_md'];
$add_fulltext['dashboard'] = ['content_md'];
$add_fulltext['group'] = ['contents_md'];
$add_fulltext['news'] = ['obsah_md'];
$add_fulltext['note'] = ['note_md'];
$add_fulltext['person'] = ['contents_md'];
$add_fulltext['report'] = ['summary_md', 'impacts_md', 'details_md', 'energy_md', 'inputs_md'];
$add_fulltext['symbol'] = ['desc_md'];

// *** ALTER COLUMN
$alter_column['user']['sid'] = " `sid` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `id`";
$alter_column['user']['idperson'] = " `idperson` int(11) NULL AFTER `pwd`";
$alter_column['user']['lastlogon'] = " `lastlogon` int(11) NULL AFTER `idperson`";
$alter_column['user']['ip'] = " `ip` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `lastlogon`";
$alter_column['user']['user_agent'] = " `user_agent` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `ip`";
$alter_column['user']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$alter_column['user']['suspended'] = " `suspended` int(11) NOT NULL DEFAULT '0' AFTER `deleted`";
$alter_column['user']['zlobody'] = " `zlobody` int(11) NOT NULL DEFAULT '0' AFTER `suspended`";
$alter_column['user']['right_text'] = " `right_text` int(11) NOT NULL DEFAULT '0' AFTER `timeout`";
$alter_column['user']['right_power'] = " `right_power` int(11) NOT NULL DEFAULT '0' AFTER `right_text`";
$alter_column['user']['right_org'] = " `right_org` int(11) NOT NULL DEFAULT '0' AFTER `right_power`";
$alter_column['user']['right_aud'] = " `right_aud` int(11) NOT NULL DEFAULT '0' AFTER `right_org`";
$alter_column['user']['right_super'] = " `right_super` int(11) NOT NULL DEFAULT '0' AFTER `right_aud`";
$alter_column['user']['filter'] = " `filter` text COLLATE 'utf8_general_ci' NULL AFTER `right_super`";
$alter_column['user']['plan'] = " `plan` text COLLATE 'utf8_general_ci' NULL AFTER `right_super`";
//$alter_column['audit_trail']['id'] = "auditid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['audit_trail']['time'] = "timestamp int(11) NOT NULL";
//$alter_column['backup']['id'] = "backupid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['backup']['time'] = "timestamp INT NOT NULL DEFAULT UNIX_TIMESTAMP() AFTER `id`";
//$alter_column['case']['id'] = "caseid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['case']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['case']['time'] = "timestamp int(11) NOT NULL";
//$alter_column['case']['contents_md'] = "content_md text NULL";
//$alter_column['dashboard']['id'] = "dashboardid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['dashboard']['created'] = "timestamp int(11) NOT NULL";
//$alter_column['file']['id'] = "fileid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['file']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['doodle']['id'] = "doodleid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['doodle']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['group']['id'] = "groupid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['group']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['group']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['case']['contents_md'] = "content_md text NULL";
//$alter_column['news']['id'] = "newsid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['news']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['news']['kategorie'] = "category int(11) NOT NULL";
//$alter_column['news']['nadpis'] = "title varchar(255) NOT NULL";
//$alter_column['news']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['news']['obsah_md'] = "content_md text NOT NULL";
//$alter_column['note']['id'] = "noteid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['note']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['note']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['operation_type']['id'] = "operationtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['person']['id'] = "personid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['person']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['person']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['person']['contents_md'] = "content_md text NULL";
//$alter_column['recordytype']['id'] = "recordtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['report']['id'] = "reportid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['report']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['report']['datum'] = "timestamp int(11) NOT NULL";
//$alter_column['report']['impacts_md'] = "impact_md text NULL";
//$alter_column['report']['details_md'] = "detail_md text NULL";
//$alter_column['report']['inputs_md'] = "input_md text NULL";
//$alter_column['symbol']['id'] = "symbolid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['symbol']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$alter_column['symbol']['desc'] = "description text NULL";
//$alter_column['symbol']['desc_md'] = "description_md text NULL";
//$alter_column['task']['id'] = "taskid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['unread']['id'] = "unreadid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['user']['id'] = "userid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$alter_column['user']['login'] = "username varchar(255) NOT NULL";


// *** CONVERT TO MD
$to_MD[] = ['user', 'id', 'plan', 'plan_md'];
//$to_MD[] = ['case','id','contents','contents_md'];
$to_MD[] = ['dashboard', 'id', 'content', 'content_md'];
//$to_MD[] = ['group','id','contents','contents_md'];
$to_MD[] = ['news', 'id', 'obsah', 'obsah_md'];
//$to_MD[] = ['note','id','note','note_md'];
//$to_MD[] = ['person','id','contents','contents_md'];
//$to_MD[] = ['report','id','summary','summary_md'];
//$to_MD[] = ['report','id','impacts','impacts_md'];
//$to_MD[] = ['report','id','details','details_md'];
//$to_MD[] = ['report','id','energy','energy_md'];
//$to_MD[] = ['report','id','inputs','inputs_md'];
//$to_MD[] = ['symbol','id','desc','desc_md'];

// *** ADD INDEX
// ALTER TABLE `nw_unread` ADD INDEX(`iduser`)


// *** DROP TABLE
$drop_table[] = 'loggedin_deleted';
$drop_table[] = 'map_deleted';

use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
require_once('update-function.php');

$counterTableRename = bistroDBTableRename($rename_table);
$counterColumnAlter = bistroDBColumnAlter($alter_column);
$counterColumnAdd = bistroDBColumnAdd($add_column);
$counterPasswordEncrypt = bistroDBPasswordEncrypt();
$counterColumnMarkdown = bistroDBColumnMarkdown($to_MD);
$counterFulltextAdd = bistroDBFulltextAdd($add_fulltext);
$counterTableDrop = bistroDBTableDrop($drop_table);

//pokud zmeny probehly, prejmenovat tento soubor
if ($counterColumnAdd + $counterColumnAlter + $counterColumnMarkdown + $counterFulltextAdd + $counterPasswordEncrypt + $counterTableRename + $counterTableDrop > 0) {
    rename(__FILE__,__FILE__.".old");
}

?>