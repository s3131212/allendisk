CREATE TABLE IF NOT EXISTS `dir` (`id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`owner` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`parent` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`recycle` int(11) NOT NULL DEFAULT '0',`share` int(11) NOT NULL DEFAULT '0',`color` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `dir` ADD PRIMARY KEY (`id`);
ALTER TABLE `file` CHANGE `date` `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `id` `id` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `file` ADD `recycle` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `file` ADD `share` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `file` ADD `color` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
INSERT INTO `setting` (`name`, `value`) VALUES('updatesec', '2'),('subtitle', '最先進，最大方，最安全的網路硬碟');