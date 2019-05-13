CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(255) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_2` (`department`),
  FULLTEXT KEY `department` (`department`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1