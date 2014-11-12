ALTER TABLE `file` ADD `recycle` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `file` ADD `share` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `file` ADD `color` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `dir` ADD `recycle` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `dir` ADD `share` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `dir` ADD `color` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
INSERT INTO `setting` (`name`, `value`) VALUES('updatesec', '2'),('subtitle', '最先進，最大方，最安全的網路硬碟');