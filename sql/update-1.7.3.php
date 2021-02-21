<?php

use Tracy\Debugger;

Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);

/*
 * CREATE TABLE
 */
$tableCreate['filter'] = 'filterId';
$tableCreate['sort'] = 'sortId';
//$tableCreate['test'] = 'testId';

/*
 * RENAME TABLE
 */
$tableRename['backups'] = "backup";
$tableRename['cases'] = "case";
$tableRename['data'] = "file";
$tableRename['groups'] = "group";
$tableRename['loggedin'] = "loggedin_deleted";
$tableRename['map'] = "map_deleted";
$tableRename['notes'] = "note";
$tableRename['persons'] = "person";
$tableRename['reports'] = "report";
$tableRename['symbols'] = "symbol";
$tableRename['tasks'] = "task";
//$tableRename['test'] = "test2";
$tableRename['users'] = "user";

/*
 * ADD COLUMN
 */
$columnAdd['backup']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$columnAdd['case']['contentMD'] = "TEXT NULL";
$columnAdd['dashboard']['contentMD'] = "TEXT NULL";
$columnAdd['filter']['userId'] = "int NOT NULL AFTER filterId";
$columnAdd['filter']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['filter']['filterPreference'] = "varchar(200) NULL AFTER objectType";
$columnAdd['group']['contentsMD'] = "TEXT NULL";
$columnAdd['news']['deleted'] = "INT(3) NOT NULL DEFAULT '0' AFTER kategorie";
$columnAdd['news']['obsahMD'] = "TEXT NULL";
$columnAdd['note']['noteMD'] = "TEXT NULL";
$columnAdd['person']['contentMD'] = "TEXT NULL";
$columnAdd['report']['detailMD'] = "TEXT NULL";
$columnAdd['report']['energyMD'] = "TEXT NULL";
$columnAdd['report']['impactMD'] = "TEXT NULL";
$columnAdd['report']['inputMD'] = "TEXT NULL";
$columnAdd['report']['summaryMD'] = "TEXT NULL";
$columnAdd['sort']['userId'] = "int NOT NULL AFTER sortId";
$columnAdd['sort']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['sort']['sortColumn'] = "varchar(100) NULL AFTER objectType";
$columnAdd['sort']['sortDirection'] = "varchar(4) NULL AFTER sortColumn";
$columnAdd['symbol']['descriptionMD'] = "TEXT NULL";
$columnAdd['task']['taskMD'] = "TEXT NULL";
//$columnAdd['test2']['test'] = "int NULL after testId";
$columnAdd['user']['aclAPI'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclAudit'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclCase'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclDeputy'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclDirector'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclGamemaster'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclGroup'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclHunt'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclPerson'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclRoot'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclSecret'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['aclTask'] = "int(3) NOT NULL DEFAULT '0'";
$columnAdd['user']['userEmail'] = "VARCHAR(256) NULL AFTER pwd";
$columnAdd['user']['planMD'] = "TEXT NULL";
$columnAdd['user']['sid'] = "VARCHAR(32) NOT NULL AFTER id";
$columnAdd['user']['userSuspended'] = "INT NOT NULL DEFAULT '0' AFTER deleted";
$columnAdd['user']['userAgent'] = "VARCHAR(256) NULL AFTER ip";

/*
 * ALTER COLUMN
 */
$columnAlter['dashboard']['content_md'] = " contentMD text COLLATE 'utf8_general_ci' NULL "; //bugfix
$columnAlter['news']['obsah_md'] = " obsahMD text COLLATE 'utf8_general_ci' NULL "; //bugfix
$columnAlter['task']['modified_by'] = " `modified_by` int(4) NULL AFTER `modified`";
$columnAlter['task']['modified'] = " `modified` int(11) NULL AFTER `created_by`";
//$columnAlter['test2']['test'] = "test2 TEXT NOT NULL DEFAULT 'random string'";
//$columnAlter['test2']['test'] = "test3 TEXT NULL";
$columnAlter['user']['deleted'] = "userDeleted int(3) NOT NULL DEFAULT '0' AFTER userSuspended";
$columnAlter['user']['email'] = "userEmail VARCHAR(256) NULL AFTER userPassword";
$columnAlter['user']['filter'] = "filter text NULL AFTER planMD";  //already exist?
$columnAlter['user']['idperson'] = "personId int(6) NULL AFTER userDeleted";
$columnAlter['user']['id'] = "userId int(6) NOT NULL AUTO_INCREMENT FIRST"; //already exist?
$columnAlter['user']['ip'] = "ipv4 varchar(15) NULL AFTER lastLogin";
$columnAlter['user']['lastlogon'] = "lastLogin INT(11) NULL AFTER userEmail";
$columnAlter['user']['login'] = "userName varchar(40) NOT NULL AFTER sid"; //already exist?
$columnAlter['user']['plan_md'] = "planMD TEXT NULL AFTER aclHunt"; //bugfix
$columnAlter['user']['pwd'] = "userPassword varchar(40) NOT NULL AFTER userName"; //already exist?
$columnAlter['user']['right_aud'] = "rightAudOld int(3) NOT NULL DEFAULT '0' after planMD"; //already exist?
$columnAlter['user']['right_org'] = "rightOrgOld int(3) NOT NULL DEFAULT '0' after planMD"; //already exist?
$columnAlter['user']['right_power'] = "rightPowerOld int(3) NOT NULL DEFAULT '0' after planMD"; //already exist?
$columnAlter['user']['right_super'] = "rightSuperOld int(3) NOT NULL DEFAULT '0' after planMD"; //already exist?
$columnAlter['user']['right_text'] = "rightTextOld int(3) NOT NULL DEFAULT '0' after planMD"; //already exist?
$columnAlter['user']['sid'] = "sid varchar(32) NULL AFTER userId"; //already exist?
$columnAlter['user']['suspended'] = "userSuspended int(3) NOT NULL DEFAULT '0' AFTER userTimeout";
$columnAlter['user']['timeout'] = "userTimeout INT(6) NOT NULL DEFAULT '600' AFTER userAgent";
$columnAlter['user']['user_agent'] = "userAgent VARCHAR(256) NULL AFTER ipv4";
$columnAlter['user']['zlobody'] = "zlobod int(6) NOT NULL DEFAULT '0' AFTER personId";

/*
 * ADD FULLTEXT INDEX
 */
//$columnAddFulltext['test2'] = ['test2'];
$columnAddFulltext['case'] = ['contentMD'];
$columnAddFulltext['dashboard'] = ['contentMD'];
$columnAddFulltext['group'] = ['contentMD'];
$columnAddFulltext['news'] = ['obsahMD'];
$columnAddFulltext['note'] = ['noteMD'];
$columnAddFulltext['person'] = ['contentMD'];
$columnAddFulltext['report'] = ['summaryMD', 'impactMD', 'detailMD', 'energyMD', 'inputMD'];
$columnAddFulltext['symbol'] = ['descriptionMD'];

/*
 * CONVERT DATA TO MARKDOWN
 */
$columnToMD[] = ['dashboard', 'id', 'content', 'contentMD'];
$columnToMD[] = ['news', 'id', 'obsah', 'obsahMD'];
//$columnToMD[] = ['test2','testId','test2','test3'];
$columnToMD[] = ['user', 'userId', 'plan', 'planMD'];

/*
 * RIGHTS TO UPDATE
 */
$rightsToUpdate['rightAudOld'] = ['aclAudit'];
$rightsToUpdate['rightOrgOld'] = ['aclGamemaster'];
$rightsToUpdate['rightPowerOld'] = ['aclDirector', 'aclDeputy', 'aclSecret', 'aclHunt'];
$rightsToUpdate['rightSuperOld'] = ['aclRoot'];
$rightsToUpdate['rightTextOld'] = ['aclTask', 'aclGroup', 'aclPerson', 'aclCase'];

/*
 * ADD INDEX
 */
// ALTER TABLE nw_unread ADD INDEX(iduser)

/*
 * COLUMNS TO DROP
 */
$columnDrop['case'][] = 'contents_md'; //bugfix
$columnDrop['dashboard'][] = 'content';
$columnDrop['group'][] = 'contents_md'; //bugfix
$columnDrop['news'][] = 'obsah';
$columnDrop['news'][] = 'obsah_md'; //bugfix
$columnDrop['note'][] = 'note_md'; //bugfix
$columnDrop['person'][] = 'contents_md'; //bugfix
$columnDrop['report'][] = 'details_md'; //bugfix
$columnDrop['report'][] = 'energy_md'; //bugfix
$columnDrop['report'][] = 'impacts_md'; //bugfix
$columnDrop['report'][] = 'inputs_md'; //bugfix
$columnDrop['report'][] = 'summary_md'; //bugfix
$columnDrop['symbol'][] = 'desc_md'; //bugfix
$columnDrop['task'][] = 'task_md'; //bugfix
//$columnDrop['test2'][] = "test2";
$columnDrop['user'][] = 'email';
$columnDrop['user'][] = 'plan';
$columnDrop['user'][] = 'plan_md'; //bugfix
$columnDrop['user'][] = 'rightAudOld';
$columnDrop['user'][] = 'rightOrgOld';
$columnDrop['user'][] = 'rightPowerOld';
$columnDrop['user'][] = 'rightSuperOld';
$columnDrop['user'][] = 'rightTextOld';
$columnDrop['user'][] = 'suspended';
$columnDrop['user'][] = 'user_agent';

/*
 * DROP TABLE
 */
$tableDrop[] = 'loggedin_deleted';
$tableDrop[] = 'map_deleted';
//$tableDrop[] = 'test2';

/**
 * UPDATING.
 */
require_once 'update-function.php';

$counterTableCreate = bistroDBTableCreate($tableCreate);
$counterTableRename = bistroDBTableRename($tableRename);
$counterColumnAdd = bistroDBColumnAdd($columnAdd);
$counterColumnAlter = bistroDBColumnAlter($columnAlter);
$counterFulltextAdd = bistroDBFulltextAdd($columnAddFulltext);
//$counterIndexAdd = bistroDBIndexAdd($columnAddIndex);
$counterIndexAdd = 0;

$counterColumnMarkdown = bistroDBColumnMarkdown($columnToMD);
$counterPasswordEncrypt = bistroDBPasswordEncrypt();
$counterUPdateRight = bistroMigratePermissions($rightsToUpdate);

$counterColumnDrop = bistroDBColumnDrop($columnDrop);
$counterTableDrop = bistroDBTableDrop($tableDrop);

//pokud zmeny probehly, prejmenovat tento soubor
if ($counterColumnAdd + $counterColumnAlter + $counterColumnMarkdown + $counterFulltextAdd + $counterPasswordEncrypt
    + $counterTableRename + $counterTableDrop + $counterTableCreate + $counterIndexAdd + $counterUPdateRight
    + $counterColumnDrop > 0) {
    rename(__FILE__,__FILE__.".old");
}
