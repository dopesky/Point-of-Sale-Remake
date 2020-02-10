ALTER TABLE `apikeys` DROP FOREIGN KEY `apikeys_ibfk_1`;
ALTER TABLE `apikeys` DROP FOREIGN KEY `apikeys_ibfk_2`;
ALTER TABLE `apikeypowers` CHANGE `apikeypower_id` `apikeypower_id` BIGINT NOT NULL AUTO_INCREMENT;
ALTER TABLE `apikey_owners` CHANGE `owner_id` `owner_id` BIGINT NOT NULL AUTO_INCREMENT;
ALTER TABLE `apikeys` CHANGE `owner_id` `owner_id` BIGINT NOT NULL, CHANGE `apikeypower_id` `apikeypower_id` BIGINT NOT NULL;
ALTER TABLE `apikeys` ADD CONSTRAINT `apikeys_ibfk_1` FOREIGN KEY (`apikeypower_id`) REFERENCES `apikeypowers`(`apikeypower_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `apikeys` ADD CONSTRAINT `apikeys_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `apikey_owners`(`owner_id`) ON DELETE CASCADE ON UPDATE CASCADE;
