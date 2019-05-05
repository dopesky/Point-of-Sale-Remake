CREATE TABLE `apikeypowers` (
  `apikeypower_id` int(11) NOT NULL AUTO_INCREMENT,
  `apikey_power` varchar(12) NOT NULL,
  PRIMARY KEY (`apikeypower_id`),
  UNIQUE KEY `apikey_power` (`apikey_power`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1