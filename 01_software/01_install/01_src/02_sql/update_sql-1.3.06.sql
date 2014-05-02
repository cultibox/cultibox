SET CHARACTER SET utf8;

-- Version 1.3.04:
INSERT INTO `cultibox`.`sensors` (`id`, `type`) VALUES (5, 0),(6, 0);
ALTER TABLE `cultibox`.`sensors` CHANGE `type`  `type` varchar(1) NOT NULL DEFAULT '0';
ALTER TABLE `cultibox`.`configuration` ADD  `RTC_OFFSET` DECIMAL(3,2) NOT NULL DEFAULT '0';
ALTER TABLE `cultibox`.`logs` CHANGE `temperature` `record1` int(4) DEFAULT NULL;
ALTER TABLE `cultibox`.`logs` CHANGE `humidity` `record2` int(4) DEFAULT NULL;
ALTER TABLE `cultibox`.`logs` DROP `type_sensor`;
