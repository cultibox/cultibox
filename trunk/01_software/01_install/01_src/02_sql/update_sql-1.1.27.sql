SET CHARACTER SET utf8;

-- Version 1.1.27:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `LOG_SEARCH`;
ALTER TABLE `cultibox`.`informations` DROP `emeteur_version`, DROP `sensor_version`, DROP `last_reboot`, DROP `nb_reboot`;

