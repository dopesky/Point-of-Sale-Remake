ALTER TABLE `tbl_users` CHANGE `last_access_time` `last_access_time` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `tbl_users` CHANGE `email` `email` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
SET GLOBAL time_zone = '+03:00';