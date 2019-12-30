<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$uid = getParam('uid', false);
if ($param === false or $Todo->checkImageFile() === false) {
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{
	$attachment = $Todo->upload();
	if ($attachment === false) {
		$Todo->code = 504;
		$Todo->msg 	= 'save image error';
	}else{
		$imageId = $Todo->addImage($uid, $attachment);
		if (!$imageId) {
			$Todo->code = 505;
			$Todo->msg 	= 'save data error';
		}
		$Todo->data = $Todo->getImages($imageId);
	}
}