ALTER TABLE `tbl_users` CHANGE `twofactor_secret` `twofactor_secret` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `tbl_users` CHANGE `twofactor_auth` `twofactor_auth` INT NOT NULL DEFAULT '0';