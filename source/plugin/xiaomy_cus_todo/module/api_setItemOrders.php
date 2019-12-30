<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$orders = getParam('orders');
if ($orders == false or !is_array($orders)) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	foreach ($orders as $key => $item) {
		$todo->setItem(['zindex'=>$item['index']], ['id'=>$item['itemid']]);
	}
}