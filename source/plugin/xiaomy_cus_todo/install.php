<?php
/**
 *	[todo定制版本(xiaomy_cus_todo.install)] (C)2019-2099 Powered by 窝窝开发者.
 *	Version: 1.0.0
 *	Date: 2019-10-12 20:46
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS pre_xiaomy_cus_todo_menu;
CREATE TABLE IF NOT EXISTS  `pre_xiaomy_cus_todo_menu` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`pid` int(10) NOT NULL DEFAULT '0',
	`uid` mediumint(8)  NOT NULL,
	`username` VARCHAR(30) NOT NULL DEFAULT '',
	`name` varchar(100) NOT NULL,
	`orderid` int(10) DEFAULT '0',
PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS pre_xiaomy_cus_todo_item;
CREATE TABLE IF NOT EXISTS  `pre_xiaomy_cus_todo_item` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`pid` int(10) NOT NULL DEFAULT '0',
	`mid` int(10) NOT NULL DEFAULT '0',
	`uid` mediumint(8)  NOT NULL,
	`username` VARCHAR(30) NOT NULL DEFAULT '',
	`zindex` int(10)  NOT NULL,
	`content` text NOT NULL,
    `dateline` int(10) DEFAULT NULL,
PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS pre_xiaomy_cus_todo_image;
CREATE TABLE IF NOT EXISTS  `pre_xiaomy_cus_todo_image` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`itemimd` int(10) NOT NULL DEFAULT '0',
	`uid` mediumint(8)  NOT NULL,
	`username` VARCHAR(30) NOT NULL DEFAULT '',
	`attachment` VARCHAR(255) NOT NULL,
    `dateline` int(10) DEFAULT NULL,
PRIMARY KEY (`id`)
)ENGINE=MyISAM;
EOF;

runquery($sql);
$finish = true;
?>