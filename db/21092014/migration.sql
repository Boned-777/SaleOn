ALTER TABLE `brands`
ADD COLUMN `status` VARCHAR(45) NULL AFTER `name`,
ADD COLUMN `partner` INT NULL AFTER `status`,
ADD COLUMN `user` INT NULL AFTER `partner`,
ADD COLUMN `created` DATETIME NULL AFTER `user`;
