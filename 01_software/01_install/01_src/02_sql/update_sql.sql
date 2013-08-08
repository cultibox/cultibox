SET CHARACTER SET utf8;

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

UPDATE `cultibox`.`configuration` SET `VERSION` = '1.1.13-amd64' WHERE `configuration`.`id` =1;

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
CREATE TABLE IF NOT EXISTS `cultibox`.`notes` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(50) NOT NULL, `desc` varchar(500) NOT NULL, `image` varchar(50) DEFAULT NULL, `link` varchar(50) DEFAULT NULL, `type_link` varchar(30) DEFAULT NULL, `lang` varchar(5) NOT NULL DEFAULT 'fr_FR', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `cultibox`.`notes` (`id`, `title`, `desc`, `image`, `link`, `type_link`, `lang`) VALUES (1, 'Documentation', 'Retrouver une documentation plus complète dans le logiciel en cliquant sur l’onglet <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Aide</a>. La version la plus à jour de l’aide est disponible à l’adresse suivante:', NULL, 'https://code.google.com/p/cultibox', 'external', 'fr_FR'), (2, 'Des questions ?', 'Si l’aide ne suffit pas pour répondre à une de vos questions, envoyez-nous un mail à l’adresse suivante :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'), (3, 'Accessoires', 'Vous pouvez compléter votre pack en achetant des capteurs supplémentaires, des prises 1000W et 3500W ou encore d’autres accessoires en vous rendant sur le site :', NULL, 'http://www.cultibox.fr', 'external', 'fr_FR'), (4, 'Recyclage', 'Chez Cultibox nous retraitons tous les élément de nos produits. L’emballage peut être recyclé et la Cultibox contient une pile lithium qui ne doit pas être jetée à la poubelle. Pour un recyclage optimal, renvoyez nous la Cultibox et vous serez récompensé.', 'recycling.png', NULL, NULL, 'fr_FR'), (5, 'Garantie', 'La Cultibox ainsi que ses accessoires sont garantis deux ans. Nous assurons la garantie en direct, sans intermédiaire. Pour nous contacter :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR');

ALTER TABLE `cultibox`.`configuration` ADD `RESET_MINMAX` VARCHAR(5) NOT NULL DEFAULT '00:00';
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_SECOND_TOLERANCE` DECIMAL( 3, 1 ) NOT NULL DEFAULT '0.0';
ALTER TABLE `cultibox`.`configuration` CHANGE `REGUL_SENSOR` `ADVANCED_REGUL_OPTIONS` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`plugs` CHANGE `PLUG_REGUL_SENSOR` `PLUG_REGUL_SENSOR` VARCHAR( 7 ) NOT NULL DEFAULT '1';
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_COMPUTE_METHOD` VARCHAR( 1 ) NOT NULL DEFAULT 'M';
ALTER TABLE `cultibox`.`logs` ADD `type_sensor` INT NOT NULL DEFAULT '2';

-- Version 1.1.29
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Documentation', 'Find a more complete documentation in the software by clicking on the Help tab <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank"> </ a>. The most current version is available using the following address:', NULL , 'https://code.google.com/p/cultibox', 'external', 'en_GB');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Some questions?', 'If help is not enough to answer one of your question, send us an email at the following address:', NULL , 'support@cultibox.fr', 'mail', 'en_GB');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Accessories', 'You can complete your package by purchasing additional sensors, 1000W and 3500W plugs or other accessories by visiting the website:', NULL , 'http://www.cultibox.fr', 'external', 'en_GB');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Recycling', 'At Cultibox we reprocess all part of our products. The packaging can be recycled and Cultibox contains a lithium battery that should not be thrown away. For optimal recycling, return us the Cultibox and you will be rewarded.', 'recycling.png', NULL , NULL , 'en_GB');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Warrantly', 'The Cultibox and accessories are warranted for two years. We ensure the security directly, without intermediary. To contact us:', NULL , 'support@cultibox.fr', 'mail', 'en_GB');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Documentación', 'Encuentra una documentación más completa en el software haciendo clic en la pestaña <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Ayuda </ a>. La versión más actualizada se encuentra disponible la siguiente dirección:', NULL , 'https://code.google.com/p/cultibox', 'external', 'es_ES');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , '¿Preguntas?', 'Si la ayuda no es suficiente para cumplir con una de sus preguntas, envíe un correo electrónico a la siguiente dirección:', NULL , 'support@cultibox.fr', 'mail', 'es_ES');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Accesorios', 'Puede completar su paquete mediante la compra de sensores adicionales, 1000W y 3500W accesorios tomadas u otro, visitando el sitio web:', NULL , 'http://www.cultibox.fr', 'external', 'es_ES');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Reciclaje', 'En Cultibox que reprocesar parte de nuestros productos. El embalaje es reciclable y Cultibox contiene una batería de litio que no debe ser desechada. Para un reciclaje óptimo, nos devuelven Cultibox y serás recompensado.', 'recycling.png', NULL , NULL , 'es_ES');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Garantía', 'El Cultibox y accesorios tienen una garantía de dos años. Garantizamos la seguridad directamente, sin intermediario. Para contactar con nosotros:', NULL , 'support@cultibox.fr', 'mail', 'es_ES');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Documentazione', 'Trova una documentazione più completa del software facendo clic sulla scheda <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Guida</a>. La versione più aggiornata è disponibile al seguente indirizzo:', NULL , 'https://code.google.com/p/cultibox', 'external', 'it_IT');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Domande?', 'Se l''aiuto non è sufficiente a soddisfare una delle vostre domande, inviateci una e-mail al seguente indirizzo:', NULL , 'support@cultibox.fr', 'mail', 'it_IT');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Accessori', 'È possibile completare il pacchetto con l''acquisto di sensori supplementari, 1000W e 3500W accessori adottate o altro visitando il sito:', NULL , 'http://www.cultibox.fr', 'external', 'it_IT');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Riciclaggio', 'In Cultibox abbiamo rielaborare tutti parte dei nostri prodotti. L''imballaggio può essere riciclato e Cultibox contiene una batteria al litio che non deve essere gettato via. Per il riciclaggio ottimale, noi tornare Cultibox e sarete ricompensati.', 'recycling.png', NULL , NULL , 'it_IT');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Garanzia', 'Il Cultibox e gli accessori sono garantiti per due anni. Noi garantiamo la sicurezza direttamente, senza intermediari. Per contattarci:', NULL , 'support@cultibox.fr', 'mail', 'it_IT');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Dokumentation', 'Finden Sie eine umfassendere Dokumentation in der Software, indem Sie auf der <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank"> Registerkarte Hilfe</ a>. Die aktuelle Version ist verfügbar unter folgender Adresse:', NULL , 'https://code.google.com/p/cultibox', 'external', 'de_DE');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Haben Sie Fragen?', 'Ist die Beihilfe ist nicht genug, um eine Ihrer Fragen gerecht zu werden, senden Sie uns eine E-Mail an die folgende Adresse:', NULL , 'support@cultibox.fr', 'mail', 'de_DE');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Zubehör', 'Sie können Ihr Paket durch den Kauf von zusätzlichen Sensoren, 1000W und 3500W genommen oder anderem Zubehör durch den Besuch der Website zu vervollständigen:', NULL , 'http://www.cultibox.fr', 'external', 'de_DE');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Recycling', 'In Cultibox wir nachbearbeiten alle Teil unserer Produkte. Die Verpackung kann recycelt werden und Cultibox enthält eine Lithium-Batterie, die nicht geworfen sollten entfernt werden. Für eine optimale Wiederverwertung, bringen uns Cultibox und Sie werden belohnt werden.', 'recycling.png', NULL , NULL , 'de_DE');
INSERT INTO `cultibox`.`notes` ( `id` , `title` , `desc` , `image` , `link` , `type_link` , `lang`) VALUES ( NULL , 'Garantie', 'Die Cultibox und Zubehör sind für zwei Jahre garantiert. Wir sorgen für die Sicherheit direkt, ohne Vermittler. Um uns zu kontaktieren:', NULL , 'support@cultibox.fr', 'mail', 'de_DE');

UPDATE `cultibox`.`notes` SET `title` = 'Recyclage' WHERE `notes`.`id` =4;
ALTER TABLE `cultibox`.`calendar` ADD `Icon` VARCHAR( 30 ) NULL ;

-- Version 1.1.30
DELETE FROM `cultibox`.`notes` WHERE `lang` LIKE "fr_FR";
INSERT INTO `cultibox`.`notes` (`id`, `title`, `desc`, `image`, `link`, `type_link`, `lang`) VALUES (1, 'Documentation', 'Retrouver une documentation plus complète dans le logiciel en cliquant sur l’onglet <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Aide</a>. La version la plus à jour de l’aide est disponible à l’adresse suivante:', NULL, 'https://code.google.com/p/cultibox', 'external', 'fr_FR'), (2, 'Des questions ?', 'Si l’aide ne suffit pas pour répondre à une de vos questions, envoyez-nous un mail à l’adresse suivante :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'), (3, 'Accessoires', 'Vous pouvez compléter votre pack en achetant des capteurs supplémentaires, des prises 1000W et 3500W ou encore d’autres accessoires en vous rendant sur le site :', NULL, 'http://www.cultibox.fr', 'external', 'fr_FR'), (4, 'Recyclage', 'Chez Cultibox nous retraitons tous les élément de nos produits. L’emballage peut être recyclé et la Cultibox contient une pile lithium qui ne doit pas être jetée à la poubelle. Pour un recyclage optimal, renvoyez nous la Cultibox et vous serez récompensé.', 'recycling.png', NULL, NULL, 'fr_FR'), (5, 'Garantie', 'La Cultibox ainsi que ses accessoires sont garantis deux ans. Nous assurons la garantie en direct, sans intermédiaire. Pour nous contacter :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR');
ALTER TABLE `cultibox`.`calendar` CHANGE `Description` `Description` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
