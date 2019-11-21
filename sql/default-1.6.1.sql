-- Adminer 4.7.5 MySQL dump

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_ar2p`;
CREATE TABLE `nw_ar2p` (
  `idperson` int(11) NOT NULL,
  `idreport` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idreport`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `version` varchar(50) NOT NULL DEFAULT '1.5.8 =<',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_c2p`;
CREATE TABLE `nw_c2p` (
  `idperson` int(11) NOT NULL,
  `idcase` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idcase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_c2s`;
CREATE TABLE `nw_c2s` (
  `idsolver` int(11) NOT NULL,
  `idcase` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idsolver`,`idcase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `contents_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `contents_md` (`contents_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_dashboard`;
CREATE TABLE `nw_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `content` text NOT NULL,
  `content_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `content_md` (`content_md`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_doodle`;
CREATE TABLE `nw_doodle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_g2p`;
CREATE TABLE `nw_g2p` (
  `idperson` int(11) NOT NULL,
  `idgroup` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idgroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `contents_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `contents_md` (`contents_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_loggedin_deleted`;
CREATE TABLE `nw_loggedin_deleted` (
  `iduser` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `sid` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `ip` varchar(100) NOT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_map_deleted`;
CREATE TABLE `nw_map_deleted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_news`;
CREATE TABLE `nw_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `kategorie` int(11) NOT NULL,
  `deleted` int(3) NOT NULL,
  `nadpis` varchar(255) NOT NULL,
  `obsah` text NOT NULL,
  `obsah_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `obsah_md` (`obsah_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `note_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `note` (`note`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `note_md` (`note_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_operation_type`;
CREATE TABLE `nw_operation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_person`;
CREATE TABLE `nw_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `contents_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `contents` (`contents`),
  FULLTEXT KEY `contents_md` (`contents_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_record_type`;
CREATE TABLE `nw_record_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


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
  `summary_md` text DEFAULT NULL,
  `impacts_md` text DEFAULT NULL,
  `details_md` text DEFAULT NULL,
  `energy_md` text DEFAULT NULL,
  `inputs_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `label` (`label`),
  FULLTEXT KEY `task` (`task`),
  FULLTEXT KEY `summary` (`summary`),
  FULLTEXT KEY `impacts` (`impacts`),
  FULLTEXT KEY `details` (`details`),
  FULLTEXT KEY `summary_md` (`summary_md`),
  FULLTEXT KEY `impacts_md` (`impacts_md`),
  FULLTEXT KEY `details_md` (`details_md`),
  FULLTEXT KEY `energy_md` (`energy_md`),
  FULLTEXT KEY `inputs_md` (`inputs_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_symbol`;
CREATE TABLE `nw_symbol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `desc_md` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `desc` (`desc`),
  FULLTEXT KEY `desc_md` (`desc_md`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
  `modified` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `task_md` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_unread`;
CREATE TABLE `nw_unread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nw_user`;
CREATE TABLE `nw_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) DEFAULT NULL,
  `login` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `idperson` int(11) DEFAULT NULL,
  `lastlogon` int(11) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `user_agent` varchar(256) DEFAULT NULL,
  `deleted` int(3) NOT NULL,
  `suspended` int(11) NOT NULL,
  `zlobody` int(11) NOT NULL,
  `timeout` int(11) NOT NULL DEFAULT 600,
  `right_text` int(11) NOT NULL,
  `right_power` int(11) NOT NULL,
  `right_org` int(11) NOT NULL,
  `right_aud` int(11) NOT NULL,
  `right_super` int(11) NOT NULL,
  `plan` text DEFAULT NULL,
  `filter` text DEFAULT NULL,
  `plan_md` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `nw_user` (`id`, `sid`, `login`, `pwd`, `idperson`, `lastlogon`, `ip`, `user_agent`, `deleted`, `suspended`, `zlobody`, `timeout`, `right_text`, `right_power`, `right_org`, `right_aud`, `right_super`, `plan`, `filter`, `plan_md`) VALUES
(1,	'',	'admin',	'955db0b81ef1989b4a4dfeae8061a9a6',	0,	1539028214,	'1',	'1',	0,	0,	1,	600,	1,	1,	1,	1,	1,	'',	'',	'');

-- 2019-11-21 17:12:49
