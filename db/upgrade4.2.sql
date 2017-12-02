ALTER TABLE `sys_action` ADD INDEX `objectType` (`objectType`);
ALTER TABLE `sys_action` ADD INDEX `objectID` (`objectID`);

ALTER TABLE `crm_contact` ADD INDEX `email` (`email`);
ALTER TABLE `crm_contact` ADD INDEX `qq` (`qq`);
ALTER TABLE `crm_contact` ADD INDEX `mobile` (`mobile`);
ALTER TABLE `crm_contact` ADD INDEX `phone` (`phone`);
