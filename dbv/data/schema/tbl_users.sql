CREATE TABLE `tbl_users` (
  `user_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(60) NOT NULL,
  `password` varchar(60) NULL DEFAULT NULL,
  `biometrics_id` VARCHAR(32) NULL DEFAULT NULL,
  `twofactor_auth` BOOLEAN NOT NULL DEFAULT FALSE,
  `twofactor_secret` VARCHAR(255) NULL DEFAULT NULL,
  `token` VARCHAR(32) NOT NULL,
  `token_expire` BIGINT(10) NOT NULL DEFAULT '3600',
  `show_inactive` BOOLEAN NOT NULL DEFAULT TRUE,
  `show_deleted` BOOLEAN NOT NULL DEFAULT TRUE,
  `country_id` INT NOT NULL DEFAULT 110,
  `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_access_time` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suspended` BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`token`),
  UNIQUE(`biometrics_id`),
  FOREIGN KEY (`country_id`) REFERENCES `countries`(`country_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1
