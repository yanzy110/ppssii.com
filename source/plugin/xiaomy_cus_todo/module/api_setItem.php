<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$param = getParam(['itemid', 'content']);
if ($param === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$newData = [ 'content'	=>	$param['content'] ];
	$condition = [ 'itemid'	=>	$param['itemid'] ];
	$itemData = $Todo->getItem($param['itemid']);
	if (!$itemData) {
		$Todo->code = 503;
		$Todo->msg 	= '没有这个item';
	}else{
		if ($param['content'] == $itemData['content']) {
			$Todo->msg = '修改内容和原内容一致';
		}else{
			$Todo->setItem($newData, $condition);
		}
		$Todo->data = $Todo->getItem($param['itemid']);
	}
}