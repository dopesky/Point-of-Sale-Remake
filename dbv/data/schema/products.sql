CREATE TABLE `products` (
  `Product-id` int(11) NOT NULL AUTO_INCREMENT,
  `Product-name` varchar(30) NOT NULL,
  `Category-id` int(30) NOT NULL,
  `Weight` varchar(30) NOT NULL,
  `Amount` int(30) NOT NULL,
  `Cost-per-unit` int(30) NOT NULL,
  PRIMARY KEY (`Product-id`),
  KEY `Category-id` (`Category-id`),
  KEY `Product-name` (`Product-name`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`Category-id`) REFERENCES `categories` (`Category-id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1