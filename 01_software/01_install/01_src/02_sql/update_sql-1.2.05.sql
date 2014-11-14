SET CHARACTER SET utf8;

-- Version 1.2.05
UPDATE `cultibox`.`calendar` SET `EndTime` = ADDTIME(EndTime, '23:59:59');

