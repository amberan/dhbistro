-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost:3306
-- Vygenerováno: Stř 08. říj 2014, 08:20
-- Verze serveru: 5.1.73
-- Verze PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `dhbistrocz`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_ar2c`
--

CREATE TABLE IF NOT EXISTS `nw_ar2c` (
  `idreport` int(11) NOT NULL DEFAULT '0',
  `idcase` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idreport`,`idcase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_ar2p`
--

CREATE TABLE IF NOT EXISTS `nw_ar2p` (
  `idperson` int(11) NOT NULL DEFAULT '0',
  `idreport` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `role` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idperson`,`idreport`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_audit_trail`
--

CREATE TABLE IF NOT EXISTS `nw_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `operation_type` int(11) NOT NULL,
  `record_type` int(11) NOT NULL,
  `idrecord` int(11) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `org` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59826 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_backups`
--

CREATE TABLE IF NOT EXISTS `nw_backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=369 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_c2p`
--

CREATE TABLE IF NOT EXISTS `nw_c2p` (
  `idperson` int(11) NOT NULL DEFAULT '0',
  `idcase` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idperson`,`idcase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_c2s`
--

CREATE TABLE IF NOT EXISTS `nw_c2s` (
  `idsolver` int(11) NOT NULL DEFAULT '0',
  `idcase` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idsolver`,`idcase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_cases`
--

CREATE TABLE IF NOT EXISTS `nw_cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `contents` text NOT NULL,
  `secret` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_dashboard`
--

CREATE TABLE IF NOT EXISTS `nw_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_data`
--

CREATE TABLE IF NOT EXISTS `nw_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniquename` varchar(255) NOT NULL DEFAULT '',
  `originalname` varchar(255) NOT NULL DEFAULT '',
  `mime` varchar(100) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0',
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `idtable` int(11) NOT NULL DEFAULT '0',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `secret` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_doodle`
--

CREATE TABLE IF NOT EXISTS `nw_doodle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_g2p`
--

CREATE TABLE IF NOT EXISTS `nw_g2p` (
  `idperson` int(11) NOT NULL DEFAULT '0',
  `idgroup` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idperson`,`idgroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_groups`
--

CREATE TABLE IF NOT EXISTS `nw_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `contents` text NOT NULL,
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `secret` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `contents` (`contents`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_loggedin`
--

CREATE TABLE IF NOT EXISTS `nw_loggedin` (
  `iduser` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `sid` varchar(255) NOT NULL DEFAULT '',
  `agent` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_map`
--

CREATE TABLE IF NOT EXISTS `nw_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_news`
--

CREATE TABLE IF NOT EXISTS `nw_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `kategorie` int(11) NOT NULL DEFAULT '0',
  `nadpis` varchar(255) NOT NULL DEFAULT '',
  `obsah` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_notes`
--

CREATE TABLE IF NOT EXISTS `nw_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `idtable` int(11) NOT NULL DEFAULT '0',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `secret` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `note` (`note`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=359 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_operation_type`
--

CREATE TABLE IF NOT EXISTS `nw_operation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_persons`
--

CREATE TABLE IF NOT EXISTS `nw_persons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL DEFAULT '',
  `surname` varchar(70) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `contents` text NOT NULL,
  `secret` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `portrait` varchar(255) NOT NULL DEFAULT '',
  `side` int(11) NOT NULL DEFAULT '0',
  `power` int(11) NOT NULL DEFAULT '0',
  `spec` int(11) NOT NULL DEFAULT '0',
  `symbol` varchar(255) NOT NULL,
  `dead` int(11) NOT NULL DEFAULT '0',
  `archiv` int(11) NOT NULL DEFAULT '0',
  `regdate` int(11) NOT NULL DEFAULT '0',
  `regid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `contents` (`contents`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=328 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_record_type`
--

CREATE TABLE IF NOT EXISTS `nw_record_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_reports`
--

CREATE TABLE IF NOT EXISTS `nw_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `datum` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `task` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `impacts` text NOT NULL,
  `details` text NOT NULL,
  `secret` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `adatum` int(11) NOT NULL DEFAULT '0',
  `start` varchar(50) NOT NULL DEFAULT '0',
  `end` varchar(50) NOT NULL DEFAULT '0',
  `energy` text NOT NULL,
  `inputs` text NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `label` (`label`),
  FULLTEXT KEY `task` (`task`),
  FULLTEXT KEY `summary` (`summary`),
  FULLTEXT KEY `impacts` (`impacts`),
  FULLTEXT KEY `details` (`details`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=234 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_symbol2all`
--

CREATE TABLE IF NOT EXISTS `nw_symbol2all` (
  `idsymbol` int(11) NOT NULL DEFAULT '0',
  `idrecord` int(11) NOT NULL DEFAULT '0',
  `iduser` int(11) NOT NULL DEFAULT '0',
  `table` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idsymbol`,`idrecord`,`table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_symbols`
--

CREATE TABLE IF NOT EXISTS `nw_symbols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `archiv` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_tasks`
--

CREATE TABLE IF NOT EXISTS `nw_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text NOT NULL,
  `iduser` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=130 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_21`
--

CREATE TABLE IF NOT EXISTS `nw_unread_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_22`
--

CREATE TABLE IF NOT EXISTS `nw_unread_22` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=137 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_23`
--

CREATE TABLE IF NOT EXISTS `nw_unread_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_25`
--

CREATE TABLE IF NOT EXISTS `nw_unread_25` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_26`
--

CREATE TABLE IF NOT EXISTS `nw_unread_26` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_27`
--

CREATE TABLE IF NOT EXISTS `nw_unread_27` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_28`
--

CREATE TABLE IF NOT EXISTS `nw_unread_28` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1290 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_29`
--

CREATE TABLE IF NOT EXISTS `nw_unread_29` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_30`
--

CREATE TABLE IF NOT EXISTS `nw_unread_30` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_31`
--

CREATE TABLE IF NOT EXISTS `nw_unread_31` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_32`
--

CREATE TABLE IF NOT EXISTS `nw_unread_32` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_34`
--

CREATE TABLE IF NOT EXISTS `nw_unread_34` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1273 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_35`
--

CREATE TABLE IF NOT EXISTS `nw_unread_35` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_36`
--

CREATE TABLE IF NOT EXISTS `nw_unread_36` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_37`
--

CREATE TABLE IF NOT EXISTS `nw_unread_37` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1203 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_38`
--

CREATE TABLE IF NOT EXISTS `nw_unread_38` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=144 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_39`
--

CREATE TABLE IF NOT EXISTS `nw_unread_39` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_40`
--

CREATE TABLE IF NOT EXISTS `nw_unread_40` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_41`
--

CREATE TABLE IF NOT EXISTS `nw_unread_41` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_42`
--

CREATE TABLE IF NOT EXISTS `nw_unread_42` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1014 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_43`
--

CREATE TABLE IF NOT EXISTS `nw_unread_43` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_44`
--

CREATE TABLE IF NOT EXISTS `nw_unread_44` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_45`
--

CREATE TABLE IF NOT EXISTS `nw_unread_45` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_46`
--

CREATE TABLE IF NOT EXISTS `nw_unread_46` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_47`
--

CREATE TABLE IF NOT EXISTS `nw_unread_47` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_48`
--

CREATE TABLE IF NOT EXISTS `nw_unread_48` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1201 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_49`
--

CREATE TABLE IF NOT EXISTS `nw_unread_49` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1087 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_50`
--

CREATE TABLE IF NOT EXISTS `nw_unread_50` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_51`
--

CREATE TABLE IF NOT EXISTS `nw_unread_51` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_52`
--

CREATE TABLE IF NOT EXISTS `nw_unread_52` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_53`
--

CREATE TABLE IF NOT EXISTS `nw_unread_53` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_54`
--

CREATE TABLE IF NOT EXISTS `nw_unread_54` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_55`
--

CREATE TABLE IF NOT EXISTS `nw_unread_55` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1084 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_56`
--

CREATE TABLE IF NOT EXISTS `nw_unread_56` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_57`
--

CREATE TABLE IF NOT EXISTS `nw_unread_57` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_58`
--

CREATE TABLE IF NOT EXISTS `nw_unread_58` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_59`
--

CREATE TABLE IF NOT EXISTS `nw_unread_59` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=463 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_60`
--

CREATE TABLE IF NOT EXISTS `nw_unread_60` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_61`
--

CREATE TABLE IF NOT EXISTS `nw_unread_61` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_62`
--

CREATE TABLE IF NOT EXISTS `nw_unread_62` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=465 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_63`
--

CREATE TABLE IF NOT EXISTS `nw_unread_63` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=913 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_64`
--

CREATE TABLE IF NOT EXISTS `nw_unread_64` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1306 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_65`
--

CREATE TABLE IF NOT EXISTS `nw_unread_65` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1150 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_66`
--

CREATE TABLE IF NOT EXISTS `nw_unread_66` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=939 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_67`
--

CREATE TABLE IF NOT EXISTS `nw_unread_67` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=909 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_68`
--

CREATE TABLE IF NOT EXISTS `nw_unread_68` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1074 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_69`
--

CREATE TABLE IF NOT EXISTS `nw_unread_69` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=528 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_70`
--

CREATE TABLE IF NOT EXISTS `nw_unread_70` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=793 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_71`
--

CREATE TABLE IF NOT EXISTS `nw_unread_71` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=939 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_72`
--

CREATE TABLE IF NOT EXISTS `nw_unread_72` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=572 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_73`
--

CREATE TABLE IF NOT EXISTS `nw_unread_73` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=486 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_74`
--

CREATE TABLE IF NOT EXISTS `nw_unread_74` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=188 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_75`
--

CREATE TABLE IF NOT EXISTS `nw_unread_75` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=245 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_76`
--

CREATE TABLE IF NOT EXISTS `nw_unread_76` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=298 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_77`
--

CREATE TABLE IF NOT EXISTS `nw_unread_77` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_78`
--

CREATE TABLE IF NOT EXISTS `nw_unread_78` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_79`
--

CREATE TABLE IF NOT EXISTS `nw_unread_79` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_80`
--

CREATE TABLE IF NOT EXISTS `nw_unread_80` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_unread_81`
--

CREATE TABLE IF NOT EXISTS `nw_unread_81` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtable` int(11) DEFAULT NULL,
  `idrecord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `nw_users`
--

CREATE TABLE IF NOT EXISTS `nw_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL DEFAULT '',
  `pwd` varchar(255) NOT NULL DEFAULT '',
  `idperson` int(11) NOT NULL DEFAULT '0',
  `lastlogon` int(11) NOT NULL DEFAULT '0',
  `right_power` int(11) NOT NULL DEFAULT '0',
  `right_text` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `plan` text NOT NULL,
  `zlobody` int(11) NOT NULL DEFAULT '0',
  `timeout` int(11) NOT NULL DEFAULT '600',
  `right_org` int(11) NOT NULL DEFAULT '0',
  `right_aud` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

