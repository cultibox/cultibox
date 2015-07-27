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
  `VERSION` varchar(30) NOT NULL DEFAULT '1.1.28',
  `COLOR_HUMIDITY_GRAPH` varchar(30) NOT NULL DEFAULT 'blue',
  `COLOR_TEMPERATURE_GRAPH` varchar(30) NOT NULL DEFAULT 'red',
  `COLOR_WATER_GRAPH` varchar(30) NOT NULL DEFAULT 'orange',
  `COLOR_LEVEL_GRAPH` varchar(30) NOT NULL DEFAULT 'pink',
  `COLOR_PH_GRAPH` varchar(30) NOT NULL DEFAULT 'brown',
  `COLOR_EC_GRAPH` varchar(30) NOT NULL DEFAULT 'yellow',
  `COLOR_OD_GRAPH` varchar(30) NOT NULL DEFAULT 'red',
  `COLOR_ORP_GRAPH` varchar(30) NOT NULL DEFAULT 'blue',
  `COLOR_CO2_GRAPH` varchar(30) NOT NULL DEFAULT 'blue', 
  `NB_PLUGS` int(11) NOT NULL DEFAULT '3',
  `ALARM_ACTIV` varchar(4) NOT NULL DEFAULT '0000',
  `ALARM_VALUE` varchar(5) NOT NULL DEFAULT '60.00',
  `COST_PRICE` decimal(6,4) NOT NULL DEFAULT '0.1249',
  `COST_PRICE_HP` decimal(6,4) NOT NULL DEFAULT '0.1353',
  `COST_PRICE_HC` decimal(6,4) NOT NULL DEFAULT '0.0926',
  `START_TIME_HC` varchar(5) NOT NULL DEFAULT '22:30',
  `STOP_TIME_HC` varchar(5) NOT NULL DEFAULT '06:30',
  `COST_TYPE` varchar(20) NOT NULL DEFAULT 'standard',
  `STATISTICS` varchar(5) NOT NULL DEFAULT 'True',
  `ADVANCED_REGUL_OPTIONS` VARCHAR(5) NOT NULL DEFAULT 'False',
  `SHOW_COST` BOOLEAN NOT NULL DEFAULT 0,
  `RESET_MINMAX` VARCHAR(5) NOT NULL DEFAULT '00:00',
  `RTC_OFFSET` int(11) NOT NULL DEFAULT '0',
  `REMOVE_1000_CHANGE_LIMIT` VARCHAR(5) NOT NULL DEFAULT 'False',
  `REMOVE_5_MINUTE_LIMIT` VARCHAR(5) NOT NULL DEFAULT 'False',
  `DEFAULT_LANG` VARCHAR(5) NOT NULL DEFAULT 'en_GB',
  `ENABLE_LED` VARCHAR(4) NOT NULL DEFAULT '0001',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `configuration`
--


INSERT INTO `configuration` (`id`, `VERSION`, `COLOR_HUMIDITY_GRAPH`, `COLOR_TEMPERATURE_GRAPH`, `COLOR_WATER_GRAPH`, `COLOR_LEVEL_GRAPH`, `COLOR_PH_GRAPH`, `COLOR_EC_GRAPH`, `COLOR_OD_GRAPH`, `COLOR_ORP_GRAPH`, `NB_PLUGS`, `ALARM_ACTIV`, `ALARM_VALUE`, `COST_PRICE`, `COST_PRICE_HP`, `COST_PRICE_HC`, `START_TIME_HC`, `STOP_TIME_HC`, `COST_TYPE`, `STATISTICS`,`ADVANCED_REGUL_OPTIONS`,`SHOW_COST`,,`RESET_MINMAX`, `RTC_OFFSET`) VALUES
(1, '1.1.28', 'blue', 'red', 'orange', 'pink', 'brown', 'yellow', 'red', 'blue', 3, '0000', '60', 0.1225, 0.1353, 0.0926, '22:30', '06:30', 'standard', 'True', 'False', 0, '00:00',0);

-- --------------------------------------------------------

--
-- Table structure for table `informations`
--

CREATE TABLE IF NOT EXISTS `informations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `cbx_id` int(5) NOT NULL DEFAULT '0',
  `firm_version` varchar(7) NOT NULL DEFAULT '000.000',
  `log` mediumtext,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `informations` (`ID` ,`cbx_id` ,`firm_version`,`log`) VALUES (NULL , '0', '', '');


-- --------------------------------------------------------


--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(1000) DEFAULT NULL,
  `Description` varchar(500) DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `External` SMALLINT(6) NOT NULL DEFAULT '0',
  `Color` varchar(7) NOT NULL DEFAULT '#4A40A4',
  `Icon` VARCHAR(30) NULL,
  `Important` INT(1) NOT NULL DEFAULT '0',
  `program_index` VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `timestamp` varchar(14) NOT NULL DEFAULT '',
  `record1` int(4) DEFAULT NULL,
  `record2` int(4) DEFAULT NULL,
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
  `PLUG_POWER` int(11) NULL DEFAULT NULL,
  `PLUG_POWER_MAX` varchar(10) NOT NULL DEFAULT '1000',
  `PLUG_REGUL` varchar(5) NOT NULL DEFAULT 'False',
  `PLUG_REGUL_SENSOR` VARCHAR( 7 ) NOT NULL DEFAULT '1',
  `PLUG_SENSO` varchar(1) NOT NULL DEFAULT 'T',
  `PLUG_SENSS` varchar(1) NOT NULL DEFAULT '+',
  `PLUG_REGUL_VALUE` decimal(3,1) NOT NULL DEFAULT '35.0',
  `PLUG_SECOND_TOLERANCE` DECIMAL( 3, 1 ) NOT NULL DEFAULT '0.0',
  `PLUG_COMPUTE_METHOD` VARCHAR( 1 ) NOT NULL DEFAULT 'M',
  `PLUG_MODULE` VARCHAR(10) NOT NULL DEFAULT 'wireless',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `plugs`
--

INSERT INTO `plugs` (`id`, `PLUG_ID`, `PLUG_NAME`, `PLUG_TYPE`, `PLUG_TOLERANCE`, `PLUG_POWER`, `PLUG_POWER_MAX`, `PLUG_REGUL`, `PLUG_REGUL_SENSOR`, `PLUG_SENSO`, `PLUG_SENSS`, `PLUG_REGUL_VALUE`, `PLUG_SECOND_TOLERANCE`,`PLUG_COMPUTE_METHOD`,`PLUG_MODULE`) VALUES
(1, '', 'Plug1', 'other', 1.0, NULL, '3500', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(2, '', 'Plug2', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(3, '', 'Plug3', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(4, '', 'Plug4', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(5, '', 'Plug5', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(6, '', 'Plug6', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(7, '', 'Plug7', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(8, '', 'Plug8', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(9, '', 'Plug9', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(10, '', 'Plug10', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(11, '', 'Plug11', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(12, '', 'Plug12', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(13, '', 'Plug13', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(14, '', 'Plug14', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(15, '', 'Plug15', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(16, '', 'Plug16', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(17,'', 'Plug17', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(18,'', 'Plug18', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(19,'', 'Plug19', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(20,'', 'Plug20', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(21,'', 'Plug21', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(22,'', 'Plug22', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(23,'', 'Plug23', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(24,'', 'Plug24', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(25,'', 'Plug25', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(26,'', 'Plug26', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(27,'', 'Plug27', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(28,'', 'Plug28', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(29,'', 'Plug29', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(30,'', 'Plug30', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(31,'', 'Plug31', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(32,'', 'Plug32', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(33,'', 'Plug33', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(34,'', 'Plug34', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless'),
(35,'', 'Plug35', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M','wireless');

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
  `value` decimal(5,1) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `date_start` varchar(10) NOT NULL DEFAULT '0000-00-00',
  `date_end` varchar(10) NOT NULL DEFAULT '0000-00-00',
  `type` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE IF NOT EXISTS `sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(2) NOT NULL DEFAULT '0',
  `detectionAuto` varchar(5) NOT NULL DEFAULT 'true',  
  `name` varchar(20) NOT NULL DEFAULT 'capteur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`id`, `type`) VALUES (1, '2'), (2, '2'), (3, '2'), (4, '2'), (5, '0'),(6, '0');


-- --------------------------------------------------------
--
-- Table structure for table `program_index`
--
CREATE TABLE IF NOT EXISTS `program_index` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100),
    `version` VARCHAR(100),
    `program_idx` INT,
    `creation` DATETIME,
    `modification` DATETIME,
    `plugv_filename` VARCHAR(10),
    `comments` VARCHAR(500)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `program_index`
--

INSERT INTO `program_index` (`name`,`version`,`program_idx`,`creation`, `modification`, `plugv_filename`,`comments`) VALUES('Current','1.0','1' , NOW(), NOW(), '00' , "Current pogramm");

-- --------------------------------------------------------
--
-- Table structure for table `synoptic`
--
CREATE TABLE IF NOT EXISTS `synoptic` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `element` varchar(10) NOT NULL DEFAULT 'other',
    `scale` int(11) NOT NULL DEFAULT '100',
    `x` int(11) NOT NULL DEFAULT '0',
    `y` int(11) NOT NULL DEFAULT '0',
    `z` int(11) NOT NULL DEFAULT '100',
    `indexElem` int(11) NOT NULL DEFAULT '0',
    `rotation` int(11) NOT NULL DEFAULT '0',
    `image` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `synoptic` (`element`,`indexElem`,`image`,`x`, `y`, `z`,`scale`) VALUES('other','1','cultipi.png' , '850', '450', '2' , '74');
INSERT INTO `synoptic` (`element`,`indexElem`,`image`,`x`, `y`, `z`,`scale`) VALUES('other','2','tente_1_espace.png' , '600', '350', '1' , '250');



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

