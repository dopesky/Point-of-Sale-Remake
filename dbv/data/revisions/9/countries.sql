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









 select 
 `hci-pos`.`products`.`product_id` AS `product_id`,
 `hci-pos`.`products`.`product` AS `product`,
 `hci-pos`.`products`.`category_id` AS `category_id`,
 `hci-pos`.`products`.`cost_per_unit` AS `cost_per_unit`,
 `hci-pos`.`products`.`owner_id` AS `owner_id`,
 `hci-pos`.`products`.`create_date` AS `create_date`,
 `hci-pos`.`products`.`modified_date` AS `modified_date`,
 `hci-pos`.`products`.`active` AS `active`,
 `hci-pos`.`products`.`suspended` AS `suspended`,
 `user_details`.`suspended` AS `owner_suspended`,
 `user_details`.`owner_active` AS `owner_active`,
 `hci-pos`.`categories`.`category_name` AS `category_name`,
 ifnull(`purchases`.`purchase_quantity`,0) as purchase_quantity,
 ifnull(`purchases`.`average_purchase_quantity`,0) as average_purchase_quantity,
 ifnull(`purchases`.`purchase_cost`,0) as purchase_cost,
 ifnull(`purchases`.`average_purchase_cost_per_purchase`,0) as average_purchase_cost_per_purchase,
 ifnull(`purchases`.`discount_received`,0) as discount_received,
 ifnull(`purchases`.`average_discount_received_per_purchase`,0) as average_discount_received_per_purchase,
 ifnull(`sales`.`sale_quantity`,0) as sale_quantity,
ifnull( `sales`.`average_sale_quantity_per_sale`,0) as average_sale_quantity_per_sale,
 ifnull(`sales`.`sale_revenue`,0) as sale_revenue,
 ifnull(`sales`.`average_sale_revenue_per_sale`,0) as average_sale_revenue_per_sale,
 ifnull(`sales`.`discount_allowed`,0) as discount_allowed,
 ifnull(`sales`.`average_discount_allowed_per_sale`,0) as average_discount_allowed_per_sale,
 (ifnull(purchases.purchase_quantity,0) - ifnull(sales.sale_quantity,0)) AS `inventory_level`,
 least(ifnull(`hci-pos`.`products`.`cost_per_unit`,0) , ((ifnull(purchases.purchase_cost,0) + ifnull(purchases.discount_received,0)) / ifnull(purchases.purchase_quantity,1))) AS `inventory_cost`,
 (
 	(	(ifnull(sales.sale_quantity,0) * 
 		least(ifnull(`hci-pos`.`products`.`cost_per_unit`,0) , ((ifnull(purchases.purchase_cost,0) + ifnull(purchases.discount_received,0)) / ifnull(purchases.purchase_quantity,1))) )) / 

 	greatest(
 		(
 			(
 				(ifnull(purchases.purchase_quantity,0) - ifnull(sales.sale_quantity,0)) * 
 				least(ifnull(`hci-pos`.`products`.`cost_per_unit`,0) , ((ifnull(purchases.purchase_cost,0) + ifnull(purchases.discount_received,0)) / ifnull(purchases.purchase_quantity,1)))
 			) / 2
 		), 1)
 	
 ) AS `inventory_turn_over` 
 from `hci-pos`.`products` left join purchase_summary as purchases on purchases.product_id = products.product_id
 left join sale_summary as sales on sales.product_id = products.product_id
 left join `hci-pos`.`user_details` on 
 `user_details`.`id_owner` = `hci-pos`.`products`.`owner_id`
 left join `hci-pos`.`categories` on
 `hci-pos`.`products`.`category_id` = `hci-pos`.`categories`.`category_id`
 group by `hci-pos`.`products`.`product_id` order by `hci-pos`.`products`.`product_id`