SET CHARACTER SET utf8;

-- Version 1.2.13
CREATE TABLE IF NOT EXISTS `sensors` ( `id` int(11) NOT NULL, `type` int(5) NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `sensors` (`id`, `type`) VALUES (1, 0), (2, 0), (3, 0), (4, 0);
