<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//$uid = getParam('uid', false);

$uid = C::t('#xiaomy_cus_todo#jnpar_add')->getuid();
if ($uid === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$Todo->data = $Todo->getImageAll(['uid'=>$uid]);
}