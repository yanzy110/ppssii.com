<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$param = getParam(['mid', 'pid', 'content', 'uid']);
if ($param === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$itemId = $Todo->addItem($param['mid'], $param['uid'], $param['content'], $param['pid']);
	if ($itemId === false) {
		$Todo->code = 503;
		$Todo->msg = 'insert error';
	}else{
		$Todo->data = $Todo->getItem($itemId);
	}
}