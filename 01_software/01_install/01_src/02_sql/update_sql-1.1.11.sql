SET CHARACTER SET utf8;

-- Version 1.1.11:
ALTER TABLE `cultibox`.`configuration` ADD `SECOND_REGUL` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`configuration` DROP `LOG_TEMP_AXIS` , DROP `LOG_HYGRO_AXIS` , DROP `LOG_POWER_AXIS`;

