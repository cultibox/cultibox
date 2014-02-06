SET CHARACTER SET utf8;

-- Version 1.3.01
CREATE TABLE IF NOT EXISTS `cultibox`.`sensors` ( `id` int(11) NOT NULL, `type` int(5) NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `cultibox`.`sensors` (`id`, `type`) VALUES (1, 0), (2, 0), (3, 0), (4, 0);
