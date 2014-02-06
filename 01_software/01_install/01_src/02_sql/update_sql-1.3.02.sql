SET CHARACTER SET utf8;

-- Version 1.3.02
ALTER TABLE `cultibox`.`configuration` ADD `WIFI` BOOLEAN NOT NULL DEFAULT 0;
ALTER TABLE `cultibox`.`configuration` ADD `WIFI_SSID` VARCHAR(32);
ALTER TABLE `cultibox`.`configuration` ADD `WIFI_KEY_TYPE` VARCHAR(10) NOT NULL DEFAULT 'NONE';
ALTER TABLE `cultibox`.`configuration` ADD `WIFI_PASSWORD` VARCHAR(63);

