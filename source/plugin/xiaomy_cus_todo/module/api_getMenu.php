<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$uid = getParam('uid', false);
if ($uid === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$Todo->data = $Todo->getMenuAll(['uid'=>$uid]);
}