CREATE TABLE `sales` (
  `Sale-id` int(11) NOT NULL AUTO_INCREMENT,
  `Product` varchar(30) NOT NULL,
  `Amount` int(30) NOT NULL,
  `T-cost` int(30) NOT NULL,
  `Date-time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Sale-id`),
  KEY `Product` (`Product`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`Product`) REFERENCES `products` (`Product-name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1