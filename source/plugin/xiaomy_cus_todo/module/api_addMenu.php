<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$param = getParam(['uid', 'pid', 'name', 'orderid']);
if ($param === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$mid = $Todo->addMenu($param['uid'], $param['name'], $param['orderid'], $param['pid']);
	if ($mid === false) {
		$Todo->code = 503;
		$Todo->msg = 'insert error';
	}else{
		$Todo->data = $Todo->getMenu($mid);
	}
}