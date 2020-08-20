SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
SET autocommit=0; 
SET unique_checks=0; 

-- Struktura tabulky nw_ar2c
CREATE TABLE `nw_ar2c`(
`idreport` int(11) NOT NULL,
`idcase` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idreport`,`idcase`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_ar2p
CREATE TABLE `nw_ar2p`(
`idperson` int(11) NOT NULL,
`idreport` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`role` int(11) NOT NULL,
PRIMARY KEY(`idperson`,`idreport`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_audit_trail
CREATE TABLE `nw_audit_trail`(
`id` int(11) NOT NULL auto_increment,
`iduser` int(11) NOT NULL,
`time` int(11) NOT NULL,
`operation_type` int(11) NOT NULL,
`record_type` int(11) NOT NULL,
`idrecord` int(11) NOT NULL,
`ip` varchar(100) NOT NULL,
`org` int(11) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_backup
CREATE TABLE `nw_backup`(
`id` int(11) NOT NULL auto_increment,
`time` int(11) NOT NULL,
`file` varchar(255) NOT NULL,
`version` varchar(50) NOT NULL DEFAULT '1.6.2 =<',
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_c2p
CREATE TABLE `nw_c2p`(
`idperson` int(11) NOT NULL,
`idcase` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idperson`,`idcase`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_c2s
CREATE TABLE `nw_c2s`(
`idsolver` int(11) NOT NULL,
`idcase` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idsolver`,`idcase`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_case
CREATE TABLE `nw_case`(
`id` int(11) NOT NULL auto_increment,
`title` varchar(255) NOT NULL,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`contents` text NOT NULL,
`secret` int(11) NOT NULL,
`deleted` int(11) NOT NULL,
`status` int(11) NOT NULL,
`contents_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`title`), FULLTEXT (`contents`), FULLTEXT (`contents_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_dashboard
CREATE TABLE `nw_dashboard`(
`id` int(11) NOT NULL auto_increment,
`created` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`content` text NOT NULL,
`content_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`content_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_doodle
CREATE TABLE `nw_doodle`(
`id` int(11) NOT NULL auto_increment,
`datum` int(11) NOT NULL,
`link` varchar(255) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_file
CREATE TABLE `nw_file`(
`id` int(11) NOT NULL auto_increment,
`uniquename` varchar(255) NOT NULL,
`originalname` varchar(255) NOT NULL,
`mime` varchar(100) NOT NULL,
`size` int(11) NOT NULL,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`idtable` int(11) NOT NULL,
`iditem` int(11) NOT NULL,
`secret` int(11) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_g2p
CREATE TABLE `nw_g2p`(
`idperson` int(11) NOT NULL,
`idgroup` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idperson`,`idgroup`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_group
CREATE TABLE `nw_group`(
`id` int(11) NOT NULL auto_increment,
`title` varchar(255) NOT NULL,
`contents` text NOT NULL,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`deleted` int(11) NOT NULL,
`secret` int(11) NOT NULL,
`archived` int(11) NOT NULL,
`contents_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`title`), FULLTEXT (`contents`), FULLTEXT (`contents_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_news
CREATE TABLE `nw_news`(
`id` int(11) NOT NULL auto_increment,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`kategorie` int(11) NOT NULL,
`deleted` int(3) NOT NULL,
`nadpis` varchar(255) NOT NULL,
`obsah` text NOT NULL,
`obsah_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`obsah_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_note
CREATE TABLE `nw_note`(
`id` int(11) NOT NULL auto_increment,
`note` text NOT NULL,
`title` varchar(255) NOT NULL,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`idtable` int(11) NOT NULL,
`iditem` int(11) NOT NULL,
`secret` int(11) NOT NULL,
`deleted` int(11) NOT NULL,
`note_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`note`), FULLTEXT (`title`), FULLTEXT (`note_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_operation_type
CREATE TABLE `nw_operation_type`(
`id` int(11) NOT NULL auto_increment,
`name` varchar(70) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_person
CREATE TABLE `nw_person`(
`id` int(11) NOT NULL auto_increment,
`name` varchar(70) NOT NULL,
`surname` varchar(70) NOT NULL,
`phone` varchar(50) NOT NULL,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`contents` text NOT NULL,
`secret` int(11) NOT NULL,
`deleted` int(11) NOT NULL,
`portrait` varchar(255) NOT NULL,
`side` int(11) NOT NULL,
`power` int(11) NOT NULL,
`spec` int(11) NOT NULL,
`symbol` varchar(255) NOT NULL,
`dead` int(11) NOT NULL,
`archiv` int(11) NOT NULL,
`regdate` int(11) NOT NULL,
`regid` int(11) NOT NULL,
`contents_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`name`), FULLTEXT (`surname`), FULLTEXT (`contents`), FULLTEXT (`contents_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_record_type
CREATE TABLE `nw_record_type`(
`id` int(11) NOT NULL auto_increment,
`name` varchar(70) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_report
CREATE TABLE `nw_report`(
`id` int(11) NOT NULL auto_increment,
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
`summary_md` text NULL,
`impacts_md` text NULL,
`details_md` text NULL,
`energy_md` text NULL,
`inputs_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`label`), FULLTEXT (`task`), FULLTEXT (`summary`), FULLTEXT (`impacts`), FULLTEXT (`details`), FULLTEXT (`summary_md`), FULLTEXT (`impacts_md`), FULLTEXT (`details_md`), FULLTEXT (`energy_md`), FULLTEXT (`inputs_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_sort
CREATE TABLE `nw_sort`(
`sortId` int(11) NOT NULL auto_increment,
`userId` int(11) NOT NULL,
`objectType` varchar(15) NOT NULL,
`sortColumn` varchar(100) NULL,
`sortDirection` varchar(4) NULL,
PRIMARY KEY(`sortId`)
) ENGINE=Aria COLLATE=utf8_czech_ci;
-- Struktura tabulky nw_symbol
CREATE TABLE `nw_symbol`(
`id` int(11) NOT NULL auto_increment,
`symbol` varchar(255) NOT NULL,
`desc` text NOT NULL,
`deleted` int(11) NOT NULL,
`created` int(11) NOT NULL,
`created_by` int(11) NOT NULL,
`modified` int(11) NOT NULL,
`modified_by` int(11) NOT NULL,
`archiv` int(11) NOT NULL,
`assigned` int(11) NOT NULL,
`search_lines` int(11) NOT NULL,
`search_curves` int(11) NOT NULL,
`search_points` int(11) NOT NULL,
`search_geometricals` int(11) NOT NULL,
`search_alphabets` int(11) NOT NULL,
`search_specialchars` int(11) NOT NULL,
`secret` int(11) NOT NULL,
`desc_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`desc`), FULLTEXT (`desc_md`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_symbol2all
CREATE TABLE `nw_symbol2all`(
`idsymbol` int(11) NOT NULL,
`idrecord` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`table` int(11) NOT NULL,
PRIMARY KEY(`idsymbol`,`idrecord`,`table`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_task
CREATE TABLE `nw_task`(
`id` int(11) NOT NULL auto_increment,
`task` text NOT NULL,
`iduser` int(11) NOT NULL,
`status` int(11) NOT NULL,
`created` int(11) NOT NULL,
`created_by` int(11) NOT NULL,
`modified` int(11) NULL,
`modified_by` int(4) NULL,
`task_md` text NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_unread
CREATE TABLE `nw_unread`(
`id` int(11) NOT NULL auto_increment,
`idtable` int(11) NULL,
`idrecord` int(11) NULL,
`iduser` int(11) NULL,
PRIMARY KEY(`id`)
) ENGINE=Aria COLLATE=utf8_general_ci;
-- Struktura tabulky nw_user
CREATE TABLE `nw_user`(
`userId` int(6) NOT NULL auto_increment,
`sid` varchar(32) NULL,
`userName` varchar(40) NOT NULL,
`userPassword` varchar(40) NOT NULL,
`userEmail` varchar(256) NULL,
`lastLogin` int(11) NULL,
`ipv4` varchar(15) NULL,
`userAgent` varchar(256) NULL,
`userTimeout` int(6) NOT NULL DEFAULT '600',
`userSuspended` int(3) NOT NULL,
`userDeleted` int(3) NOT NULL,
`personId` int(6) NULL,
`zlobod` int(6) NOT NULL,
`aclRoot` int(3) NOT NULL,
`aclGamemaster` int(3) NOT NULL,
`aclDirector` int(3) NOT NULL,
`aclDeputy` int(3) NOT NULL,
`aclTask` int(3) NOT NULL,
`aclSecret` int(3) NOT NULL,
`aclAudit` int(3) NOT NULL,
`aclAPI` int(3) NOT NULL,
`aclGroup` int(3) NOT NULL,
`aclPerson` int(3) NOT NULL,
`aclCase` int(3) NOT NULL,
`aclHunt` int(3) NOT NULL,
`planMD` text NULL,
`plan` text NULL,
`filter` text NULL,
`rightSuperOld` int(3) NOT NULL,
`rightAudOld` int(3) NOT NULL,
`rightOrgOld` int(3) NOT NULL,
`rightPowerOld` int(3) NOT NULL,
`rightTextOld` int(3) NOT NULL,
PRIMARY KEY(`userId`)
) ENGINE=Aria COLLATE=utf8_general_ci;




-- Data tabulky nw_record_type
INSERT INTO `nw_record_type` VALUES('1','osoba');
INSERT INTO `nw_record_type` VALUES('2','skupina');
INSERT INTO `nw_record_type` VALUES('3','případ');
INSERT INTO `nw_record_type` VALUES('4','hlášení');
INSERT INTO `nw_record_type` VALUES('5','novinky');
INSERT INTO `nw_record_type` VALUES('6','nástěnka');
INSERT INTO `nw_record_type` VALUES('7','symbol');
INSERT INTO `nw_record_type` VALUES('8','uživatel');
INSERT INTO `nw_record_type` VALUES('9','zlobody');
INSERT INTO `nw_record_type` VALUES('10','úkoly');
INSERT INTO `nw_record_type` VALUES('11','audit');
INSERT INTO `nw_record_type` VALUES('12','jiné');
-- Data tabulky nw_operation_type
INSERT INTO `nw_operation_type` VALUES('1','čtení');
INSERT INTO `nw_operation_type` VALUES('2','úprava');
INSERT INTO `nw_operation_type` VALUES('3','nový');
INSERT INTO `nw_operation_type` VALUES('4','přiložení souboru');
INSERT INTO `nw_operation_type` VALUES('5','odstranění souboru');
INSERT INTO `nw_operation_type` VALUES('6','provazba');
INSERT INTO `nw_operation_type` VALUES('7','nová poznámka');
INSERT INTO `nw_operation_type` VALUES('8','smazání poznámky');
INSERT INTO `nw_operation_type` VALUES('9','úprava poznámy');
INSERT INTO `nw_operation_type` VALUES('10','organizační zásah');
INSERT INTO `nw_operation_type` VALUES('11','smazání záznamu');
INSERT INTO `nw_operation_type` VALUES('12','pokus o neoprávněný přístup');
INSERT INTO `nw_operation_type` VALUES('13','pokus o přístup ke smazanému záznamu');
INSERT INTO `nw_operation_type` VALUES('14','vyhledávání');
-- Data tabulky nw_backup
INSERT INTO `nw_backup` VALUES('1','1353882227','backup1353882227.sql','1.6.2 =<');
-- Data tabulky nw_user
INSERT INTO `nw_user` VALUES('1','','admin','24aa1b323cd7b2b8324a4ed27e5b01ce','','1425141161','','','600','0','0','0','0','1','1','1','1','1','1','1','1','1','1','1','1','','','','1','1','1','1','1');
