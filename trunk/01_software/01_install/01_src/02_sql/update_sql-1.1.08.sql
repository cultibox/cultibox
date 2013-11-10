SET CHARACTER SET utf8;

-- Version 1.1.08:
ALTER TABLE `cultibox`.`configuration` DROP `LOG_TEMP_AXIS` , DROP `LOG_HYGRO_AXIS` ;
UPDATE `cultibox`.`configuration` SET `CHECK_UPDATE` = 'True' WHERE `configuration`.`id` =1;


