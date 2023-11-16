<?php

/*
 * CREATE TABLE
 */
$tableCreate['filter'] = 'filterId';
$tableCreate['sort'] = 'sortId';

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
$tableRename['users'] = "user";

/*
 * ADD COLUMN
 */
$columnAdd['filter']['userId'] = "int NOT NULL AFTER filterId";
$columnAdd['filter']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['filter']['filterPreference'] = "varchar(200) NULL AFTER objectType";
$columnAdd['sort']['userId'] = "int NOT NULL AFTER sortId";
$columnAdd['sort']['objectType'] = "varchar(15) NOT NULL AFTER userId";
$columnAdd['sort']['sortColumn'] = "varchar(100) NULL AFTER objectType";
$columnAdd['sort']['sortDirection'] = "varchar(4) NULL AFTER sortColumn";
$columnAdd['task']['taskMD'] = "TEXT NULL";
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
$columnAdd['case']['contentMD'] = "TEXT NULL";

/*
 * ALTER COLUMN
 */
$columnAlter['dashboard']['content_md'] = 'contentMD text NULL ';
$columnAlter['group']['contents_md'] = 'contentMD TEXT NULL';
$columnAlter['note']['note_md'] = 'noteMD TEXT NULL';
$columnAlter['person']['contents_md'] = 'contentMD TEXT NULL';
$columnAlter['report']['details_md'] = 'detailMD TEXT NULL';
$columnAlter['report']['energy_md'] = 'energyMD TEXT NULL';
$columnAlter['report']['impacts_md'] = 'impactMD TEXT NULL';
$columnAlter['report']['inputs_md'] = 'inputMD TEXT NULL';
$columnAlter['report']['summary_md'] = 'summaryMD TEXT NULL';
$columnAlter['symbol']['desc_md'] = 'descriptionMD TEXT NULL';
$columnAlter['news']['obsah_md'] = 'obsahMD text NULL ';
$columnAlter['task']['modified_by'] = 'modified_by int(4) NULL AFTER modified';
$columnAlter['task']['modified'] = 'modified int(11) NULL AFTER created_by';
$columnAlter['user']['id'] = 'userId int(6) NOT NULL AUTO_INCREMENT FIRST';
$columnAlter['user']['login'] = 'userName varchar(40) NOT NULL AFTER sid';
$columnAlter['user']['pwd'] = 'userPassword varchar(40) NOT NULL AFTER userName';
$columnAlter['user']['lastlogon'] = 'lastLogin INT(11) NULL AFTER userEmail';
$columnAlter['user']['ip'] = 'ipv4 varchar(15) NULL AFTER lastLogin';
$columnAlter['user']['user_agent'] = 'userAgent VARCHAR(256) NULL AFTER ipv4';
$columnAlter['user']['timeout'] = 'userTimeout INT(6) NOT NULL DEFAULT "600" AFTER userAgent';
$columnAlter['user']['suspended'] = 'userSuspended int(3) NOT NULL DEFAULT "0" AFTER userTimeout';
$columnAlter['user']['deleted'] = 'userDeleted int(3) NOT NULL DEFAULT "0" AFTER userSuspended';
$columnAlter['user']['idperson'] = 'personId int(6) NULL AFTER userDeleted';
$columnAlter['user']['zlobody'] = 'zlobod int(6) NOT NULL DEFAULT "0" AFTER personId';
$columnAlter['user']['plan_md'] = 'planMD TEXT NULL AFTER aclHunt';
$columnAlter['user']['filter'] = 'filter text NULL AFTER planMD';
$columnAlter['user']['right_aud'] = 'rightAudOld int(3) NOT NULL DEFAULT "0" after planMD';
$columnAlter['user']['right_org'] = 'rightOrgOld int(3) NOT NULL DEFAULT "0" after planMD';
$columnAlter['user']['right_power'] = 'rightPowerOld int(3) NOT NULL DEFAULT "0" after planMD';
$columnAlter['user']['right_super'] = 'rightSuperOld int(3) NOT NULL DEFAULT "0" after planMD';
$columnAlter['user']['right_text'] = 'rightTextOld int(3) NOT NULL DEFAULT "0" after planMD';
/*
 * CONVERT DATA TO MARKDOWN
 */
$columnToMD[] = ['dashboard', 'id', 'content', 'contentMD'];
$columnToMD[] = ['news', 'id', 'obsah', 'obsahMD'];
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
 * ADD FULLTEXT INDEX
 */
$columnAddFulltext['case'] = ['contentMD'];
$columnAddFulltext['dashboard'] = ['contentMD'];
$columnAddFulltext['group'] = ['contentMD'];
$columnAddFulltext['news'] = ['obsahMD'];
$columnAddFulltext['note'] = ['noteMD'];
$columnAddFulltext['person'] = ['contentMD'];
$columnAddFulltext['report'] = ['summaryMD', 'impactMD', 'detailMD', 'energyMD', 'inputMD'];
$columnAddFulltext['symbol'] = ['descriptionMD'];
/*
 * COLUMNS TO DROP
 */
$columnDrop['dashboard'][] = 'content';
$columnDrop['news'][] = 'obsah';
$columnDrop['user'][] = 'plan';
/*
 * DROP TABLE
 */
$tableDrop[] = 'loggedin_deleted';
$tableDrop[] = 'map_deleted';

// #98 insert new value to audit enum
$updateScript[98] = 'INSERT INTO ' . DB_PREFIX . 'operation_type (id,name) VALUES ("15","neopravněný pokus o řístup k tajnému") ON DUPLICATE KEY UPDATE id="15", name="neopravněný pokus o Přístup k tajnému"';
