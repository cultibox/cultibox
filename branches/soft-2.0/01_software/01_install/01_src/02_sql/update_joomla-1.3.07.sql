SET CHARACTER SET utf8;

-- Version 1.3.07
DELETE FROM `cultibox_joomla`.`dkg45_content` WHERE `title` LIKE "historic%";
DELETE FROM `cultibox_joomla`.`dkg45_assets` WHERE `title` LIKE "historic";
DELETE FROM `cultibox_joomla`.`dkg45_menu` WHERE `alias` LIKE "historic-%";
DELETE FROM `cultibox_joomla`.`dkg45_redirect_links` WHERE `referer` LIKE "http://localhost:6891/cultibox/index.php/historic%";
DELETE FROM `cultibox_joomla`.`dkg45_redirect_links` WHERE `old_url` LIKE "http://localhost:6891/cultibox/index.php/historic%";
