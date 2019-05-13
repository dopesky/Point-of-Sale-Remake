CREATE TABLE `apikeys` (
  `apikey_id` int(11) NOT NULL AUTO_INCREMENT,
  `apikey` varchar(255) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `apikeypower_id` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`apikey_id`),
  UNIQUE KEY `apikey` (`apikey`),
  KEY `apikeypower_id` (`apikeypower_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `apikeys_ibfk_1` FOREIGN KEY (`apikeypower_id`) REFERENCES `apikeypowers` (`apikeypower_id`),
  CONSTRAINT `apikeys_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `apikey_owners` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1