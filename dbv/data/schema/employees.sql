CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `owner_id` INT NOT NULL,
  `user_id` bigint(12) NOT NULL,
  `active` BOOLEAN NOT NULL DEFAULT TRUE,
  `suspended` BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  FULLTEXT KEY `first_name` (`first_name`),
  FULLTEXT KEY `last_name` (`last_name`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`),
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  FOREIGN KEY (`owner_id`) REFERENCES `owner`(`owner_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1
