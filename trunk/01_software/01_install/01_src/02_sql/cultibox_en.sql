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
  `COLOR_POWER_GRAPH` varchar(30) NOT NULL DEFAULT 'black',
  `COLOR_COST_GRAPH` varchar(30) NOT NULL DEFAULT 'purple',
  `RECORD_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `POWER_FREQUENCY` int(11) NOT NULL DEFAULT '5',
  `NB_PLUGS` int(11) NOT NULL DEFAULT '3',
  `UPDATE_PLUGS_FREQUENCY` int(20) NOT NULL DEFAULT '-1',
  `ALARM_ACTIV` varchar(4) NOT NULL DEFAULT '0000',
  `ALARM_VALUE` varchar(5) NOT NULL DEFAULT '50.00',
  `COST_PRICE` decimal(6,4) NOT NULL DEFAULT '0.1249',
  `COST_PRICE_HP` decimal(6,4) NOT NULL DEFAULT '0.1353',
  `COST_PRICE_HC` decimal(6,4) NOT NULL DEFAULT '0.0926',
  `START_TIME_HC` varchar(5) NOT NULL DEFAULT '22:30',
  `STOP_TIME_HC` varchar(5) NOT NULL DEFAULT '06:30',
  `COST_TYPE` varchar(20) NOT NULL DEFAULT 'standard',
  `STATISTICS` varchar(5) NOT NULL DEFAULT 'True',
  `SECOND_REGUL` VARCHAR( 5 ) NOT NULL DEFAULT 'False',
  `ADVANCED_REGUL_OPTIONS` VARCHAR( 5 ) NOT NULL DEFAULT 'False',
  `SHOW_COST` BOOLEAN NOT NULL DEFAULT 0,
  `RESET_MINMAX` VARCHAR( 5 ) NOT NULL DEFAULT '00:00',
  `WIFI` BOOLEAN NOT NULL DEFAULT 0,
  `WIFI_SSID` VARCHAR(32),
  `WIFI_KEY_TYPE` VARCHAR(10) NOT NULL DEFAULT 'NONE',
  `WIFI_PASSWORD` VARCHAR(63),
  `WIFI_IP` VARCHAR(15) NOT NULL DEFAULT '000.000.000.000',
  `WIFI_IP_MANUAL` BOOLEAN NOT NULL DEFAULT false,
  `RTC_OFFSET` DECIMAL(3,2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `configuration`
--


INSERT INTO `configuration` (`id`, `VERSION`, `COLOR_HUMIDITY_GRAPH`, `COLOR_TEMPERATURE_GRAPH`, `COLOR_WATER_GRAPH`, `COLOR_LEVEL_GRAPH`, `COLOR_PH_GRAPH`, `COLOR_EC_GRAPH`, `COLOR_OD_GRAPH`, `COLOR_ORP_GRAPH`, `COLOR_POWER_GRAPH`, `COLOR_COST_GRAPH`, `RECORD_FREQUENCY`, `POWER_FREQUENCY`, `NB_PLUGS`, `UPDATE_PLUGS_FREQUENCY`, `ALARM_ACTIV`, `ALARM_VALUE`, `COST_PRICE`, `COST_PRICE_HP`, `COST_PRICE_HC`, `START_TIME_HC`, `STOP_TIME_HC`, `COST_TYPE`, `STATISTICS`,`SECOND_REGUL`,`ADVANCED_REGUL_OPTIONS`,`SHOW_COST`,`RESET_MINMAX`, `WIFI`, `WIFI_SSID`, `WIFI_KEY_TYPE`, `WIFI_PASSWORD`, `WIFI_IP`, `WIFI_IP_MANUAL`, `RTC_OFFSET`) VALUES
(1, '1.1.28', 'blue', 'red', 'orange', 'pink', 'brown', 'yellow', 'red', 'blue', 'black', 'purple', 5, 1, 3, -1, '0000', '15', 0.1225, 0.1353, 0.0926, '22:30', '06:30', 'standard', 'True', 'False', 'False', 0, '00:00',0,'','NONE','','000.000.000.000',0,0);

-- --------------------------------------------------------

--
-- Table structure for table `informations`
--

CREATE TABLE IF NOT EXISTS `informations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `cbx_id` int(5) NOT NULL DEFAULT '0',
  `firm_version` varchar(7) NOT NULL DEFAULT '000.000',
  `id_computer` varchar(50) NOT NULL DEFAULT 'NULL',
  `log` mediumtext,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `cultibox`.`informations` (`ID` ,`cbx_id` ,`firm_version`,`id_computer`,`log`) VALUES (NULL , '0', '0', 'NULL','');


-- --------------------------------------------------------


--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `Description` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `External` SMALLINT( 6 ) NOT NULL DEFAULT '0',
  `Color` varchar(7) NOT NULL DEFAULT '#4A40A4',
  `Icon` VARCHAR( 30 ) NULL,
  `Important` INT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `plugs`
--

INSERT INTO `plugs` (`id`, `PLUG_ID`, `PLUG_NAME`, `PLUG_TYPE`, `PLUG_TOLERANCE`, `PLUG_POWER`, `PLUG_POWER_MAX`, `PLUG_REGUL`, `PLUG_REGUL_SENSOR`, `PLUG_SENSO`, `PLUG_SENSS`, `PLUG_REGUL_VALUE`, `PLUG_SECOND_TOLERANCE`,`PLUG_COMPUTE_METHOD`) VALUES
(1, '', 'Plug1', 'other', 1.0, NULL, '3500', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(2, '', 'Plug2', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(3, '', 'Plug3', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(4, '', 'Plug4', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(5, '', 'Plug5', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(6, '', 'Plug6', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(7, '', 'Plug7', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(8, '', 'Plug8', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(9, '', 'Plug9', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(10, '', 'Plug10', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(11, '', 'Plug11', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(12, '', 'Plug12', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(13, '', 'Plug13', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(14, '', 'Plug14', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(15, '', 'Plug15', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
(16, '', 'Plug16', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M');

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
  `date_end` varchar(10) NOT NULL DEFAULT '0000-00-00',
  `type` INT NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `desc` varchar(500) NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `link` varchar(50) DEFAULT NULL,
  `type_link` varchar(30) DEFAULT NULL,
  `lang` varchar(5) NOT NULL DEFAULT 'fr_FR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `desc`, `image`, `link`, `type_link`, `lang`) VALUES
(1, 'Documentation', 'Retrouver une documentation plus complète dans le logiciel en cliquant sur l’onglet <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Aide</a>. La version la plus à jour de l’aide est disponible à l’adresse suivante:', NULL, 'https://code.google.com/p/cultibox', 'external', 'fr_FR'),
(2, 'Des questions ?', 'Si l’aide ne suffit pas pour répondre à une de vos questions, envoyez-nous un mail à l’adresse suivante :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'),
(3, 'Accessoires', 'Vous pouvez compléter votre pack en achetant des capteurs supplémentaires, des prises 1000W et 3500W ou encore d’autres accessoires en vous rendant sur le site :', NULL, 'http://www.cultibox.fr', 'external', 'fr_FR'),
(4, 'Recyclage', 'Chez Cultibox nous retraitons tous les éléments de nos produits. L’emballage peut être recyclé et la Cultibox contient une pile lithium qui ne doit pas être jetée à la poubelle. Pour un recyclage optimal, renvoyez nous la Cultibox et vous serez récompensé.', 'recycling.png', NULL, NULL, 'fr_FR'),
(5, 'Garantie', 'La Cultibox ainsi que ses accessoires sont garantis deux ans. Nous assurons la garantie en direct, sans intermédiaire. Pour nous contacter :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'),
(6, 'Documentation', 'Find a more complete documentation in the software by clicking on the <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Help tab</a>. The most current version is available using the following address:', NULL, 'https://code.google.com/p/cultibox', 'external', 'en_GB'),
(7, 'Some questions?', 'If help is not enough to answer one of your question, send us an email at the following address:', NULL, 'support@cultibox.fr', 'mail', 'en_GB'),
(8, 'Accessories', 'You can complete your package by purchasing additional sensors, 1000W and 3500W plugs or other accessories by visiting the website:', NULL, 'http://www.cultibox.fr', 'external', 'en_GB'),
(9, 'Recycling', 'At Cultibox we reprocess all part of our products. The packaging can be recycled and Cultibox contains a lithium battery that should not be thrown away. For optimal recycling, return us the Cultibox and you will be rewarded.', 'recycling.png', NULL, NULL, 'en_GB'),
(10, 'Warrantly', 'The Cultibox and accessories are warranted for two years. We ensure the security directly, without intermediary. To contact us:', NULL, 'support@cultibox.fr', 'mail', 'en_GB'),
(11, 'Documentación', 'Encuentra una documentación más completa en el software haciendo clic en la pestaña  <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Ayuda</a>. La versión más actualizada se encuentra disponible la siguiente dirección:', NULL, 'https://code.google.com/p/cultibox', 'external', 'es_ES'),
(12, '¿Preguntas?', 'Si la ayuda no es suficiente para cumplir con una de sus preguntas, envíe un correo electrónico a la siguiente dirección:', NULL, 'support@cultibox.fr', 'mail', 'es_ES'),
(13, 'Accesorios', 'Puede completar su paquete mediante la compra de sensores adicionales, 1000W y 3500W accesorios tomadas u otro, visitando el sitio web:', NULL, 'http://www.cultibox.fr', 'external', 'es_ES'),
(14, 'Reciclaje', 'En Cultibox que reprocesar parte de nuestros productos. El embalaje es reciclable y Cultibox contiene una batería de litio que no debe ser desechada. Para un reciclaje óptimo, nos devuelven Cultibox y serás recompensado.', 'recycling.png', NULL, NULL, 'es_ES'),
(15, 'Garantía', 'El Cultibox y accesorios tienen una garantía de dos años. Garantizamos la seguridad directamente, sin intermediario. Para contactar con nosotros:', NULL, 'support@cultibox.fr', 'mail', 'es_ES'),
(16, 'Documentazione', 'Trova una documentazione più completa del software facendo clic sulla scheda <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Guida</a>. La versione più aggiornata è disponibile al seguente indirizzo:', NULL, 'https://code.google.com/p/cultibox', 'external', 'it_IT'),
(17, 'Domande?', 'Se l''aiuto non è sufficiente a soddisfare una delle vostre domande, inviateci una e-mail al seguente indirizzo:', NULL, 'support@cultibox.fr', 'mail', 'it_IT'),
(18, 'Accessori', 'È possibile completare il pacchetto con l''acquisto di sensori supplementari, 1000W e 3500W accessori adottate o altro visitando il sito:', NULL, 'http://www.cultibox.fr', 'external', 'it_IT'),
(19, 'Riciclaggio', 'In Cultibox abbiamo rielaborare tutti parte dei nostri prodotti. L''imballaggio può essere riciclato e Cultibox contiene una batteria al litio che non deve essere gettato via. Per il riciclaggio ottimale, noi tornare Cultibox e sarete ricompensati.', 'recycling.png', NULL, NULL, 'it_IT'),
(20, 'Garanzia', 'Il Cultibox e gli accessori sono garantiti per due anni. Noi garantiamo la sicurezza direttamente, senza intermediari. Per contattarci:', NULL, 'support@cultibox.fr', 'mail', 'it_IT'),
(21, 'Dokumentation', 'Finden Sie eine umfassendere Dokumentation in der Software, indem Sie auf der <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank"> Registerkarte Hilfe</a>. Die aktuelle Version ist verfügbar unter folgender Adresse:', NULL, 'https://code.google.com/p/cultibox', 'external', 'de_DE'),
(22, 'Haben Sie Fragen?', 'Ist die Beihilfe ist nicht genug, um eine Ihrer Fragen gerecht zu werden, senden Sie uns eine E-Mail an die folgende Adresse:', NULL, 'support@cultibox.fr', 'mail', 'de_DE'),
(23, 'Zubehör', 'Sie können Ihr Paket durch den Kauf von zusätzlichen Sensoren, 1000W und 3500W genommen oder anderem Zubehör durch den Besuch der Website zu vervollständigen:', NULL, 'http://www.cultibox.fr', 'external', 'de_DE'),
(24, 'Recycling', 'In Cultibox wir nachbearbeiten alle Teil unserer Produkte. Die Verpackung kann recycelt werden und Cultibox enthält eine Lithium-Batterie, die nicht geworfen sollten entfernt werden. Für eine optimale Wiederverwertung, bringen uns Cultibox und Sie werden belohnt werden.', 'recycling.png', NULL, NULL, 'de_DE'),
(25, 'Garantie', 'Die Cultibox und Zubehör sind für zwei Jahre garantiert. Wir sorgen für die Sicherheit direkt, ohne Vermittler. Um uns zu kontaktieren:', NULL, 'support@cultibox.fr', 'mail', 'de_DE');

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE IF NOT EXISTS `sensors` (
  `id` int(11) NOT NULL,
  `type` varchar(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`id`, `type`) VALUES (1, '2'), (2, '2'), (3, '2'), (4, '2'), (5, '0'),(6, '0');

-- --------------------------------------------------------


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

