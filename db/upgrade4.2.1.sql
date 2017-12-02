ALTER TABLE `oa_attend` ADD `client` varchar(20) NOT NULL AFTER `device`;
ALTER TABLE `sys_file` CHANGE `objectType` `objectType` char(30) NOT NULL;
