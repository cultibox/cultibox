SET CHARACTER SET utf8;

-- Version 1.3.06:
INSERT INTO `cultibox`.`sensors` (`id`, `type`) VALUES (5, 0),(6, 0);
ALTER TABLE `cultibox`.`sensors` CHANGE `type`  `type` varchar(1) NOT NULL DEFAULT '0';
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=1 GROUP BY `type_sensor`) WHERE id=1;
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=2 GROUP BY `type_sensor`) WHERE id=2;
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=3 GROUP BY `type_sensor`) WHERE id=3;
UPDATE `cultibox`.`sensors` set `type` = (SELECT `type_sensor` FROM `cultibox`.`logs` where `sensor_nb`=4 GROUP BY `type_sensor`) WHERE id=4;
UPDATE `cultibox`.`sensors` set `type` = '0' WHERE `type` LIKE '';
ALTER TABLE `cultibox`.`logs` CHANGE `temperature` `record1` int(4) DEFAULT NULL;
ALTER TABLE `cultibox`.`logs` CHANGE `humidity` `record2` int(4) DEFAULT NULL;
ALTER TABLE `cultibox`.`logs` DROP `type_sensor`;
