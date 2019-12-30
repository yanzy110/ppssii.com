<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$imageId = getParam('imageid', false);
if ($imageId === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$Item = $Todo->getImages($imageId);
	if (!$Item) {
		$Todo->msg 	= '图片不存在';
	}else{
		$Todo->msg = $Todo->deleteImage($imageId)?'删除成功':'删除失败';
	}
}