SET CHARACTER SET utf8;

-- Version 1.1.26:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `SHOW_WIZARD`;
UPDATE `cultibox_joomla`.`dkg45_menu` SET  published = "1" WHERE alias LIKE "wizard-%";

