ALTER TABLE `employees` ADD `owner_id` INT NOT NULL AFTER `profile_photo`;
ALTER TABLE `employees` ADD FOREIGN KEY (`owner_id`) REFERENCES `owner`(`owner_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `employees` ADD `active` BOOLEAN NOT NULL DEFAULT '1' AFTER `user_id`;