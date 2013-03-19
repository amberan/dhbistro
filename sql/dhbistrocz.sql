-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Ned 19. úno 2012, 16:44
-- Verze MySQL: 5.5.20
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
  PRIMARY KEY (`idperson`,`idreport`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
