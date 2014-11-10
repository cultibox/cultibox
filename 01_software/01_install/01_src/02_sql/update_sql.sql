SET CHARACTER SET utf8;

UPDATE `cultibox`.`configuration` SET `VERSION` = '1.4.10-noarch' WHERE `configuration`.`id` =1;
ALTER TABLE `cultibox`.`configuration` ADD `DEFAULT_LANG` VARCHAR(5) NOT NULL DEFAULT 'fr_FR'; 
DROP DATABASE IF EXISTS `cultibox_joomla`;

