<?php

/** 
 * CREATE TABLE
 */
$tableCreate['test'] = 'testId';
$tableCreate['sort'] = 'sortId';

/** 
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

/** 
 * ADD COLUMN
 */
$columnAdd['test2']['test'] = "int NULL after testId";
$columnAdd['backup']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$columnAdd['case']['contents_md'] = "TEXT NULL";
$columnAdd['dashboard']['content_md'] = "TEXT NULL";
$columnAdd['group']['contents_md'] = "TEXT NULL";
$columnAdd['news']['deleted'] = "INT(3) NOT NULL DEFAULT '0' AFTER kategorie";
$columnAdd['news']['obsah_md'] = "TEXT NULL";
$columnAdd['note']['note_md'] = "TEXT NULL";
$columnAdd['person']['contents_md'] = "TEXT NULL";
$columnAdd['report']['summary_md'] = "TEXT NULL";
$columnAdd['report']['impacts_md'] = "TEXT NULL";
$columnAdd['report']['details_md'] = "TEXT NULL";
$columnAdd['report']['energy_md'] = "TEXT NULL";
$columnAdd['report']['inputs_md'] = "TEXT NULL";
$columnAdd['sort']['userId'] = "int NOT NULL AFTER sortId";
$columnAdd['sort']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['sort']['sortColumn'] = "varchar(100) NULL AFTER objectType";
$columnAdd['sort']['sortDirection'] = "varchar(4) NULL AFTER sortColumn";
$columnAdd['symbol']['desc_md'] = "TEXT NULL";
$columnAdd['task']['task_md'] = "TEXT NULL";
$columnAdd['user']['sid'] = "VARCHAR(32) NOT NULL AFTER id";
$columnAdd['user']['user_agent'] = "VARCHAR(256) NULL AFTER ip";
$columnAdd['user']['suspended'] = "INT NOT NULL DEFAULT '0' AFTER deleted";
$columnAdd['user']['plan_md'] = "TEXT NULL";
$columnAdd['user']['email'] = "VARCHAR(256) NULL AFTER pwd";
$columnAdd['user']['aclRoot'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclGamemaster'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclDirector'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclDeputy'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclTask'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclSecret'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclAudit'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclAPI'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclGroup'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclPerson'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclCase'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclHunt'] = "int(3) NOT NULL DEFAULT '0'";
//$columnAdd['task']['deleted'] = "int(3) NOT NULL DEFAULT '0'";

/** 
 * ALTER COLUMN
 */
$columnAlter['test2']['test'] = "test2 int null";
$columnAlter['user']['id'] = "userId int(6) NOT NULL AUTO_INCREMENT FIRST";
$columnAlter['user']['sid'] = "sid varchar(32) NULL AFTER userId";
$columnAlter['user']['login'] = "userName varchar(40) NOT NULL AFTER sid";
$columnAlter['user']['pwd'] = "userPassword varchar(40) NOT NULL AFTER userName";
$columnAlter['user']['email'] = "userEmail VARCHAR(256) NULL AFTER userPassword";
$columnAlter['user']['lastlogon'] = "lastLogin INT(11) NULL AFTER userEmail";
$columnAlter['user']['ip'] = "ipv4 varchar(15) NULL AFTER lastLogin";
$columnAlter['user']['user_agent'] = "userAgent VARCHAR(256) NULL AFTER ipv4";
$columnAlter['user']['timeout'] = "userTimeout INT(6) NOT NULL DEFAULT '600' AFTER userAgent";
$columnAlter['user']['suspended'] = "userSuspended int(3) NOT NULL DEFAULT '0' AFTER userTimeout";
$columnAlter['user']['deleted'] = "userDeleted int(3) NOT NULL DEFAULT '0' AFTER userSuspended";
$columnAlter['user']['idperson'] = "personId int(6) NULL AFTER userDeleted";
$columnAlter['user']['zlobody'] = "zlobod int(6) NOT NULL DEFAULT '0' AFTER personId";
$columnAlter['user']['plan_md'] = "planMD TEXT NULL AFTER aclHunt";
$columnAlter['user']['filter'] = "filter text NULL AFTER planMD";
$columnAlter['user']['plan'] = "plan text NULL AFTER planMD";
$columnAlter['user']['right_text'] = "rightTextOld int(3) NOT NULL DEFAULT '0' after planMD";
$columnAlter['user']['right_power'] = "rightPowerOld int(3) NOT NULL DEFAULT '0' after planMD";
$columnAlter['user']['right_org'] = "rightOrgOld int(3) NOT NULL DEFAULT '0' after planMD";
$columnAlter['user']['right_aud'] = "rightAudOld int(3) NOT NULL DEFAULT '0' after planMD";
$columnAlter['user']['right_super'] = "rightSuperOld int(3) NOT NULL DEFAULT '0' after planMD";
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

/** 
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


/** 
 * RIGHTS TO UPDATE
 */
$rightsToUpdate['rightTextOld'] = ['aclTask', 'aclGroup', 'aclPerson', 'aclCase'];
$rightsToUpdate['rightAudOld'] = ['aclAudit'];
$rightsToUpdate['rightOrgOld'] = ['aclGamemaster'];
$rightsToUpdate['rightPowerOld'] = ['aclDirector', 'aclDeputy', 'aclSecret', 'aclHunt'];
$rightsToUpdate['rightSuperOld'] = ['aclRoot'];

/**
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


/**
 * ADD INDEX
 */
// ALTER TABLE nw_unread ADD INDEX(iduser)


/** 
 * COLUMNS TO DROP 
 */
$columnDrop['test2'][] = "test2";
// $columnDrop['user'] = 'rightTextOld';
// $columnDrop['user'] = 'rightAudOld';
// $columnDrop['user'] = 'rightOrgOld';
// $columnDrop['user'] = 'rightPowerOld';
// $columnDrop['user'] = 'rightSuperOld';
$columnDrop['user'][] = 'user_agent';
$columnDrop['user'][] = 'suspended';
$columnDrop['user'][] = 'plan_md';
$columnDrop['user'][] = 'email';


/** 
 * DROP TABLE
 */
$tableDrop[] = 'test2';
$tableDrop[] = 'loggedin_deleted';
$tableDrop[] = 'map_deleted';


use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
require_once('update-function.php');

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

?>
