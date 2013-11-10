SET CHARACTER SET utf8;

-- Version 1.1.14:
ALTER TABLE `cultibox`.`power` ADD INDEX ( `timestamp` );
ALTER TABLE `cultibox`.`logs` ADD INDEX ( `timestamp` );

