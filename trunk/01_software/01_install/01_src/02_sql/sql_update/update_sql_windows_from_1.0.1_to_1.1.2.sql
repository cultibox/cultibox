ALTER TABLE `cultibox`.`configuration` ADD `STATISTICS` VARCHAR( 5 ) NOT NULL DEFAULT 'True';

CREATE TABLE IF NOT EXISTS `historic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` varchar(25) NOT NULL,
  `action` varchar(300) NOT NULL,
  `type` VARCHAR( 15 ) NOT NULL,                           
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `programs` ADD `number` INT NOT NULL DEFAULT '1';

ALTER TABLE `programs` ADD `date_start` VARCHAR( 10 ) NOT NULL DEFAULT '0000-00-00',
ADD `date_end` VARCHAR( 10 ) NOT NULL DEFAULT '0000-00-00';

