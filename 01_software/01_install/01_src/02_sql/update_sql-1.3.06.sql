SET CHARACTER SET utf8;

-- Version 1.3.04:
INSERT INTO `cultibox`.`sensors` (`id`, `type`) VALUES (5, 0),(6, 0);
ALTER TABLE `cultibox`.`sensors` CHANGE `type`  `type` varchar(1) NOT NULL DEFAULT '0';
