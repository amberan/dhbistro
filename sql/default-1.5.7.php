--
-- Struktura tabulky nw_ar2c
--


CREATE TABLE `nw_ar2c`(
`idreport` int(11) NOT NULL,
`idcase` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idreport`,`idcase`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_ar2p
--


CREATE TABLE `nw_ar2p`(
`idperson` int(11) NOT NULL,
`idreport` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`role` int(11) NOT NULL,
PRIMARY KEY(`idperson`,`idreport`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_audit_trail
--


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
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_backups
--


CREATE TABLE `nw_backups`(
`id` int(11) NOT NULL auto_increment,
`time` int(11) NOT NULL,
`file` varchar(255) NOT NULL,
`version` varchar(50) NOT NULL DEFAULT '1.5.2 =<',
PRIMARY KEY(`id`)
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_c2p
--


CREATE TABLE `nw_c2p`(
`idperson` int(11) NOT NULL,
`idcase` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idperson`,`idcase`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_c2s
--


CREATE TABLE `nw_c2s`(
`idsolver` int(11) NOT NULL,
`idcase` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idsolver`,`idcase`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_cases
--


CREATE TABLE `nw_cases`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_dashboard
--


CREATE TABLE `nw_dashboard`(
`id` int(11) NOT NULL auto_increment,
`created` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`content` text NOT NULL,
`content_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`content_md`)
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_data
--


CREATE TABLE `nw_data`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_doodle
--


CREATE TABLE `nw_doodle`(
`id` int(11) NOT NULL auto_increment,
`datum` int(11) NOT NULL,
`link` varchar(255) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_g2p
--


CREATE TABLE `nw_g2p`(
`idperson` int(11) NOT NULL,
`idgroup` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
PRIMARY KEY(`idperson`,`idgroup`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_groups
--


CREATE TABLE `nw_groups`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_loggedin
--


CREATE TABLE `nw_loggedin`(
`iduser` int(11) NOT NULL,
`time` int(11) NOT NULL,
`sid` varchar(255) NOT NULL,
`agent` varchar(255) NOT NULL,
`ip` varchar(100) NOT NULL,
PRIMARY KEY(`iduser`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_map
--


CREATE TABLE `nw_map`(
`id` int(11) NOT NULL auto_increment,
`datum` int(11) NOT NULL,
`link` varchar(255) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_news
--


CREATE TABLE `nw_news`(
`id` int(11) NOT NULL auto_increment,
`datum` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`kategorie` int(11) NOT NULL,
`deleted` int(11) NOT NULL,
`nadpis` varchar(255) NOT NULL,
`obsah` text NOT NULL,
`obsah_md` text NULL,
PRIMARY KEY(`id`),
FULLTEXT (`obsah_md`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_notes
--


CREATE TABLE `nw_notes`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_operation_type
--


CREATE TABLE `nw_operation_type`(
`id` int(11) NOT NULL auto_increment,
`name` varchar(70) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_persons
--


CREATE TABLE `nw_persons`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_record_type
--


CREATE TABLE `nw_record_type`(
`id` int(11) NOT NULL auto_increment,
`name` varchar(70) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_reports
--


CREATE TABLE `nw_reports`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_symbol2all
--


CREATE TABLE `nw_symbol2all`(
`idsymbol` int(11) NOT NULL,
`idrecord` int(11) NOT NULL,
`iduser` int(11) NOT NULL,
`table` int(11) NOT NULL,
PRIMARY KEY(`idsymbol`,`idrecord`,`table`)
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_symbols
--


CREATE TABLE `nw_symbols`(
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
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_tasks
--


CREATE TABLE `nw_tasks`(
`id` int(11) NOT NULL auto_increment,
`task` text NOT NULL,
`iduser` int(11) NOT NULL,
`status` int(11) NOT NULL,
`created` int(11) NOT NULL,
`created_by` int(11) NOT NULL,
`modified` int(11) NOT NULL,
`modified_by` int(11) NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=InnoDB COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_unread
--


CREATE TABLE `nw_unread`(
`id` int(11) NOT NULL auto_increment,
`idtable` int(11) NULL,
`idrecord` int(11) NULL,
`iduser` int(11) NULL,
PRIMARY KEY(`id`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;



--
-- Struktura tabulky nw_users
--


CREATE TABLE `nw_users`(
`id` int(11) NOT NULL auto_increment,
`sid` varchar(32) NOT NULL,
`login` varchar(255) NOT NULL,
`pwd` varchar(255) NOT NULL,
`idperson` int(11) NOT NULL,
`lastlogon` int(11) NOT NULL,
`right_power` int(11) NOT NULL,
`right_text` int(11) NOT NULL,
`ip` varchar(50) NOT NULL,
`user_agent` varchar(256) NOT NULL,
`deleted` int(11) NOT NULL,
`suspended` int(11) NOT NULL,
`plan` text NOT NULL,
`zlobody` int(11) NOT NULL,
`timeout` int(11) NOT NULL DEFAULT '600',
`right_org` int(11) NOT NULL,
`right_aud` int(11) NOT NULL,
`right_super` int(11) NOT NULL,
`filter` text NOT NULL,
PRIMARY KEY(`id`)
) ENGINE=MyISAM COLLATE=utf8_general_ci;

