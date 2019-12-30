<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 设置菜单名称
 * 传递的api参数为 菜单id:mid 菜单名称:name
 */

$param = getParam(['mid', 'name']);
if ($param === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$newData = [ 'name'	=>	$param['name'] ];
	$condition = [ 'mid'	=>	$param['mid'] ];
	$itemData = $Todo->getMenu($param['mid']);
	if (!$itemData) {
		$Todo->code = 503;
		$Todo->msg 	= '没有这个菜单';
	}else{
		if ($param['name'] == $itemData['name']) {
			$Todo->msg = '修改内容和原内容一致';
		}else{
			$Todo->setMenu($newData, $condition);
		}
		$Todo->data = $Todo->getMenu($param['mid']);
	}
}