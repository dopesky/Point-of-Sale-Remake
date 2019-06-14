CREATE TABLE `payment_methods` (
  `method_id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(30) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `suspended` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1