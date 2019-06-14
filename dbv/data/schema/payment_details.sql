CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `payment_details` AS select `payments`.`payment_id` AS `payment_id`,`payments`.`payfor_id` AS `payfor_id`,`payments`.`method_id` AS `method_id`,`payments`.`create_date` AS `create_date`,`payments`.`modified_date` AS `modified_date`,`payments`.`active` AS `active`,`payments`.`suspended` AS `suspended`,`pay_for`.`pay_for` AS `pay_for`,`pay_for`.`suspended` AS `pay_for_suspended`,`payment_methods`.`method` AS `method`,`payment_methods`.`suspended` AS `method_suspended`,(case when (`payments`.`suspended` = 1) then 'Payment Deactivated by Admin' when (`payments`.`active` = 0) then 'Payment Deactivated by StakeHolder' else 'Payment Active' end) AS `status` from ((`payments` join `pay_for` on((`payments`.`payfor_id` = `pay_for`.`payfor_id`))) join `payment_methods` on((`payment_methods`.`method_id` = `payments`.`method_id`)))