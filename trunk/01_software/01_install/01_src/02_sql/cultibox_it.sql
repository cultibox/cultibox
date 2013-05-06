-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2012 at 04:07 PM
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
  `CHECK_UPDATE` varchar(5) NOT NULL DEFAULT 'True',
  `VERSION` varchar(30) NOT NULL DEFAULT '1.1.9',
  `COLOR_HUMIDITY_GRAPH` varchar(30) NOT NULL DEFAULT 'blue',
  `COLOR_TEMPERATURE_GRAPH` varchar(30) NOT NULL DEFAULT 'red',
  `COLOR_POWER_GRAPH` varchar(30) NOT NULL DEFAULT 'black',
  `COLOR_COST_GRAPH` varchar(30) NOT NULL DEFAULT 'purple',
  `RECORD_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `POWER_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `NB_PLUGS` int(11) NOT NULL DEFAULT '3',
  `UPDATE_PLUGS_FREQUENCY` int(20) NOT NULL DEFAULT '-1',
  `SHOW_POPUP` varchar(5) NOT NULL DEFAULT 'True',
  `ALARM_ACTIV` varchar(4) NOT NULL DEFAULT '0000',
  `ALARM_VALUE` varchar(5) NOT NULL DEFAULT '50.00',
  `ALARM_SENSO` varchar(4) NOT NULL DEFAULT '000T',
  `ALARM_SENSS` varchar(4) NOT NULL DEFAULT '000+',
  `COST_PRICE` decimal(6,4) NOT NULL DEFAULT '0.1249',
  `COST_PRICE_HP` decimal(6,4) NOT NULL DEFAULT '0.1353',
  `COST_PRICE_HC` decimal(6,4) NOT NULL DEFAULT '0.0926',
  `START_TIME_HC` varchar(5) NOT NULL DEFAULT '22:30',
  `STOP_TIME_HC` varchar(5) NOT NULL DEFAULT '06:30',
  `COST_TYPE` varchar(20) NOT NULL DEFAULT 'standard',
  `LOG_SEARCH` int(11) NOT NULL DEFAULT '2',
  `STATISTICS` varchar(5) NOT NULL DEFAULT 'True',
  `SECOND_REGUL` VARCHAR( 5 ) NOT NULL DEFAULT 'False',
  `REGUL_SENSOR` VARCHAR( 5 ) NOT NULL DEFAULT 'False',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `configuration`
--


INSERT INTO `configuration` (`id`, `CHECK_UPDATE`, `VERSION`, `COLOR_HUMIDITY_GRAPH`, `COLOR_TEMPERATURE_GRAPH`, `COLOR_POWER_GRAPH`, `COLOR_COST_GRAPH`, `RECORD_FREQUENCY`, `POWER_FREQUENCY`, `NB_PLUGS`, `UPDATE_PLUGS_FREQUENCY`, `SHOW_POPUP`, `ALARM_ACTIV`, `ALARM_VALUE`, `ALARM_SENSO`, `ALARM_SENSS`, `COST_PRICE`, `COST_PRICE_HP`, `COST_PRICE_HC`, `START_TIME_HC`, `STOP_TIME_HC`, `COST_TYPE`, `LOG_SEARCH`, `STATISTICS`,`SECOND_REGUL`,`REGUL_SENSOR`) VALUES
(1, 'True', '1.1.9', 'blue', 'red', 'black', 'purple', 5, 1, 3, -1, 'True', '0000', '15', '000H', '000+', 0.1225, 0.1353, 0.0926, '22:30', '06:30', 'standard', 2, 'True', 'False', 'False');

-- --------------------------------------------------------

--
-- Table structure for table `historic`
--

CREATE TABLE IF NOT EXISTS `historic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` varchar(25) NOT NULL,
  `action` varchar(300) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `informations`
--

CREATE TABLE IF NOT EXISTS `informations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `cbx_id` int(5) NOT NULL DEFAULT '0',
  `firm_version` varchar(7) NOT NULL DEFAULT '000.000',
  `emeteur_version` varchar(7) NOT NULL DEFAULT '000.000',
  `sensor_version` varchar(7) NOT NULL DEFAULT '000.000',
  `last_reboot` varchar(14) NOT NULL DEFAULT '00000000000000',
  `nb_reboot` int(11) NOT NULL DEFAULT '0',
  `id_computer` varchar(50) NOT NULL DEFAULT 'NULL',
  `log` mediumtext,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `cultibox`.`informations` (`ID` ,`cbx_id` ,`firm_version` ,`emeteur_version` ,`sensor_version` ,`last_reboot` ,`nb_reboot` ,`id_computer`,`log`) VALUES (NULL , '0', '0', '000.000', '000.000', '00000000000000', '0', 'NULL','');


-- --------------------------------------------------------


--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `Description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `External` SMALLINT( 6 ) NOT NULL DEFAULT '0',
  `Color` varchar(7) NOT NULL DEFAULT '#4A40A4',
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
  `fake_log` varchar(5) NOT NULL DEFAULT 'False',
  `sensor_nb` int(4) NOT NULL DEFAULT '1',
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `plugs`
--

CREATE TABLE IF NOT EXISTS `plugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PLUG_ID` varchar(3) DEFAULT NULL,
  `PLUG_NAME` varchar(30) DEFAULT NULL,
  `PLUG_TYPE` varchar(20) NOT NULL DEFAULT 'other',
  `PLUG_TOLERANCE` decimal(3,1) DEFAULT NULL,
  `PLUG_POWER` int(11) NOT NULL DEFAULT '0',
  `PLUG_POWER_MAX` varchar(10) NOT NULL DEFAULT '1000',
  `PLUG_REGUL` varchar(5) NOT NULL DEFAULT 'False',
  `PLUG_REGUL_SENSOR` INT NOT NULL DEFAULT '1',
  `PLUG_SENSO` varchar(1) NOT NULL DEFAULT 'T',
  `PLUG_SENSS` varchar(1) NOT NULL DEFAULT '+',
  `PLUG_REGUL_VALUE` decimal(3,1) NOT NULL DEFAULT '35.0',
  `PLUG_ENABLED` varchar(5) NOT NULL DEFAULT 'True',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `plugs`
--

INSERT INTO `plugs` (`id`, `PLUG_ID`, `PLUG_NAME`, `PLUG_TYPE`, `PLUG_TOLERANCE`, `PLUG_POWER`, `PLUG_POWER_MAX`, `PLUG_REGUL`, `PLUG_REGUL_SENSOR`, `PLUG_SENSO`, `PLUG_SENSS`, `PLUG_REGUL_VALUE`, `PLUG_ENABLED`) VALUES
(1, '', 'Spina1', 'other', 1.0, NULL, '3500', 'False', '1', 'T', '+', 35.0,'True'),
(2, '', 'Spina2', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(3, '', 'Spina3', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(4, '', 'Spina4', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(5, '', 'Spina5', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(6, '', 'Spina6', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(7, '', 'Spina7', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(8, '', 'Spina8', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(9, '', 'Spina9', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(10, '', 'Spina10', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(11, '', 'Spina11', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(12, '', 'Spina12', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(13, '', 'Spina13', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(14, '', 'Spina14', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(15, '', 'Spina15', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True'),
(16, '', 'Spina16', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,'True');

-- --------------------------------------------------------

--
-- Table structure for table `power`
--

CREATE TABLE IF NOT EXISTS `power` (
  `timestamp` varchar(14) NOT NULL DEFAULT '',
  `record` int(3) DEFAULT NULL,
  `plug_number` int(3) DEFAULT NULL,
  `date_catch` varchar(10) DEFAULT NULL,
  `time_catch` varchar(10) DEFAULT NULL,
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE IF NOT EXISTS `programs` (
  `plug_id` int(11) NOT NULL,
  `time_start` varchar(6) NOT NULL,
  `time_stop` varchar(6) NOT NULL,
  `value` decimal(3,1) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `date_start` varchar(10) NOT NULL DEFAULT '0000-00-00',
  `date_end` varchar(10) NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

