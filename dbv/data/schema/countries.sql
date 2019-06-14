CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(60) NOT NULL,
  `continent` varchar(20) NOT NULL,
  `abbr` varchar(5) DEFAULT NULL,
  `phone_code` int(11) NOT NULL,
  `currency_code` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `country_name` (`country_name`),
  FULLTEXT KEY `country_name_2` (`country_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1