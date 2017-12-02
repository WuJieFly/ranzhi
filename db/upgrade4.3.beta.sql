ALTER TABLE `sys_block` ADD `height` smallint unsigned NOT NULL AFTER `grid`;
ALTER TABLE `oa_refund` ADD `dept` mediumint(8) unsigned NOT NULL AFTER `parent`;
INSERT INTO `sys_cron` (`m`, `h`, `dom`, `mon`, `dow`, `command`, `remark`, `type`, `buildin`, `status`, `lastTime`) VALUES
('1', '7', '*', '*', '*', 'appName=sys&moduleName=report&methodName=remind', '每日提醒', 'ranzhi', 1, 'normal', '0000-00-00 00:00:00');
