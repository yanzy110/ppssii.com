<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * 获取传递的参数
 */
function getParam($key, $default=false, $requesttype='post'){
	$input = $requesttype=='get'?$_GET:$_POST;
	if (is_array($key)) {
		$param = [];
		foreach ($key as $k) {
			if (empty($input[$k]) and $input[$k] != 0) {
				return false;
			}else{
				$param[$k] = $input[$k];
			}
		}
		return $param;
	}else{
		return isset($input[$key])?$input[$key]:$default;
	}
}