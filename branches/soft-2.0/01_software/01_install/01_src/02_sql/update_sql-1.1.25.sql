SET CHARACTER SET utf8;

-- Version 1.1.25:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `ALARM_SENSO`;
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `ALARM_SENSS`;

ALTER TABLE `cultibox`.`configuration` ADD `SHOW_COST` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`configuration` ADD `SHOW_WIZARD` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`configuration` ADD `SHOW_HISTORIC` VARCHAR( 5 ) NOT NULL DEFAULT 'False';

