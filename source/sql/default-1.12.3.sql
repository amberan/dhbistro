-- Adminer 4.8.1 MySQL 5.5.5-10.11.6-MariaDB-1:10.11.6+maria~ubu2204 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `nw_ar2c`;
CREATE TABLE `nw_ar2c` (
  `idreport` int(11) NOT NULL DEFAULT 0,
  `idcase` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idreport`,`idcase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_ar2p`;
CREATE TABLE `nw_ar2p` (
  `idperson` int(11) NOT NULL DEFAULT 0,
  `idreport` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `role` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idperson`,`idreport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_audit_trail`;
CREATE TABLE `nw_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `time` int(11) NOT NULL DEFAULT 0,
  `operation_type` int(11) NOT NULL,
  `record_type` int(11) NOT NULL,
  `idrecord` int(11) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `org` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_backup`;
CREATE TABLE `nw_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT 0,
  `file` varchar(255) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '1.5.5 =<',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_c2p`;
CREATE TABLE `nw_c2p` (
  `idperson` int(11) NOT NULL DEFAULT 0,
  `idcase` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idperson`,`idcase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_c2s`;
CREATE TABLE `nw_c2s` (
  `idsolver` int(11) NOT NULL DEFAULT 0,
  `idcase` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idsolver`,`idcase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_case`;
CREATE TABLE `nw_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `contents` text NOT NULL,
  `secret` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `caseArchived` timestamp NULL DEFAULT NULL,
  `contentMD` text DEFAULT NULL,
  `caseCreated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `title_2` (`title`),
  FULLTEXT KEY `contents_2` (`contents`),
  FULLTEXT KEY `contentMD` (`contentMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_dashboard`;
CREATE TABLE `nw_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `contentMD` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_doodle`;
CREATE TABLE `nw_doodle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL DEFAULT 0,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_file`;
CREATE TABLE `nw_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniquename` varchar(255) NOT NULL DEFAULT '',
  `originalname` varchar(255) NOT NULL DEFAULT '',
  `mime` varchar(100) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT 0,
  `datum` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `idtable` int(11) NOT NULL DEFAULT 0,
  `iditem` int(11) NOT NULL DEFAULT 0,
  `secret` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_filter`;
CREATE TABLE `nw_filter` (
  `filterId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(6) DEFAULT NULL,
  `objectType` varchar(15) NOT NULL,
  `filterPreference` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`filterId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_g2p`;
CREATE TABLE `nw_g2p` (
  `idperson` int(11) NOT NULL DEFAULT 0,
  `idgroup` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idperson`,`idgroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_group`;
CREATE TABLE `nw_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `contents` text NOT NULL,
  `datum` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `secret` int(11) NOT NULL DEFAULT 0,
  `archived` int(11) NOT NULL,
  `contentsMD` text DEFAULT NULL,
  `groupCreated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `title_2` (`title`),
  FULLTEXT KEY `contents_2` (`contents`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_news`;
CREATE TABLE `nw_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `kategorie` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `nadpis` varchar(255) NOT NULL DEFAULT '',
  `obsahMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `obsahMD` (`obsahMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_note`;
CREATE TABLE `nw_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `idtable` int(11) NOT NULL DEFAULT 0,
  `iditem` int(11) NOT NULL DEFAULT 0,
  `secret` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `noteMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `note` (`note`),
  FULLTEXT KEY `title_2` (`title`),
  FULLTEXT KEY `note_2` (`note`),
  FULLTEXT KEY `noteMD` (`noteMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_operation_type`;
CREATE TABLE `nw_operation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_person`;
CREATE TABLE `nw_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL DEFAULT '',
  `surname` varchar(70) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `contents` text NOT NULL,
  `secret` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `archived` timestamp NULL DEFAULT NULL,
  `portrait` varchar(255) NOT NULL DEFAULT '',
  `side` int(11) NOT NULL DEFAULT 0,
  `power` int(11) NOT NULL DEFAULT 0,
  `roof` timestamp NULL DEFAULT NULL,
  `spec` int(11) NOT NULL DEFAULT 0,
  `symbol` varchar(255) NOT NULL DEFAULT '',
  `dead` int(11) NOT NULL DEFAULT 0,
  `regdate` int(11) NOT NULL DEFAULT 0,
  `regid` int(11) NOT NULL DEFAULT 0,
  `contentMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filter` (`side`,`spec`,`power`,`dead`,`surname`,`regdate`,`datum`,`iduser`,`id`,`deleted`,`secret`,`archived`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `surname_2` (`surname`),
  FULLTEXT KEY `name_2` (`name`),
  FULLTEXT KEY `contents_2` (`contents`),
  FULLTEXT KEY `contentMD` (`contentMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_record_type`;
CREATE TABLE `nw_record_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_report`;
CREATE TABLE `nw_report` (
  `reportId` int(6) NOT NULL AUTO_INCREMENT,
  `reportName` tinytext DEFAULT NULL,
  `reportCreated` timestamp NULL DEFAULT NULL,
  `reportModified` timestamp NULL DEFAULT NULL,
  `reportCreatedBy` int(6) DEFAULT NULL,
  `reportOwner` int(6) DEFAULT NULL,
  `reportSecret` int(3) DEFAULT NULL,
  `reportDeleted` timestamp NULL DEFAULT NULL,
  `reportStatus` int(3) DEFAULT NULL,
  `reportArchived` timestamp NULL DEFAULT NULL,
  `reportType` int(3) DEFAULT NULL,
  `reportEventDate` timestamp NULL DEFAULT NULL,
  `reportModifiedBy` int(6) DEFAULT NULL,
  `reportEventStart` varchar(50) DEFAULT NULL,
  `reportEventEnd` varchar(50) DEFAULT NULL,
  `reportTask` text DEFAULT NULL,
  `reportDetail` text DEFAULT NULL,
  `reportCost` text DEFAULT NULL,
  `reportImpact` text DEFAULT NULL,
  `reportInput` text DEFAULT NULL,
  `reportSummary` text DEFAULT NULL,
  PRIMARY KEY (`reportId`),
  KEY `filter` (`reportSecret`,`reportStatus`,`reportType`,`reportDeleted`,`reportModifiedBy`,`reportCreatedBy`,`reportOwner`,`reportId`),
  FULLTEXT KEY `label` (`reportName`),
  FULLTEXT KEY `label_2` (`reportName`),
  FULLTEXT KEY `summaryMD` (`reportSummary`),
  FULLTEXT KEY `impactMD` (`reportImpact`),
  FULLTEXT KEY `detailMD` (`reportDetail`),
  FULLTEXT KEY `energyMD` (`reportCost`),
  FULLTEXT KEY `inputMD` (`reportInput`),
  FULLTEXT KEY `reportTask` (`reportTask`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_sort`;
CREATE TABLE `nw_sort` (
  `sortId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(6) DEFAULT NULL,
  `objectType` varchar(15) NOT NULL,
  `sortColumn` varchar(100) DEFAULT NULL,
  `sortDirection` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`sortId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_symbol`;
CREATE TABLE `nw_symbol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `archived` timestamp NULL DEFAULT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `assigned` int(11) NOT NULL,
  `search_lines` int(11) NOT NULL DEFAULT 0,
  `search_curves` int(11) NOT NULL DEFAULT 0,
  `search_points` int(11) NOT NULL DEFAULT 0,
  `search_geometricals` int(11) NOT NULL DEFAULT 0,
  `search_alphabets` int(11) NOT NULL DEFAULT 0,
  `search_specialchars` int(11) NOT NULL DEFAULT 0,
  `secret` int(11) NOT NULL,
  `descriptionMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `desc` (`desc`),
  FULLTEXT KEY `descriptionMD` (`descriptionMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_symbol2all`;
CREATE TABLE `nw_symbol2all` (
  `idsymbol` int(11) NOT NULL DEFAULT 0,
  `idrecord` int(11) NOT NULL DEFAULT 0,
  `iduser` int(11) NOT NULL DEFAULT 0,
  `table` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idsymbol`,`idrecord`,`table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_task`;
CREATE TABLE `nw_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text NOT NULL,
  `iduser` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `modified_by` int(4) DEFAULT NULL,
  `taskMD` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_unread`;
CREATE TABLE `nw_unread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filter` (`idtable`,`idrecord`,`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `nw_user`;
CREATE TABLE `nw_user` (
  `userId` int(6) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) DEFAULT NULL,
  `userName` varchar(40) NOT NULL,
  `userPassword` varchar(40) NOT NULL,
  `userEmail` varchar(256) DEFAULT NULL,
  `lastLogin` int(11) DEFAULT NULL,
  `ipv4` varchar(15) DEFAULT NULL,
  `userAgent` varchar(512) DEFAULT NULL,
  `userTimeout` int(6) NOT NULL DEFAULT 600,
  `userSuspended` int(11) NOT NULL DEFAULT 0,
  `userDeleted` int(3) NOT NULL DEFAULT 0,
  `personId` int(6) DEFAULT NULL,
  `zlobod` int(6) NOT NULL DEFAULT 0,
  `aclAPI` int(3) NOT NULL DEFAULT 0,
  `aclAudit` int(3) NOT NULL DEFAULT 0,
  `aclCase` int(3) NOT NULL DEFAULT 0,
  `aclDeputy` int(3) NOT NULL DEFAULT 0,
  `aclUser` int(3) NOT NULL DEFAULT 0,
  `aclNews` int(3) NOT NULL DEFAULT 0,
  `aclBoard` int(3) NOT NULL DEFAULT 0,
  `aclGamemaster` int(3) NOT NULL DEFAULT 0,
  `aclGroup` int(3) NOT NULL DEFAULT 0,
  `aclHunt` int(3) NOT NULL DEFAULT 0,
  `aclPerson` int(3) NOT NULL DEFAULT 0,
  `aclRoot` int(3) NOT NULL DEFAULT 0,
  `aclSecret` int(3) NOT NULL DEFAULT 0,
  `aclTask` int(3) NOT NULL DEFAULT 0,
  `aclReport` int(3) NOT NULL DEFAULT 0,
  `aclSymbol` int(3) NOT NULL DEFAULT 0,
  `planMD` text DEFAULT NULL,
  `filter` text DEFAULT NULL,
  `aclDirector` int(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`userId`),
  KEY `filter` (`userDeleted`,`userId`,`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

INSERT INTO `nw_user` (`userId`, `sid`, `userName`, `userPassword`, `userEmail`, `lastLogin`, `ipv4`, `userAgent`, `userTimeout`, `userSuspended`, `userDeleted`, `personId`, `zlobod`, `aclAPI`, `aclAudit`, `aclCase`, `aclDeputy`, `aclUser`, `aclNews`, `aclBoard`, `aclGamemaster`, `aclGroup`, `aclHunt`, `aclPerson`, `aclRoot`, `aclSecret`, `aclTask`, `aclReport`, `aclSymbol`, `planMD`, `filter`, `aclDirector`) VALUES
(1,	'',	'Shiva',	'3fbff0e6b620e4d259dc427abc6574da',	NULL,	0,	'',	'',	3600,	0,	0,	0,	0,	0,	1,	2,	1,	1,	1,	1,	1,	2,	1,	2,	1,	1,	1,	2,	2,	'',	'',	1);

-- 2023-11-19 22:33:55
