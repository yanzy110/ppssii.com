<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
  *  mid  、             父节点
  *  itemid、            子节点
  *  is_catalog 、       1.把文件变成目录 是在父节点新增2.把文件拖拽到目录下3.把目录拖拽到目录下
 *   drag_file          true 拖拽的是目录 false 拖拽的是文件
  *
  **/
//参数模拟 3

//$_POST['mid'] = 3;
//$_POST['itemid']=2;
//$_POST['is_catalog'] =3;
//$_POST['drag_file'] ='true';

//参数模拟 1
//$_POST['mid'] = 1;
//$_POST['itemid']=4;
//$_POST['is_catalog'] =  1;
//$_POST['drag_file'] ='false';


//参数模拟 2
//$_POST['mid'] = 60;
//$_POST['itemid']=5;
//$_POST['is_catalog'] =  2;
//$_POST['drag_file'] ='true';



$param = getParam(['itemid', 'mid','drag_file','is_catalog']);

if ($param === false) {
	$Todo->code = 503;
	$Todo->msg 	= '参数错误';
}else{
    $res = $Todo->getMenu($param['mid']);
    if(empty($res)){  //父节点不存在
        $Todo->code = 503;
        $Todo->msg 	= '父节点不存在';
    }else{
		$content = ''; // 子节点名称
    if($param['drag_file'] == 'true'){   //如果是目录
        $itemData = $Todo->getMenu($param['itemid']);
        if(empty($itemData)){
            $Todo->code = 503;
            $Todo->msg 	= '子节点目录不存在';
        }
        $content = $itemData['name'];
    }else if($param['drag_file'] == 'false'){
        $itemData = $Todo->getItem($param['itemid']);
        if(empty($itemData)){
            $Todo->code = 503;
            $Todo->msg 	= '子节点目录不存在';
        }
        $content = $itemData['content'];
    }


    if($param['is_catalog'] == '1'){       //1.把文件变成目录 是在父节点新增
        $Todo->addMenu(1,$content,0,$param['mid']);
        // 删除原文件
        $Todo->deleteItem($param['itemid']);
        $Todo->data = $Todo->getMenu($param['mid']);
    }else if ($param['is_catalog'] == '2'){ //2.把文件拖拽到目录下
        $condition = [ 'itemid'	=>	$param['itemid'] ];
        $newData = [ 'mid'	=>	$param['mid'] ];
        $Todo->setItem($newData, $condition);
        $Todo->data = $Todo->getMenu($param['mid']);
    }else if($param['is_catalog'] == '3'){ //3.把目录拖拽到目录下
        $condition['id'] = $param['itemid'];
        $newData['pid'] = $param['mid'];
        $Todo->setMenu($newData, $condition);
        $Todo->data = $Todo->getMenu($param['mid']);
    }else{
        $Todo->code = 503;
        $Todo->msg 	= 'is_catalog 参数错误';
    }
}

    








}