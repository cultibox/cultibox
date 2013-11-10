SET CHARACTER SET utf8;

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

