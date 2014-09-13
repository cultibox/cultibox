SET CHARACTER SET utf8;

-- Version 1.1.28:
CREATE TABLE IF NOT EXISTS `cultibox`.`notes` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(50) NOT NULL, `desc` varchar(500) NOT NULL, `image` varchar(50) DEFAULT NULL, `link` varchar(50) DEFAULT NULL, `type_link` varchar(30) DEFAULT NULL, `lang` varchar(5) NOT NULL DEFAULT 'fr_FR', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `cultibox`.`notes` (`id`, `title`, `desc`, `image`, `link`, `type_link`, `lang`) VALUES (1, 'Documentation', 'Retrouver une documentation plus complète dans le logiciel en cliquant sur l’onglet <a href="http://localhost:6891/cultibox/main/docs/documentation_cultibox.pdf" target="_blank">Aide</a>. La version la plus à jour de l’aide est disponible à l’adresse suivante:', NULL, 'https://code.google.com/p/cultibox', 'external', 'fr_FR'), (2, 'Des questions ?', 'Si l’aide ne suffit pas pour répondre à une de vos questions, envoyez-nous un mail à l’adresse suivante :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR'), (3, 'Accessoires', 'Vous pouvez compléter votre pack en achetant des capteurs supplémentaires, des prises 1000W et 3500W ou encore d’autres accessoires en vous rendant sur le site :', NULL, 'http://www.cultibox.fr', 'external', 'fr_FR'), (4, 'Recyclage', 'Chez Cultibox nous retraitons tous les élément de nos produits. L’emballage peut être recyclé et la Cultibox contient une pile lithium qui ne doit pas être jetée à la poubelle. Pour un recyclage optimal, renvoyez nous la Cultibox et vous serez récompensé.', 'recycling.png', NULL, NULL, 'fr_FR'), (5, 'Garantie', 'La Cultibox ainsi que ses accessoires sont garantis deux ans. Nous assurons la garantie en direct, sans intermédiaire. Pour nous contacter :', NULL, 'support@cultibox.fr', 'mail', 'fr_FR');

ALTER TABLE `cultibox`.`configuration` ADD `RESET_MINMAX` VARCHAR(5) NOT NULL DEFAULT '00:00';
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_SECOND_TOLERANCE` DECIMAL( 3, 1 ) NOT NULL DEFAULT '0.0';
ALTER TABLE `cultibox`.`configuration` CHANGE `REGUL_SENSOR` `ADVANCED_REGUL_OPTIONS` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'False';
ALTER TABLE `cultibox`.`plugs` CHANGE `PLUG_REGUL_SENSOR` `PLUG_REGUL_SENSOR` VARCHAR( 7 ) NOT NULL DEFAULT '1';
ALTER TABLE `cultibox`.`plugs` ADD `PLUG_COMPUTE_METHOD` VARCHAR( 1 ) NOT NULL DEFAULT 'M';
ALTER TABLE `cultibox`.`logs` ADD `type_sensor` INT NOT NULL DEFAULT '2';

