CREATE TABLE `apiactivitylogs` (
  `activitylog_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`activitylog_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `apiactivitylogs_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `apikey_owners` (`owner_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1