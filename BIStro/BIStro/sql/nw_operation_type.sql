-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Sob 24. lis 2012, 23:45
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
-- Struktura tabulky `nw_operation_type`
--

CREATE TABLE IF NOT EXISTS `nw_operation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `nw_operation_type`
--

INSERT INTO `nw_operation_type` (`id`, `name`) VALUES
(1, 'čtení'),
(2, 'úprava'),
(3, 'nový'),
(4, 'přiložení souboru'),
(5, 'odstranění souboru'),
(6, 'provazba'),
(7, 'nová poznámka'),
(8, 'smazání poznámky'),
(9, 'úprava poznámy'),
(10, 'organizační zásah'),
(11, 'smazání záznamu');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
