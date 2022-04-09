<?php
/*
 * ADD COLUMN
 */
$columnAdd['report']['reportOwner'] = "int(6) NULL AFTER iduser";
$columnAdd['report']['reportModifiedBy'] = "int(6) NULL AFTER adatum";
$columnAdd['report']['reportArchived'] = "timestamp NULL AFTER status";
$columnAdd['report']['reportDeleted'] = " timestamp NULL after deleted";
$columnAdd['report']['reportTask'] = "text NULL AFTER inputs";
$columnAdd['report']['reportModified'] = " timestamp NULL after datum";
$columnAdd['report']['reportCreated'] = " timestamp NULL after datum";
$columnAdd['report']['reportEventDate'] = " timestamp NULL after adatum";

/*
 * ALTER COLUMN
 */
$columnAlter['report']['id'] = "reportId int(6) NOT NULL AUTO_INCREMENT";
$columnAlter['report']['label'] = "reportName tinytext";
$columnAlter['report']['iduser'] = "reportCreatedBy int(6)";
$columnAlter['report']['secret'] = "reportSecret int(3)";
$columnAlter['report']['status'] = "reportStatus int(3)";
$columnAlter['report']['type'] = "reportType int(3)";
$columnAlter['report']['start'] = "reportEventStart varchar(50) NULL";
$columnAlter['report']['end'] = "reportEventEnd varchar(50) NULL";
$columnAlter['report']['detailMD'] = "reportDetail text NULL";
$columnAlter['report']['energyMD'] = "reportCost text NULL";
$columnAlter['report']['impactMD'] = "reportImpact text NULL";
$columnAlter['report']['inputMD'] = "reportInput text NULL";
$columnAlter['report']['summaryMD'] = "reportSummary text NULL";
$columnAlter['sort']['userId'] = "userId int(6)";
$columnAlter['filter']['userId'] = "userId int(6)";

/*
 * TIME TO CONVERT
 */
$convertTime[] = ['report','datum','reportModified'];
$convertTime[] = ['report','adatum','reportEventDate'];
$convertTime[] = ['report','adatum','reportCreated'];
$convertTime[] = ['report','deleted','reportDeleted'];

/*
 * CONVERT DATA TO MARKDOWN
 */
$columnToMD[] = ['report', 'reportId', 'task', 'reportTask'];
$columnToMD[] = ['report', 'reportId', 'details', 'reportDetail'];
$columnToMD[] = ['report', 'reportId', 'energy', 'reportCost'];
$columnToMD[] = ['report', 'reportId', 'summary', 'reportSummary'];
$columnToMD[] = ['report', 'reportId', 'impacts', 'reportImpact'];
$columnToMD[] = ['report', 'reportId', 'inputs', 'reportInput'];

/*
 * ADD FULLTEXT INDEX
 */
$columnAddFulltext['report'] = ['reportTask','reportDetail','reportCost','reportSummary','reportImpact','reportInput'];

/*
 * ADD INDEX
 */
$columnAddIndex['unread']['filter'] = ['idtable', 'idrecord', 'iduser'];
$columnAddIndex['report']['filter'] = ['reportSecret','reportStatus','reportType','reportDeleted','reportModifiedBy','reportCreatedBy','reportOwner','reportId'];
$columnAddIndex['user']['filter'] = ['userDeleted','userId','personId'];
$columnAddIndex['person']['filter'] = ['side','spec','power','dead','surname','regdate','datum','iduser','id','deleted','secret','archived'];



$updateScript[114_1] = 'UPDATE '.DB_PREFIX.'report SET reportArchived=FROM_UNIXTIME("1") WHERE reportStatus=3';
$updateScript[114_2] = 'UPDATE '.DB_PREFIX.'report SET reportOwner = reportCreatedBy, reportModifiedBy = reportCreatedBy';
