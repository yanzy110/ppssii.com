<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$param = getParam(['itemid', 'mid']);
if ($param === false) {
	$Todo->code = 503;
	$Todo->msg 	= '参数错误';
}else{
	$newData = [ 'mid'	=>	$param['mid'] ];
	$condition = [ 'itemid'	=>	$param['itemid'] ];
	$itemData = $Todo->getItem($param['itemid']);
	if (!$itemData) {
		$Todo->code = 503;
		$Todo->msg 	= '没有这个item';
	}else{
		if ($param['mid'] == $itemData['mid']) {
			$this->msg = '当前item已经在mid='.$param['mid'].'的菜单中';
		}else{
			$Todo->setItem($newData, $condition);
		}
		$Todo->data = $Todo->getItem($param['itemid']);
	}
}