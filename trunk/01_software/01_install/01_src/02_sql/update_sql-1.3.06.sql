SET CHARACTER SET utf8;

-- Version 1.3.04:
INSERT INTO `cultibox`.`sensors` (`id`, `type`) VALUES (5, 0),(6, 0);
ALTER TABLE `cultibox`.`sensors` CHANGE `type`  `type` varchar(1) NOT NULL DEFAULT '0';
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=1 GROUP BY `type_sensor`) WHERE id=1;
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=2 GROUP BY `type_sensor`) WHERE id=2;
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=3 GROUP BY `type_sensor`) WHERE id=3;
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=4 GROUP BY `type_sensor`) WHERE id=4;
UPDATE `cultibox`.`sensors` set `type` = '0' WHERE `type` LIKE '';
ALTER TABLE `cultibox`.`configuration` ADD  `RTC_OFFSET` DECIMAL(3,2) NOT NULL DEFAULT '0';
ALTER TABLE `cultibox`.`logs` CHANGE `temperature` `record1` int(4) DEFAULT NULL;
ALTER TABLE `cultibox`.`logs` CHANGE `humidity` `record2` int(4) DEFAULT NULL;
ALTER TABLE `cultibox`.`logs` DROP `type_sensor`;
ALTER TABLE `cultibox`.`configuration` ADD `COLOR_WATER_GRAPH` varchar(30) NOT NULL DEFAULT 'orange' AFTER `COLOR_HUMIDITY_GRAPH`;
ALTER TABLE `cultibox`.`configuration` ADD `COLOR_LEVEL_GRAPH` varchar(30) NOT NULL DEFAULT 'pink' AFTER `COLOR_HUMIDITY_GRAPH`;
ALTER TABLE `cultibox`.`configuration` ADD `COLOR_PH_GRAPH` varchar(30) NOT NULL DEFAULT 'brown' AFTER `COLOR_HUMIDITY_GRAPH`;
ALTER TABLE `cultibox`.`configuration` ADD `COLOR_EC_GRAPH` varchar(30) NOT NULL DEFAULT 'yellow' AFTER `COLOR_HUMIDITY_GRAPH`;
ALTER TABLE `cultibox`.`configuration` ADD `COLOR_OD_GRAPH` varchar(30) NOT NULL DEFAULT 'red' AFTER `COLOR_HUMIDITY_GRAPH`;
ALTER TABLE `cultibox`.`configuration` ADD `COLOR_ORP_GRAPH` varchar(30) NOT NULL DEFAULT 'blue' AFTER `COLOR_HUMIDITY_GRAPH`;

