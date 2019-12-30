<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
  *  参数说明  mid    int  父节点ID int
  *          orderid int 排序ID
  *
  *  参数说明  mid    int   父节点ID int
  *          orderid int 排序ID
  **/
$orders = getParam('orders');
if ($orders == false or !is_array($orders)) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	foreach ($orders as $key => $item) {
        $Todo->setMenu(['orderid'=>$item['orderid']], ['id'=>$item['mid']]);
	}
}