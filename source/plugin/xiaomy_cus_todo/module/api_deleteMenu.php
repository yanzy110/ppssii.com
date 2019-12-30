<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$mid = getParam('mid', false);
if ($mid === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$menu = $Todo->getMenu($mid);
	if (!$menu) {
		$Todo->msg 	= '分类不存在';
	}else{
		$Todo->msg = $Todo->deleteMenu($mid)?'删除成功':'删除失败';
	}
}