ALTER TABLE `cultibox`.`logs` ADD `sensor_nb` INT NOT NULL DEFAULT '1';

ALTER TABLE `cultibox`.`configuration` ADD `STATISTICS` VARCHAR( 5 ) NOT NULL DEFAULT 'True';

CREATE TABLE IF NOT EXISTS `cultibox`.`historic` (`id` int(11) NOT NULL AUTO_INCREMENT,`timestamp` varchar(25) NOT NULL,`action` varchar(300) NOT NULL,`type` VARCHAR( 15 ) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `cultibox`.`programs` ADD `number` INT NOT NULL DEFAULT '1';

ALTER TABLE `cultibox`.`programs` ADD `date_start` VARCHAR( 10 ) NOT NULL DEFAULT '0000-00-00', ADD `date_end` VARCHAR( 10 ) NOT NULL DEFAULT '0000-00-00';

ALTER TABLE `cultibox`.`configuration` DROP COLUMN `COLOR_PROGRAM_GRAPH`;

ALTER TABLE `cultibox`.`informations` CHANGE `firm_version` `firm_version` VARCHAR( 7 ) NOT NULL DEFAULT '000.000';

-- Version 1.1.3:
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_ENABLED` VARCHAR( 5 ) NOT NULL DEFAULT 'True';

-- Version 1.1.5:
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_POWER_MAX` VARCHAR( 10 ) NOT NULL DEFAULT '1000' AFTER `PLUG_POWER`;
UPDATE `cultibox`.`plugs` SET `PLUG_POWER_MAX` = '3500' WHERE `plugs`.`id` =1;

UPDATE `cultibox`.`configuration` SET `VERSION` = '1.1.5' WHERE `configuration`.`id` =1;
DROP DATABASE `cultibox_joomla`;

