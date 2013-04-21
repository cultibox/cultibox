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

-- Version 1.1.7:
UPDATE `cultibox`.`plugs` SET `PLUG_ID` = '';

UPDATE `cultibox`.`configuration` SET `VERSION` = '1.1.13-amd64' WHERE `configuration`.`id` =1;

-- Version 1.1.8:
ALTER TABLE `cultibox`.`configuration` DROP `LOG_TEMP_AXIS` , DROP `LOG_HYGRO_AXIS` ;
UPDATE `cultibox`.`configuration` SET `CHECK_UPDATE` = 'True' WHERE `configuration`.`id` =1;


-- Version 1.1.10:
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_REGUL_SENSOR` INT NOT NULL DEFAULT '1' AFTER `PLUG_REGUL`;

-- Version 1.1.11:
ALTER TABLE `cultibox`.`configuration` ADD `SECOND_REGUL` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`configuration` DROP `LOG_TEMP_AXIS` , DROP `LOG_HYGRO_AXIS` , DROP `LOG_POWER_AXIS`;

-- Version 1.1.12:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `LANG`;

-- Version 1.1.13:
RENAME TABLE `cultibox`.`jqcalendar` TO `cultibox`.`calendar`; 
ALTER TABLE `cultibox`.`calendar` CHANGE `Subject` `Title` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `cultibox`.`calendar` DROP COLUMN `color`;
ALTER TABLE `cultibox`.`calendar` DROP COLUMN `RecurringRule`;
ALTER TABLE `cultibox`.`calendar` DROP COLUMN `Location`;
ALTER TABLE `cultibox`.`calendar` ADD `Color` VARCHAR( 7 ) NOT NULL DEFAULT '#000000';
ALTER TABLE `cultibox`.`calendar` CHANGE `IsAllDayEvent` `External` SMALLINT( 6 ) NOT NULL DEFAULT '0';
REVOKE ALL PRIVILEGES ON * . * FROM 'cultibox'@'localhost';
REVOKE GRANT OPTION ON * . * FROM 'cultibox'@'localhost';


GRANT SELECT , INSERT , UPDATE , DELETE , DROP, LOCK TABLES, FILE ON * . * TO 'cultibox'@'localhost' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
