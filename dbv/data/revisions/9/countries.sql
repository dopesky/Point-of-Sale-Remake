ALTER TABLE `tbl_users` ADD `country_id` INT NOT NULL DEFAULT '110' AFTER `show_deleted`;
ALTER TABLE `tbl_users` ADD FOREIGN KEY (`country_id`) REFERENCES `countries`(`country_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `products` DROP INDEX `product`;
ALTER TABLE `purchases` DROP FOREIGN KEY `purchases_ibfk_1`;
ALTER TABLE `purchases` DROP INDEX `Product`;
ALTER TABLE `purchases` CHANGE `Purchase-id` `purchase_id` BIGINT NOT NULL AUTO_INCREMENT, CHANGE `Product` `product_id` INT NOT NULL, CHANGE `Amount` `quantity` INT(30) NOT NULL, CHANGE `T-cost` `total_cost` INT(30) NOT NULL, CHANGE `Date-time` `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `purchases` ADD `modified_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `create_date`, ADD `suspended` BOOLEAN NOT NULL DEFAULT FALSE AFTER `modified_date`;
ALTER TABLE `purchases` ADD `active` BOOLEAN NOT NULL DEFAULT TRUE AFTER `modified_date`;
ALTER TABLE `purchases` ADD `recorded_by` BIGINT(12) NOT NULL AFTER `total_cost`, ADD `modified_by` BIGINT(12) NOT NULL AFTER `recorded_by`;
ALTER TABLE `purchases` ADD FOREIGN KEY (`modified_by`) REFERENCES `tbl_users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `purchases` ADD FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `purchases` ADD FOREIGN KEY (`recorded_by`) REFERENCES `tbl_users`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
INSERT INTO `departments` (`department_id`, `department`, `modified_date`, `create_date`, `suspended`) VALUES (NULL, 'stock manager', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0');
ALTER TABLE `products` ADD UNIQUE( `product`, `owner_id`);
ALTER TABLE `purchases` ADD `discount` INT NOT NULL DEFAULT '0' AFTER `total_cost`;
INSERT INTO `payment_methods` (`method_id`, `method`, `create_date`, `modified_date`, `suspended`) VALUES (1, 'cash', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0'), (2, 'm-pesa', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0');
INSERT INTO `pay_for` (`payfor_id`, `pay_for`, `create_date`, `modified_date`, `suspended`) VALUES (1, 'sale', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0'), (2, 'purchase', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0');
ALTER TABLE `purchases` ADD `payment_id` INT NOT NULL AFTER `product_id`;
ALTER TABLE `purchases` ADD FOREIGN KEY (`payment_id`) REFERENCES `payments`(`payment_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `sales` DROP FOREIGN KEY `sales_ibfk_1`;
ALTER TABLE `sales` DROP INDEX `Product`;
ALTER TABLE `sales` CHANGE `Sale-id` `sale_id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `Product` `product_id` INT NOT NULL, CHANGE `Amount` `payment_id` INT NULL DEFAULT NULL, CHANGE `T-cost` `quantity` INT(30) NOT NULL, CHANGE `Date-time` `cost_per_item` INT NOT NULL;
ALTER TABLE `sales` ADD `discount` INT NOT NULL AFTER `cost_per_item`, ADD `recorded_by` BIGINT NOT NULL AFTER `discount`, ADD `modified_by` BIGINT NOT NULL AFTER `recorded_by`, ADD `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified_by`, ADD `modified_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `create_date`, ADD `active` BOOLEAN NOT NULL DEFAULT TRUE AFTER `modified_date`;
ALTER TABLE `sales` ADD `suspended` BOOLEAN NOT NULL DEFAULT FALSE AFTER `active`;
ALTER TABLE `payments` DROP `amount`;
ALTER TABLE `purchases` ADD UNIQUE(`payment_id`);