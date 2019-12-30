<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$itemId = getParam('itemid', false);
if ($itemId === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$Item = $Todo->getItem($itemId);
	if (!$Item) {
		$Todo->msg 	= 'item不存在';
	}else{
		$Todo->msg = $Todo->deleteItem($itemId)?'删除成功':'删除失败';
	}
}