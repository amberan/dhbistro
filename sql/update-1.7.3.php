<?php

use Tracy\Debugger;

Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

/*
 * CREATE TABLE
 */
$tableCreate['test'] = 'testId';
$tableCreate['sort'] = 'sortId';
$tableCreate['filter'] = 'filterId';

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
$columnAdd['case']['contentMD'] = "TEXT NULL";
$columnAdd['dashboard']['contentMD'] = "TEXT NULL";
$columnAdd['group']['contentsMD'] = "TEXT NULL";
$columnAdd['news']['obsahMD'] = "TEXT NULL";
$columnAdd['note']['noteMD'] = "TEXT NULL";
$columnAdd['person']['contentsMD'] = "TEXT NULL";
$columnAdd['report']['summaryMD'] = "TEXT NULL";
$columnAdd['report']['impactsMD'] = "TEXT NULL";
$columnAdd['report']['detailsMD'] = "TEXT NULL";
$columnAdd['report']['energyMD'] = "TEXT NULL";
$columnAdd['report']['inputsMD'] = "TEXT NULL";
$columnAdd['symbol']['descMD'] = "TEXT NULL";
$columnAdd['user']['planMD'] = "TEXT NULL";
$columnAdd['task']['taskMD'] = "TEXT NULL";
$columnAdd['user']['email'] = "VARCHAR(256) NULL AFTER pwd";
$columnAdd['sort']['userId'] = "int NOT NULL AFTER sortId";
$columnAdd['sort']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['sort']['sortColumn'] = "varchar(100) NULL AFTER objectType";
$columnAdd['sort']['sortDirection'] = "varchar(4) NULL AFTER sortColumn";
$columnAdd['filter']['userId'] = "int NOT NULL AFTER filterId";
$columnAdd['filter']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['filter']['filterPreference'] = "varchar(200) NULL AFTER objectType";
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
//$columnAlter['case']['contentsMD'] = "contentMD text NULL";
//$columnAlter['dashboard']['id'] = "dashboardid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['dashboard']['created'] = "timestamp int(11) NOT NULL";
//$columnAlter['file']['id'] = "fileid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['file']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['doodle']['id'] = "doodleid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['doodle']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['group']['id'] = "groupid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['group']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['group']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['case']['contentsMD'] = "contentMD text NULL";
//$columnAlter['news']['id'] = "newsid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['news']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['news']['kategorie'] = "category int(11) NOT NULL";
//$columnAlter['news']['nadpis'] = "title varchar(255) NOT NULL";
//$columnAlter['news']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['news']['obsahMD'] = "contentMD text NOT NULL";
//$columnAlter['note']['id'] = "noteid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['note']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['note']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['operation_type']['id'] = "operationtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['person']['id'] = "personid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['person']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['person']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['person']['contentsMD'] = "contentMD text NULL";
//$columnAlter['recordytype']['id'] = "recordtypeid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['report']['id'] = "reportid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['report']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['report']['datum'] = "timestamp int(11) NOT NULL";
//$columnAlter['report']['impactsMD'] = "impactMD text NULL";
//$columnAlter['report']['detailsMD'] = "detailMD text NULL";
//$columnAlter['report']['inputsMD'] = "inputMD text NULL";
//$columnAlter['symbol']['id'] = "symbolid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['symbol']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
//$columnAlter['symbol']['desc'] = "description text NULL";
//$columnAlter['symbol']['descMD'] = "descriptionMD text NULL";
//$columnAlter['task']['id'] = "taskid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['unread']['id'] = "unreadid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['user']['id'] = "userid int(11) NOT NULL AUTO_INCREMENT FIRST";
//$columnAlter['user']['login'] = "username varchar(255) NOT NULL";

/*
 * CONVERT DATA TO MARKDOWN
 */
$columnToMD[] = ['user', 'id', 'plan', 'planMD'];
//$columnToMD[] = ['case','id','contents','contentsMD'];
$columnToMD[] = ['dashboard', 'id', 'content', 'contentMD'];
//$columnToMD[] = ['group','id','contents','contentsMD'];
$columnToMD[] = ['news', 'id', 'obsah', 'obsahMD'];
//$columnToMD[] = ['note','id','note','noteMD'];
//$columnToMD[] = ['person','id','contents','contentsMD'];
//$columnToMD[] = ['report','id','summary','summaryMD'];
//$columnToMD[] = ['report','id','impacts','impactsMD'];
//$columnToMD[] = ['report','id','details','detailsMD'];
//$columnToMD[] = ['report','id','energy','energyMD'];
//$columnToMD[] = ['report','id','inputs','inputsMD'];
//$columnToMD[] = ['symbol','id','desc','descMD'];

/*
 * RIGHTS TO UPDATE
 */
$rightsToUpdate['rightTextOld'] = ['aclTask', 'aclGroup', 'aclPerson', 'aclCase'];
$rightsToUpdate['rightAudOld'] = ['aclAudit'];
$rightsToUpdate['rightOrgOld'] = ['aclGamemaster'];
$rightsToUpdate['rightPowerOld'] = ['aclDirector', 'aclDeputy', 'aclSecret', 'aclHunt'];
$rightsToUpdate['rightSuperOld'] = ['aclRoot'];

/*
 * ADD FULLTEXT INDEX
 */
$columnAddFulltext['test2'] = ['test2'];
$columnAddFulltext['case'] = ['contentsMD'];
$columnAddFulltext['dashboard'] = ['contentMD'];
$columnAddFulltext['group'] = ['contentsMD'];
$columnAddFulltext['news'] = ['obsahMD'];
$columnAddFulltext['note'] = ['noteMD'];
$columnAddFulltext['person'] = ['contentsMD'];
$columnAddFulltext['report'] = ['summaryMD', 'impactsMD', 'detailsMD', 'energyMD', 'inputsMD'];
$columnAddFulltext['symbol'] = ['descMD'];

/*
 * ADD INDEX
 */
// ALTER TABLE nw_unread ADD INDEX(iduser)

/*
 * COLUMNS TO DROP
 */
$columnDrop['test2'][] = "test2";
$columnDrop['user'] = 'rightTextOld';
$columnDrop['user'] = 'rightAudOld';
$columnDrop['user'] = 'rightOrgOld';
$columnDrop['user'] = 'rightPowerOld';
$columnDrop['user'] = 'rightSuperOld';
$columnDrop['user'][] = 'user_agent';
$columnDrop['user'][] = 'suspended';
$columnDrop['user'][] = 'plan_md';
$columnDrop['user'][] = 'email';

/*
 * DROP TABLE
 */
$tableDrop[] = 'test2';
$tableDrop[] = 'loggedin_deleted';
$tableDrop[] = 'map_deleted';

/**
 * UPDATING.
 */
require_once 'update-function.php';

$counterTableCreate = bistroDBTableCreate($tableCreate);
$counterTableRename = bistroDBTableRename($tableRename);
$counterColumnAdd = bistroDBColumnAdd($columnAdd);
$counterColumnAlter = bistroDBColumnAlter($columnAlter);
$counterColumnMarkdown = bistroDBColumnMarkdown($columnToMD);
$counterPasswordEncrypt = bistroDBPasswordEncrypt();
$counterUPdateRight = bistroMigrateRights($rightsToUpdate);
$counterFulltextAdd = bistroDBFulltextAdd($columnAddFulltext);
//$counterIndexAdd = bistroDBIndexAdd($columnAddIndex);
$counterIndexAdd = 0;
$counterColumnDrop = bistroDBColumnDrop($columnDrop);
$counterTableDrop = bistroDBTableDrop($tableDrop);

//pokud zmeny probehly, prejmenovat tento soubor
if ($counterColumnAdd + $counterColumnAlter + $counterColumnMarkdown + $counterFulltextAdd + $counterPasswordEncrypt
    + $counterTableRename + $counterTableDrop + $counterTableCreate + $counterIndexAdd + $counterUPdateRight
    + $counterColumnDrop > 0) {
    rename(__FILE__,__FILE__.".old");
}
