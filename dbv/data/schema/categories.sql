CREATE TABLE `categories` (
  `Category-id` int(30) NOT NULL AUTO_INCREMENT,
  `Category-name` varchar(30) NOT NULL,
  PRIMARY KEY (`Category-id`),
  KEY `Category-id` (`Category-id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1