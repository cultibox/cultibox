-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2012 at 03:00 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cultibox`
--
CREATE DATABASE `cultibox` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cultibox`;

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `COLOR_HUMIDITY_GRAPH` varchar(30) NOT NULL DEFAULT 'green',
  `COLOR_TEMPERATURE_GRAPH` varchar(30) NOT NULL DEFAULT 'red',
  `RECORD_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `POWER_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `NB_PLUGS` int(11) NOT NULL DEFAULT '3',
  `UPDATE_PLUGS_FREQUENCY` int(20) NOT NULL DEFAULT '-1',
  `LANG` varchar(5) NOT NULL DEFAULT 'en_GB',
  `LOG_TEMP_AXIS` int(2) NOT NULL DEFAULT '50',
  `LOG_HYGRO_AXIS` int(2) NOT NULL DEFAULT '100',
  `SHOW_POPUP` varchar(5) NOT NULL DEFAULT 'True',
  `ALARM_ACTIV` varchar(4) NOT NULL DEFAULT '0000',
  `ALARM_VALUE` varchar(5) NOT NULL DEFAULT '50.00',
  `ALARM_SENSO` varchar(4) NOT NULL DEFAULT '000T',
  `ALARM_SENSS` varchar(4) NOT NULL,
  `FIRST_USE` varchar(5) NOT NULL DEFAULT 'True', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`id`, `COLOR_HUMIDITY_GRAPH`, `COLOR_TEMPERATURE_GRAPH`, `RECORD_FREQUENCY`, `POWER_FREQUENCY`, `NB_PLUGS`, `UPDATE_PLUGS_FREQUENCY`, `LANG`, `LOG_TEMP_AXIS`, `LOG_HYGRO_AXIS`, `SHOW_POPUP`, `ALARM_ACTIV`, `ALARM_VALUE`, `ALARM_SENSO`, `ALARM_SENSS`) VALUES (1, 'red', 'green', 5, 5, 3, -1, 'fr_FR', 50, 100, 'True', '0000', '50.0', '000T', '000+');

-- --------------------------------------------------------

--
-- Table structure for table `jqcalendar`
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `timestamp` varchar(14) NOT NULL DEFAULT '',
  `temperature` int(4) DEFAULT NULL,
  `humidity` int(4) DEFAULT NULL,
  `date_catch` varchar(10) DEFAULT NULL,
  `time_catch` varchar(10) DEFAULT NULL,
  `fake_log` varchar(5) NOT NULL DEFAULT 'False'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `plugs`
--

CREATE TABLE IF NOT EXISTS `plugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PLUG_ID` varchar(3) DEFAULT NULL,
  `PLUG_NAME` varchar(30) DEFAULT NULL,
  `PLUG_TYPE` varchar(20) NOT NULL DEFAULT 'unknown',
  `PLUG_TOLERANCE` decimal(3,1) DEFAULT NULL,
  `PLUG_POWER` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `plugs`
--

INSERT INTO `plugs` (`id`, `PLUG_ID`, `PLUG_NAME`, `PLUG_TYPE`, `PLUG_TOLERANCE`, `PLUG_POWER`) VALUES
(1, '', 'HPS/MH', 'lamp', NULL, 0),
(2, '', 'plug2', 'unknown', NULL, 0),
(3, '', 'plug3', 'unknown', NULL, 0),
(4, '', 'plug4', 'unknown', NULL, 0),
(5, '', 'plug5', 'unknown', NULL, 0),
(6, '', 'plug6', 'unknown', NULL, 0),
(7, '', 'plug7', 'unknown', NULL, 0),
(8, '', 'plug8', 'unknown', NULL, 0),
(9, '', 'plug9', 'unknown', NULL, 0),
(10, '', 'plug10', 'unknown', NULL, 0),
(11, '', 'plug11', 'unknown', NULL, 0),
(12, '', 'plug12', 'unknown', NULL, 0),
(13, '', 'plug13', 'unknown', NULL, 0),
(14, '', 'plug14', 'unknown', NULL, 0),
(15, '', 'plug15', 'unknown', NULL, 0),
(16, '', 'plug16', 'unknown', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
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
