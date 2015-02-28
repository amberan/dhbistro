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
-- Struktura tabulky `nw_record_type`
--

CREATE TABLE IF NOT EXISTS `nw_record_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Vypisuji data pro tabulku `nw_record_type`
--

INSERT INTO `nw_record_type` (`id`, `name`) VALUES
(1, 'osoba'),
(2, 'skupina'),
(3, 'případ'),
(4, 'hlášení'),
(5, 'novinky'),
(6, 'nástěnka'),
(7, 'symbol'),
(8, 'uživatel'),
(9, 'zlobody'),
(10, 'úkoly'),
(11, 'audit'),
(12, 'jiné');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
