CREATE TABLE IF NOT EXISTS `cultibox`.`informations` ( `ID` int(11) NOT NULL AUTO_INCREMENT, `cbx_id` int(5) NOT NULL DEFAULT '0', `firm_version` int(5) NOT NULL DEFAULT '0', `emeteur_version` varchar(7) NOT NULL DEFAULT '000.000', `sensor_version` varchar(7) NOT NULL DEFAULT '000.000', `last_reboot` varchar(14) NOT NULL DEFAULT '00000000000000', `nb_reboot` int(11) NOT NULL DEFAULT '0', `id_computer` varchar(50) NOT NULL DEFAULT 'NULL', `log` mediumtext, PRIMARY KEY (`ID`), KEY `ID` (`ID`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `cultibox`.`informations` (`ID` ,`cbx_id` ,`firm_version` ,`emeteur_version` ,`sensor_version` ,`last_reboot` ,`nb_reboot` ,`id_computer`,`log`) VALUES (NULL , '0', '0', '000.000', '000.000', '00000000000000', '0', 'NULL','');

