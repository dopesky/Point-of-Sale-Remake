INSERT INTO `apikeypowers` (`apikeypower_id`, `apikey_power`) VALUES (1, 'READ'), (2, 'WRITE'), (3, 'BOTH');
ALTER TABLE `tbl_users` CHANGE `session_id` `biometrics_id` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_users` ADD UNIQUE(`biometrics_id`);
ALTER TABLE `tbl_users` CHANGE `password` `password` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `tbl_users` CHANGE `suspended` `suspended` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `tbl_users` CHANGE `created_time` `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `last_access_time` `last_access_time` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `tbl_users` CHANGE `token` `token` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL, CHANGE `token_expire` `token_expire` BIGINT(10) NOT NULL DEFAULT '3600';
ALTER TABLE `owner` ADD `profile_photo` VARCHAR(255) NOT NULL AFTER `company`;
INSERT INTO `departments` (`department_id`, `department`, `modified_date`, `create_date`, `suspended`) VALUES (1, 'sales', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0');