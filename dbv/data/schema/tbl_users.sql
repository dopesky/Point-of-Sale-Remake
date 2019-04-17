CREATE TABLE `tbl_users` (
  `user_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(60) NOT NULL DEFAULT '',
  `session_id` varchar(32) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL,
  `token_expire` bigint(10) DEFAULT NULL,
  `created_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_access_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `suspended` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1