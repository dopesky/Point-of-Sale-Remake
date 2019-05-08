CREATE TABLE `apikey_owners` (
  `owner_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_email` varchar(60) NOT NULL,
  `owner_password` varchar(255) DEFAULT NULL,
  `token` varchar(30) NOT NULL,
  `token_expire` int(11) NOT NULL DEFAULT '3600',
  `last_access_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suspended` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1