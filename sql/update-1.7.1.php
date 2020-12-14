<?php

/**
 * CREATE TABLE.
 */
$tableCreate['test'] = 'testId';
$tableCreate['sort'] = 'sortId';

/*
 * RENAME TABLE
 */
$tableRename['test'] = "test2";
$tableRename['loggedin'] = "loggedin_deleted";
$tableRename['map'] = "map_deleted";
$tableRename['backups'] = "backup";
$tableRename['cases'] = "case";
$tableRename['data'] = "file";
$tableRename['groups'] = "group";
$tableRename['notes'] = "note";
$tableRename['persons'] = "person";
$tableRename['reports'] = "report";
$tableRename['symbols'] = "symbol";
$tableRename['tasks'] = "task";
$tableRename['users'] = "user";

/*
 * ADD COLUMN
 */
$columnAdd['test2']['test'] = "int NULL after testId";
$columnAdd['backup']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$columnAdd['user']['sid'] = "VARCHAR(32) NOT NULL AFTER id";
$columnAdd['user']['user_agent'] = "VARCHAR(256) NULL AFTER ip";
$columnAdd['user']['suspended'] = "INT NOT NULL DEFAULT '0' AFTER deleted";
$columnAdd['news']['deleted'] = "INT(3) NOT NULL DEFAULT '0' AFTER kategorie";
$columnAdd['case']['contents_md'] = "TEXT NULL";
$columnAdd['dashboard']['content_md'] = "TEXT NULL";
$columnAdd['group']['contents_md'] = "TEXT NULL";
$columnAdd['news']['obsah_md'] = "TEXT NULL";
$columnAdd['note']['note_md'] = "TEXT NULL";
$columnAdd['person']['contents_md'] = "TEXT NULL";
$columnAdd['report']['summary_md'] = "TEXT NULL";
$columnAdd['report']['impacts_md'] = "TEXT NULL";
$columnAdd['report']['details_md'] = "TEXT NULL";
$columnAdd['report']['energy_md'] = "TEXT NULL";
$columnAdd['report']['inputs_md'] = "TEXT NULL";
$columnAdd['symbol']['desc_md'] = "TEXT NULL";
$columnAdd['user']['plan_md'] = "TEXT NULL";
$columnAdd['task']['task_md'] = "TEXT NULL";
$columnAdd['user']['email'] = "VARCHAR(256) NULL AFTER pwd";
$columnAdd['sort']['userId'] = "int NOT NULL AFTER sortId";
$columnAdd['sort']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['sort']['sortColumn'] = "varchar(100) NULL AFTER objectType";
$columnAdd['sort']['sortDirection'] = "varchar(4) NULL AFTER sortColumn";
//$columnAdd['task']['deleted'] = "int(3) NOT NULL DEFAULT '0'";

/*
 * ALTER COLUMN
 */
$columnAlter['test2']['test'] = "test2 int null";
$columnAlter['user']['sid'] = " sid varchar(32) COLLATE 'utf8_general_ci' NULL AFTER id";
$columnAlter['user']['idperson'] = " idperson int(11) NULL AFTER pwd";
$columnAlter['user']['lastlogon'] = " lastlogon int(11) NULL AFTER idperson";
$columnAlter['user']['ip'] = " ip varchar(50) COLLATE 'utf8_general_ci' NULL AFTER lastlogon";
$columnAlter['user']['user_agent'] = " user_agent varchar(256) COLLATE 'utf8_general_ci' NULL AFTER ip";
$columnAlter['user']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$columnAlter['user']['suspended'] = " suspended int(11) NOT NULL DEFAULT '0' AFTER deleted";
$columnAlter['user']['zlobody'] = " zlobody int(11) NOT NULL DEFAULT '0' AFTER suspended";
$columnAlter['user']['right_text'] = " right_text int(11) NOT NULL DEFAULT '0' AFTER timeout";
$columnAlter['user']['right_power'] = " right_power int(11) NOT NULL DEFAULT '0' AFTER right_text";
$columnAlter['user']['right_org'] = " right_org int(11) NOT NULL DEFAULT '0' AFTER right_power";
$columnAlter['user']['right_aud'] = " right_aud int(11) NOT NULL DEFAULT '0' AFTER right_org";
$columnAlter['user']['right_super'] = " right_super int(11) NOT NULL DEFAULT '0' AFTER right_aud";
$columnAlter['user']['filter'] = " filter text COLLATE 'utf8_general_ci' NULL AFTER right_super";
$columnAlter['user']['plan'] = " plan text COLLATE 'utf8_general_ci' NULL AFTER right_super";
//$columnAlter['audit_trail']['id'] = "auditid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['audit_trail']['time'] = "timestamp int(11) NOT NULL";
//$columnAlter['backup']['id'] = "backupid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['backup']['time'] = "timestamp INT NOT NULL DEFAULT UNIX_TIMESTAMP() AFTER id";
//$columnAlter['case']['id'] = "caseid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['case']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['case']['time'] = "timestamp int(11) NOT NULL";
//$columnAlter['case']['contents_md'] = "content_md text NULL";
//$columnAlter['dashboard']['id'] = "dashboardid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['dashboard']['created'] = "timestamp int(11) NOT NULL";
//$columnAlter['file']['id'] = "fileid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['file']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['doodle']['id'] = "doodleid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['doodle']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['group']['id'] = "groupid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['group']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['group']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['case']['contents_md'] = "content_md text NULL";
//$columnAlter['news']['id'] = "newsid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['news']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['news']['kategorie'] = "category int(11) NOT NULL";
//$columnAlter['news']['nadpis'] = "title varchar(255) NOT NULL";
//$columnAlter['news']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['news']['obsah_md'] = "content_md text NOT NULL";
//$columnAlter['note']['id'] = "noteid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['note']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['note']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['operation_type']['id'] = "operationtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['person']['id'] = "personid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['person']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['person']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['person']['contents_md'] = "content_md text NULL";
//$columnAlter['recordytype']['id'] = "recordtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['report']['id'] = "reportid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['report']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['report']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['report']['impacts_md'] = "impact_md text NULL";
//$columnAlter['report']['details_md'] = "detail_md text NULL";
//$columnAlter['report']['inputs_md'] = "input_md text NULL";
//$columnAlter['symbol']['id'] = "symbolid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['symbol']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['symbol']['desc'] = "description text NULL";
//$columnAlter['symbol']['desc_md'] = "description_md text NULL";
//$columnAlter['task']['id'] = "taskid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['unread']['id'] = "unreadid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['user']['id'] = "userid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['user']['login'] = "username varchar(255) NOT NULL";

/*
 * CONVERT DATA TO MARKDOWN
 */
$columnToMD[] = ['user', 'id', 'plan', 'plan_md'];
//$columnToMD[] = ['case','id','contents','contents_md'];
$columnToMD[] = ['dashboard', 'id', 'content', 'content_md'];
//$columnToMD[] = ['group','id','contents','contents_md'];
$columnToMD[] = ['news', 'id', 'obsah', 'obsah_md'];
//$columnToMD[] = ['note','id','note','note_md'];
//$columnToMD[] = ['person','id','contents','contents_md'];
//$columnToMD[] = ['report','id','summary','summary_md'];
//$columnToMD[] = ['report','id','impacts','impacts_md'];
//$columnToMD[] = ['report','id','details','details_md'];
//$columnToMD[] = ['report','id','energy','energy_md'];
//$columnToMD[] = ['report','id','inputs','inputs_md'];
//$columnToMD[] = ['symbol','id','desc','desc_md'];

/*
 * ADD FULLTEXT INDEX
 */
$columnAddFulltext['test2'] = ['test2'];
$columnAddFulltext['case'] = ['contents_md'];
$columnAddFulltext['dashboard'] = ['content_md'];
$columnAddFulltext['group'] = ['contents_md'];
$columnAddFulltext['news'] = ['obsah_md'];
$columnAddFulltext['note'] = ['note_md'];
$columnAddFulltext['person'] = ['contents_md'];
$columnAddFulltext['report'] = ['summary_md', 'impacts_md', 'details_md', 'energy_md', 'inputs_md'];
$columnAddFulltext['symbol'] = ['desc_md'];

/*
 * ADD INDEX
 */
// ALTER TABLE nw_unread ADD INDEX(iduser)

/*
 * DROP TABLE
 */
$tableDrop[] = 'test2';
$tableDrop[] = 'loggedin_deleted';
$tableDrop[] = 'map_deleted';

use Tracy\Debugger;

Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
require_once 'update-function.php';

$counterTableCreate = bistroDBTableCreate($tableCreate);
$counterTableRename = bistroDBTableRename($tableRename);
$counterColumnAdd = bistroDBColumnAdd($columnAdd);
$counterColumnAlter = bistroDBColumnAlter($columnAlter);
$counterColumnMarkdown = bistroDBColumnMarkdown($columnToMD);
$counterPasswordEncrypt = bistroDBPasswordEncrypt();
$counterFulltextAdd = bistroDBFulltextAdd($columnAddFulltext);
//$counterIndexAdd = bistroDBIndexAdd($columnAddIndex);
$counterIndexAdd = 0;
$counterTableDrop = bistroDBTableDrop($tableDrop);

//pokud zmeny probehly, prejmenovat tento soubor
if ($counterColumnAdd + $counterColumnAlter + $counterColumnMarkdown + $counterFulltextAdd + $counterPasswordEncrypt + $counterTableRename + $counterTableDrop + $counterTableCreate + $counterIndexAdd > 0) {
    rename(__FILE__,__FILE__.".old");
}
