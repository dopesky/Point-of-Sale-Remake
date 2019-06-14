CREATE TABLE `pay_for` (
  `payfor_id` int(11) NOT NULL AUTO_INCREMENT,
  `pay_for` varchar(30) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payfor_id`),
  UNIQUE KEY `pay_for` (`pay_for`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1