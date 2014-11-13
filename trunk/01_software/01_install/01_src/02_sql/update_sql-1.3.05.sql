SET CHARACTER SET utf8;

-- Version 1.3.04:
ALTER TABLE `cultibox`.`programs` ADD `type` INT NOT NULL DEFAULT '0';
UPDATE `cultibox`.`programs` SET `type`='1' WHERE  (`value` < 99.9) AND (`value` > 0);
