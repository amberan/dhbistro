-- Adminer 4.7.9 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `nw_ar2c`;
CREATE TABLE `nw_ar2c` (
  `idreport` int(11) NOT NULL,
  `idcase` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idreport`,`idcase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_ar2p`;
CREATE TABLE `nw_ar2p` (
  `idperson` int(11) NOT NULL,
  `idreport` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idreport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_audit_trail`;
CREATE TABLE `nw_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `operation_type` int(11) NOT NULL,
  `record_type` int(11) NOT NULL,
  `idrecord` int(11) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `org` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_backup`;
CREATE TABLE `nw_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '1.8.1 =<',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_c2p`;
CREATE TABLE `nw_c2p` (
  `idperson` int(11) NOT NULL,
  `idcase` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idcase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_c2s`;
CREATE TABLE `nw_c2s` (
  `idsolver` int(11) NOT NULL,
  `idcase` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idsolver`,`idcase`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_case`;
CREATE TABLE `nw_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `contents` text NOT NULL,
  `secret` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `contentMD` text DEFAULT NULL,
  `caseCreated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `contentMD` (`contentMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_dashboard`;
CREATE TABLE `nw_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `contentMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `contentMD` (`contentMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_doodle`;
CREATE TABLE `nw_doodle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_file`;
CREATE TABLE `nw_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniquename` varchar(255) NOT NULL,
  `originalname` varchar(255) NOT NULL,
  `mime` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idtable` int(11) NOT NULL,
  `iditem` int(11) NOT NULL,
  `secret` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `nw_filter`;
CREATE TABLE `nw_filter` (
  `filterId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `objectType` varchar(15) NOT NULL,
  `filterPreference` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`filterId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `nw_g2p`;
CREATE TABLE `nw_g2p` (
  `idperson` int(11) NOT NULL,
  `idgroup` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idgroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_group`;
CREATE TABLE `nw_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `contents` text NOT NULL,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `secret` int(11) NOT NULL,
  `archived` int(11) NOT NULL,
  `contentsMD` text DEFAULT NULL,
  `groupCreated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_news`;
CREATE TABLE `nw_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `kategorie` int(11) NOT NULL,
  `deleted` int(3) NOT NULL,
  `nadpis` varchar(255) NOT NULL,
  `obsahMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `obsahMD` (`obsahMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_note`;
CREATE TABLE `nw_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idtable` int(11) NOT NULL,
  `iditem` int(11) NOT NULL,
  `secret` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `noteMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `note` (`note`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `noteMD` (`noteMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_operation_type`;
CREATE TABLE `nw_operation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `nw_operation_type` (`id`, `name`) VALUES
(1,	'čtení'),
(2,	'úprava'),
(3,	'nový'),
(4,	'přiložení souboru'),
(5,	'odstranění souboru'),
(6,	'provazba'),
(7,	'nová poznámka'),
(8,	'smazání poznámky'),
(9,	'úprava poznámy'),
(10,	'organizační zásah'),
(11,	'smazání záznamu'),
(12,	'pokus o neoprávněný přístup'),
(13,	'pokus o přístup ke smazanému záznamu'),
(14,	'vyhledávání');

DROP TABLE IF EXISTS `nw_person`;
CREATE TABLE `nw_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `surname` varchar(70) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `contents` text NOT NULL,
  `secret` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `archived` timestamp NULL DEFAULT NULL,
  `portrait` varchar(255) DEFAULT NULL,
  `side` int(11) NOT NULL,
  `power` int(11) NOT NULL,
  `roof` timestamp NULL DEFAULT NULL,
  `spec` int(11) NOT NULL,
  `symbol` varchar(255) DEFAULT NULL,
  `dead` int(11) NOT NULL,
  `regdate` int(11) NOT NULL,
  `regid` int(11) NOT NULL,
  `contentMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `contentMD` (`contentMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_record_type`;
CREATE TABLE `nw_record_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `nw_record_type` (`id`, `name`) VALUES
(1,	'osoba'),
(2,	'skupina'),
(3,	'případ'),
(4,	'hlášení'),
(5,	'novinky'),
(6,	'nástěnka'),
(7,	'symbol'),
(8,	'uživatel'),
(9,	'zlobody'),
(10,	'úkoly'),
(11,	'audit'),
(12,	'jiné');

DROP TABLE IF EXISTS `nw_report`;
CREATE TABLE `nw_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `impacts` text NOT NULL,
  `details` text NOT NULL,
  `secret` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `adatum` int(11) NOT NULL,
  `start` varchar(50) NOT NULL,
  `end` varchar(50) NOT NULL,
  `energy` text NOT NULL,
  `inputs` text NOT NULL,
  `detailMD` text DEFAULT NULL,
  `energyMD` text DEFAULT NULL,
  `impactMD` text DEFAULT NULL,
  `inputMD` text DEFAULT NULL,
  `summaryMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `label` (`label`),
  FULLTEXT KEY `task` (`task`),
  FULLTEXT KEY `summary` (`summary`),
  FULLTEXT KEY `impacts` (`impacts`),
  FULLTEXT KEY `details` (`details`),
  FULLTEXT KEY `detailMD` (`detailMD`),
  FULLTEXT KEY `energyMD` (`energyMD`),
  FULLTEXT KEY `impactMD` (`impactMD`),
  FULLTEXT KEY `inputMD` (`inputMD`),
  FULLTEXT KEY `summaryMD` (`summaryMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_sort`;
CREATE TABLE `nw_sort` (
  `sortId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `objectType` varchar(15) NOT NULL,
  `sortColumn` varchar(100) DEFAULT NULL,
  `sortDirection` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`sortId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `nw_symbol`;
CREATE TABLE `nw_symbol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `deleted` int(11) NOT NULL,
  `archived` timestamp NULL DEFAULT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `assigned` int(11) NOT NULL,
  `search_lines` int(11) NOT NULL,
  `search_curves` int(11) NOT NULL,
  `search_points` int(11) NOT NULL,
  `search_geometricals` int(11) NOT NULL,
  `search_alphabets` int(11) NOT NULL,
  `search_specialchars` int(11) NOT NULL,
  `secret` int(11) NOT NULL,
  `descriptionMD` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `desc` (`desc`),
  FULLTEXT KEY `descriptionMD` (`descriptionMD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_symbol2all`;
CREATE TABLE `nw_symbol2all` (
  `idsymbol` int(11) NOT NULL,
  `idrecord` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `table` int(11) NOT NULL,
  PRIMARY KEY (`idsymbol`,`idrecord`,`table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_task`;
CREATE TABLE `nw_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text NOT NULL,
  `iduser` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `modified_by` int(4) DEFAULT NULL,
  `taskMD` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_unread`;
CREATE TABLE `nw_unread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_user`;
CREATE TABLE `nw_user` (
  `userId` int(6) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) DEFAULT NULL,
  `userName` varchar(40) NOT NULL,
  `userPassword` varchar(40) NOT NULL,
  `userEmail` varchar(256) DEFAULT NULL,
  `lastLogin` int(11) DEFAULT NULL,
  `ipv4` varchar(15) DEFAULT NULL,
  `userAgent` varchar(256) DEFAULT NULL,
  `userTimeout` int(6) NOT NULL DEFAULT 600,
  `userSuspended` int(11) NOT NULL,
  `userDeleted` int(3) NOT NULL,
  `personId` int(6) DEFAULT NULL,
  `zlobod` int(6) NOT NULL,
  `plan` text NOT NULL,
  `aclAPI` int(3) NOT NULL,
  `aclAudit` int(3) NOT NULL,
  `aclCase` int(3) NOT NULL,
  `aclNews` int(3) NOT NULL DEFAULT 0,
  `aclUser` int(3) NOT NULL DEFAULT 0,
  `aclBoard` int(3) NOT NULL DEFAULT 0,
  `aclGamemaster` int(3) NOT NULL,
  `aclGroup` int(3) NOT NULL,
  `aclHunt` int(3) NOT NULL,
  `aclPerson` int(3) NOT NULL,
  `aclRoot` int(3) NOT NULL,
  `aclSecret` int(3) NOT NULL,
  `aclTask` int(3) NOT NULL,
  `aclReport` int(3) NOT NULL,
  `aclSymbol` int(3) NOT NULL,
  `planMD` text DEFAULT NULL,
  `filter` text DEFAULT NULL,
  `aclDirector` int(3) NOT NULL DEFAULT 0,
  `aclDeputy` int(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `nw_user` (`userId`, `sid`, `userName`, `userPassword`, `userEmail`, `lastLogin`, `ipv4`, `userAgent`, `userTimeout`, `userSuspended`, `userDeleted`, `personId`, `zlobod`, `plan`, `aclAPI`, `aclAudit`, `aclCase`, `aclNews`, `aclUser`, `aclBoard`, `aclGamemaster`, `aclGroup`, `aclHunt`, `aclPerson`, `aclRoot`, `aclSecret`, `aclTask`, `aclReport`, `aclSymbol`, `planMD`, `filter`, `aclDirector`, `aclDeputy`) VALUES
(1,	'',	'admin',	'24aa1b323cd7b2b8324a4ed27e5b01ce',	'',	1631617253,	'127.0.0.1',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',	600,	0,	0,	0,	0,	'',	1,	1,	2,	1,	1,	1,	1,	2,	1,	2,	1,	1,	1,	2,	2,	'',	'',	2,	2);

-- 2021-11-23 20:57:12
