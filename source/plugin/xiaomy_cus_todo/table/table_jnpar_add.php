<?php
/*
 *	[jnpar] (C)2018-2023 jnpar技能趴荣耀出品.
 *	这不是一个免费的程序！由QQ：94526868提供技术支持，如需定制或者个性化修改插件，欢迎与我们联系。
 *  技术交流站www.jnpar.com 辅助推广，敬请访问惠临。
 *	$_G['basescript'] = 模块名称
 *	CURMODULE = 为模块自定义常量
 */
 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_jnpar_add extends discuz_table
{
	public function __construct() {
		$this->_table = 'jnpar_add';
		$this->_pk    = '';
		parent::__construct();
	} 
	
	public function getuid(){
		global $_G;
		//dsetcookie('outloginuid', $uid, 3600*24*30);
		//$_GET['uid']=intval($_GET['uid']);
		//$uid = $_GET['uid']?$_GET['uid']:$_G['uid'];
		$uid = $_G['uid'];
		if (!$uid) {
			if (getcookie('outloginuid')) {
				$uid = getcookie('outloginuid');
			}else{
				//$uid = 9999999+rand(100000,9999999);
				$uid = 8378600+rand(1000,9999);
				dsetcookie('outloginuid', $uid, 3600*24*30);
			}
		}
		$_GET['uid']=$uid;
		//debug($uid);
		return $uid;
	}
	
}