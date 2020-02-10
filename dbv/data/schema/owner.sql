CREATE TABLE `owner` (
  `owner_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `company` VARCHAR(100) NOT NULL,
  `profile_photo` VARCHAR(255) NOT NULL,
  `active` BOOLEAN NOT NULL DEFAULT TRUE,
  `user_id` BIGINT(12) NOT NULL,
  PRIMARY KEY (`owner_id`),
  FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  UNIQUE (`user_id`),
  FULLTEXT (`first_name`),
  FULLTEXT (`last_name`),
  UNIQUE (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
