CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payfor_id` int(11) NOT NULL,
  `method_id` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_id`),
  KEY `method_id` (`method_id`),
  KEY `payfor_id` (`payfor_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`method_id`) REFERENCES `payment_methods` (`method_id`),
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payfor_id`) REFERENCES `pay_for` (`payfor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1