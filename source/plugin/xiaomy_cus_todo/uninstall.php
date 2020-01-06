<?php
/**
 *	[todo定制版本(xiaomy_cus_todo.uninstall)] (C)2019-2099 Powered by 窝窝开发者.
 *	Version: 1.0.0
 *	Date: 2019-10-12 20:46
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS `pre_xiaomy_cus_todo_menu`;
DROP TABLE IF EXISTS `pre_xiaomy_cus_todo_item`;
DROP TABLE IF EXISTS `pre_xiaomy_cus_todo_image`;

EOF;

runquery($sql);
$finish = true;
?>