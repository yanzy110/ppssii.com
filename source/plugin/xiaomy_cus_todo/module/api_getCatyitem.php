<?php


//判断常量是否存在 不然停止访问
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//获取由post传过来的 mid 参数 即 菜单ID
$mid = getParam('mid', false);
if ($mid === false) {  //若参数没传 或者其他原因 返回如下内容
	$Todo->code = 503;
	$Todo->msg 	= 'system error';
}else{                //若有参数将查询 数据库 xiaomy_cus_todo_menu 表 返回该数据库的一条内容
	$menuData = $Todo->getMenu($mid);
	$menuData['item'] = $Todo->getItemAll(['mid'=>$mid]); //接着获取 数据库 pre_xiaomy_cus_todo_item 表中 mid=1 所有内容
	$Todo->data = $menuData;
}                                                       //最后由框架加载后续内容输出到页面