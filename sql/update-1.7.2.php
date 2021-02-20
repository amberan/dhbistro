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

/*
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
$columnAlter['task']['modified'] = " `modified` int(11) NULL AFTER `created_by`";
$columnAlter['task']['modified_by'] = " `modified_by` int(4) NULL AFTER `modified`";

/*
 * CONVERT DATA TO MARKDOWN
 */
$columnToMD[] = ['user', 'id', 'plan', 'plan_md'];
$columnToMD[] = ['dashboard', 'id', 'content', 'content_md'];
$columnToMD[] = ['news', 'id', 'obsah', 'obsah_md'];

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

/*
 * COLUMNS TO DROP
 */
$columnDrop['test2'][] = "test2";
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

use Tracy\Debugger;

Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
require_once 'update-function.php';

$counterTableCreate = bistroDBTableCreate($tableCreate);
$counterTableRename = bistroDBTableRename($tableRename);
$counterColumnAdd = bistroDBColumnAdd($columnAdd);
$counterColumnAlter = bistroDBColumnAlter($columnAlter);
$counterColumnMarkdown = bistroDBColumnMarkdown($columnToMD);
$counterPasswordEncrypt = bistroDBPasswordEncrypt();
$counterUPdateRight = bistroMigrateRights($rightsToUpdate);
$counterFulltextAdd = bistroDBFulltextAdd($columnAddFulltext);
$counterIndexAdd = 0;
$counterColumnDrop = bistroDBColumnDrop($columnDrop);
$counterTableDrop = bistroDBTableDrop($tableDrop);

//pokud zmeny probehly, prejmenovat tento soubor
if ($counterColumnAdd + $counterColumnAlter + $counterColumnMarkdown + $counterFulltextAdd + $counterPasswordEncrypt
    + $counterTableRename + $counterTableDrop + $counterTableCreate + $counterIndexAdd + $counterUPdateRight
    + $counterColumnDrop > 0) {
    rename(__FILE__,__FILE__.".old");
}
