-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Jeu 17 Mai 2012 à 23:30
-- Version du serveur: 5.5.16
-- Version de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `cultibox`
--
CREATE DATABASE `cultibox` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cultibox`;

-- --------------------------------------------------------

--
-- Structure de la table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `COLOR_HUMIDITY_GRAPH` varchar(30) NOT NULL DEFAULT 'green',
  `COLOR_TEMPERATURE_GRAPH` varchar(30) NOT NULL DEFAULT 'red',
  `RECORD_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `NB_PLUGS` int(11) NOT NULL DEFAULT '4',
  `UPDATE_PLUGS_FREQUENCY` int(20) NOT NULL DEFAULT '-1',
  `LANG` varchar(5) NOT NULL DEFAULT 'en_GB',
  `SHOW_WIZARD` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `configuration`
--

INSERT INTO `configuration` (`id`, `COLOR_HUMIDITY_GRAPH`, `COLOR_TEMPERATURE_GRAPH`, `RECORD_FREQUENCY`, `NB_PLUGS`, `UPDATE_PLUGS_FREQUENCY`, `LANG`, `SHOW_WIZARD`) VALUES
(1, 'red', 'black', 1, 4, -1, 'fr_FR', 1);

-- --------------------------------------------------------

--
-- Structure de la table `jqcalendar`
--

CREATE TABLE IF NOT EXISTS `jqcalendar` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Subject` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `Location` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `Description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `IsAllDayEvent` smallint(6) NOT NULL,
  `Color` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `RecurringRule` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `timestamp` varchar(14) NOT NULL DEFAULT '',
  `temperature` int(11) DEFAULT NULL,
  `humidity` int(11) DEFAULT NULL,
  `date_catch` varchar(10) DEFAULT NULL,
  `time_catch` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `plugs`
--

CREATE TABLE IF NOT EXISTS `plugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PLUG_NAME` varchar(30) DEFAULT NULL,
  `PLUG_TYPE` varchar(20) DEFAULT 'unknown',
  `PLUG_TOLERANCE` decimal(3,1) DEFAULT '0.0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `plugs`
--

INSERT INTO `plugs` (`id`, `PLUG_NAME`, `PLUG_TYPE`, `PLUG_TOLERANCE`) VALUES
(1, 'plug1', NULL, NULL),
(2, 'plug2', NULL, NULL),
(3, 'plug3', NULL, NULL),
(4, 'plug4', NULL, NULL),
(5, 'plug5', NULL, NULL),
(6, 'plug6', NULL, NULL),
(7, 'plug7', NULL, NULL),
(8, 'plug8', NULL, NULL),
(9, 'plug9', NULL, NULL),
(10, 'plug10', NULL, NULL),
(11, 'plug11', NULL, NULL),
(12, 'plug12', NULL, NULL),
(13, 'plug13', NULL, NULL),
(14, 'plug14', NULL, NULL),
(15, 'plug15', NULL, NULL),
(16, 'plug16', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `programs`
--

CREATE TABLE IF NOT EXISTS `programs` (
  `plug_id` int(11) NOT NULL,
  `time_start` varchar(6) NOT NULL,
  `time_stop` varchar(6) NOT NULL,
  `value` decimal(3,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
