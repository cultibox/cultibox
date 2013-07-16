-- ALTER TABLE `cultibox`.`logs` ADD `sensor_nb` INT NOT NULL DEFAULT '1';

-- ALTER TABLE `cultibox`.`configuration` ADD `STATISTICS` VARCHAR( 5 ) NOT NULL DEFAULT 'True';

-- CREATE TABLE IF NOT EXISTS `cultibox`.`historic` (`id` int(11) NOT NULL AUTO_INCREMENT,`timestamp` varchar(25) NOT NULL,`action` varchar(300) NOT NULL,`type` VARCHAR( 15 ) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- ALTER TABLE `cultibox`.`programs` ADD `number` INT NOT NULL DEFAULT '1';

-- ALTER TABLE `cultibox`.`programs` ADD `date_start` VARCHAR( 10 ) NOT NULL DEFAULT '0000-00-00', ADD `date_end` VARCHAR( 10 ) NOT NULL DEFAULT '0000-00-00';

-- ALTER TABLE `cultibox`.`configuration` DROP COLUMN `COLOR_PROGRAM_GRAPH`;

-- ALTER TABLE `cultibox`.`informations` CHANGE `firm_version` `firm_version` VARCHAR( 7 ) NOT NULL DEFAULT '000.000';

-- Version 1.1.3:
-- ALTER TABLE `cultibox`.`plugs` ADD `PLUG_ENABLED` VARCHAR( 5 ) NOT NULL DEFAULT 'True';

-- Version 1.1.5:
-- ALTER TABLE `cultibox`.`plugs` ADD `PLUG_POWER_MAX` VARCHAR( 10 ) NOT NULL DEFAULT '1000' AFTER `PLUG_POWER`;
-- UPDATE `cultibox`.`plugs` SET `PLUG_POWER_MAX` = '3500' WHERE `plugs`.`id` =1;

-- Version 1.1.7:
-- UPDATE `cultibox`.`plugs` SET `PLUG_ID` = '';

-- UPDATE `cultibox`.`configuration` SET `VERSION` = '1.1.13-amd64' WHERE `configuration`.`id` =1;

-- Version 1.1.8:
-- ALTER TABLE `cultibox`.`configuration` DROP `LOG_TEMP_AXIS` , DROP `LOG_HYGRO_AXIS` ;
-- UPDATE `cultibox`.`configuration` SET `CHECK_UPDATE` = 'True' WHERE `configuration`.`id` =1;


-- Version 1.1.10:
-- ALTER TABLE `cultibox`.`plugs` ADD `PLUG_REGUL_SENSOR` INT NOT NULL DEFAULT '1' AFTER `PLUG_REGUL`;

-- Version 1.1.11:
-- ALTER TABLE `cultibox`.`configuration` ADD `SECOND_REGUL` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
-- ALTER TABLE `cultibox`.`configuration` DROP `LOG_TEMP_AXIS` , DROP `LOG_HYGRO_AXIS` , DROP `LOG_POWER_AXIS`;

-- Version 1.1.12:
-- ALTER TABLE `cultibox`.`configuration` DROP COLUMN `LANG`;

-- Version 1.1.13:
-- RENAME TABLE `cultibox`.`jqcalendar` TO `cultibox`.`calendar`; 
-- ALTER TABLE `cultibox`.`calendar` CHANGE `Subject` `Title` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
-- ALTER TABLE `cultibox`.`calendar` DROP COLUMN `color`;
-- ALTER TABLE `cultibox`.`calendar` DROP COLUMN `RecurringRule`;
-- ALTER TABLE `cultibox`.`calendar` DROP COLUMN `Location`;
-- ALTER TABLE `cultibox`.`calendar` ADD `Color` VARCHAR( 7 ) NOT NULL DEFAULT '#000000';
-- ALTER TABLE `cultibox`.`calendar` CHANGE `IsAllDayEvent` `External` SMALLINT( 6 ) NOT NULL DEFAULT '0';
-- REVOKE ALL PRIVILEGES ON * . * FROM 'cultibox'@'localhost';
-- REVOKE GRANT OPTION ON * . * FROM 'cultibox'@'localhost';
-- GRANT SELECT , INSERT , UPDATE , DELETE , DROP, LOCK TABLES, FILE ON * . * TO 'cultibox'@'localhost' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

-- Version 1.1.14:
-- ALTER TABLE `cultibox`.`power` ADD INDEX ( `timestamp` );
-- ALTER TABLE `cultibox`.`logs` ADD INDEX ( `timestamp` );

-- Version 1.1.19:
-- ALTER TABLE `cultibox`.`configuration` ADD `REGUL_SENSOR` VARCHAR( 5 ) NOT NULL DEFAULT 'True';
-- ALTER TABLE `cultibox`.`plugs` CHANGE `PLUG_POWER` `PLUG_POWER` INT( 11 ) NULL DEFAULT NULL;

-- Version 1.1.25:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `ALARM_SENSO`;
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `ALARM_SENSS`;

ALTER TABLE `cultibox`.`configuration` ADD `SHOW_COST` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`configuration` ADD `SHOW_WIZARD` VARCHAR( 5 ) NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`configuration` ADD `SHOW_HISTORIC` VARCHAR( 5 ) NOT NULL DEFAULT 'False';

-- Version 1.1.26:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `SHOW_WIZARD`;
UPDATE `cultibox_joomla`.`dkg45_menu` SET  published = "1" WHERE alias LIKE "wizard-%";

-- Version 1.1.27:
ALTER TABLE `cultibox`.`configuration` DROP COLUMN `LOG_SEARCH`;
ALTER TABLE `cultibox`.`informations` DROP `emeteur_version`, DROP `sensor_version`, DROP `last_reboot`, DROP `nb_reboot`;

-- Version 1.1.28:
CREATE TABLE IF NOT EXISTS `notes` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(50) NOT NULL, `desc` varchar(500) NOT NULL, `image` varchar(50) DEFAULT NULL, `link` varchar(50) DEFAULT NULL, `type_link` varchar(30) DEFAULT NULL, `lang` varchar(5) NOT NULL DEFAULT 'fr_FR', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `notes` (`id`, `title`, `desc`, `image`, `link`, `type_link`, `lang`) VALUES (1, 'Documentation', 'Retrouver une documentation plus complète dans le logiciel en cliquant sur l’onglet <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Aide</a>. La version la plus à jour de l’aide est disponible à l’adresse suivante:', NULL, 'https://code.google.com/p/cultibox', 'external', 'fr_FR'), (2, 'Des questions ?', 'Si l’aide ne suffit pas pour répondre à une de vos questions, envoyez-nous un mail à l’adresse suivante :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'), (3, 'Accessoires', 'Vous pouvez compléter votre pack en achetant des capteurs supplémentaires, des prises 1000W et 3500W ou encore d’autres accessoires en vous rendant sur le site :', NULL, 'www.cultibox.fr', 'external', 'fr_FR'), (4, 'Recylcage', 'Chez Cultibox nous retraitons tous les élément de nos produits. L’emballage peut être recyclé et la Cultibox contient une pile lithium qui ne doit pas être jetée à la poubelle. Pour un recyclage optimal, renvoyez nous la Cultibox et vous serez récompensé.', 'recycling.png', NULL, NULL, 'fr_FR'), (5, 'Garantie', 'La Cultibox ainsi que ses accessoires sont garantis deux ans. Nous assurons la garantie en direct, sans intermédiaire. Pour nous contacter :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR');

 
