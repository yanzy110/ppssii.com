<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include DISCUZ_ROOT.'source/plugin/xiaomy_cus_todo/include/loader.php';
$Todo = new Todo();
if ($Todo->checkMod() === false) {
	$Todo->code = 404;
	$Todo->msg = 'fail';
}else{
	$Todo->checkMember();
	include $Todo->getModFile();
}
$Todo->return();
