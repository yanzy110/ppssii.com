<?php 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class Todo
{
	// 默认返回的状态码
	public $code = 200;
	// 默认返回的说明信息
	public $msg = 'success';
	// 默认返回的数据
	public $data = false;
	// 允许访问的接口
	public $mods = ['setMidOrders','catalog','getCatyitem', 'addItem', 'setItem', 'deleteItem', 'mvItem', 'getMenu', 'addMenu', 'deleteMenu', 'uploadImages', 'getImages', 'deleteImages', 'setItemOrders', 'setMenu'];

	// 默认的接口文件
	public $modFile = '';
	// 默认的图片上传名称
	public $imageName = 'fileimg';
	// 允许上传的图片类型
	public $imageType = ['image/jpeg', 'image/png', 'image/gif'];
	// 图片上传保存路径
	public $imagePath = DISCUZ_ROOT.'source/plugin/xiaomy_cus_todo/upload/';
	// discuz全局表里
	public $_G;

	// 初始化操作
	function __construct()
	{
		global $_G;
		$this->_G = $_G;
	}

	/**
	 * 判断参数中是否存在uid, 如果不存在则创建一个临时uid
	 */
	public function checkMember(){
		$uid = $_GET['uid'];
		if (!$uid) {
			if (getcookie('outloginuid')) {
				$uid = getcookie('outloginuid');
			}else{
				$uid = 9999999+rand(1000,9999);
				setcookie('outloginuid', $uid, 3600*24*30);
			}
		}
		$_GET['uid'] = $uid;
	}

	/**
	 * [return 数据返回函数]
	 * @return [type] [输出json字符串]
	 */
	public function return(){
		$return = [
			'code'	=>	$this->code,
			'msg'	=>	$this->msg,
		];
		// 如果返回数据不为false则添加数据到返回数组
		if($this->data !== false and is_array($this->data)){
			$return['data'] = $this->data;
		}
		header("Access-Control-Allow-Origin: *");   //全域名
		header("Access-Control-Allow-Credentials: true");   //是否可以携带cookie
		header("Access-Control-Allow-Methods: POST,GET,PUT,OPTIONS,DELETE");   //允许请求方式
		header("Access-Control-Allow-Headers: X-Custom-Header");   //允许请求字段，由客户端决定
		header("Content-Type: application/json; charset=utf-8 "); //返回数据类型（ text/html; charset=utf-8、 application/json; charset=utf-8 )
		echo json_encode($return);exit;
	}

	/**
	 * [checkMod 检查api是否为合法请求]
	 * @return [type] [检查结果]
	 */
	public function checkMod(){
		$mod = in_array($_GET['mod'], $this->mods)?$_GET['mod']:'null';
		$this->modFile = DISCUZ_ROOT.'source/plugin/xiaomy_cus_todo/module/api_'.$mod.'.php';
		return ($mod == 'null' or !file_exists($this->modFile))?false:true;
	}

	/**
	 * [getModFile 获取api接口文件]
	 * @return [type] [文件地址]
	 */
	public function getModFile(){
		return $this->modFile?$this->modFile:false;
	}

	/**
	 * [checkImageFile 判断文件是否上传至服务器成功]
	 * @return [type] [判断结果]
	 */
	public function checkImageFile(){
		return $_FILES[$this->imageName]['size']?true:false;
	}

	/**
	 * [upload 上传图片文件]
	 * @return [type] [图片文件地址]
	 */
	public function upload(){
		$imageFile = $_FILES[$this->imageName];
		if (in_array($imageFile['type'], $this->imageType) === false) {
			return false;
		}
		list($imageName, $imageType) = explode('.', $imageFile['name']);
		$file_name = md5($imageFile['name'].time()).'.'.$imageType;
		$imageSave = $this->imagePath.date('Y/m/d/');
		$imageSource = $imageFile['tmp_name'];
		$this->mkdirs($imageSave);
		$imageSave .= $file_name;
		if (move_uploaded_file($imageSource, $imageSave)) {
			unlink($imageSource);
			global $_G;
			return $_G['siteurl'].str_replace(DISCUZ_ROOT, '', $imageSave);
		}
		return false;
	}

	// [mkdirs 创建文件夹]
	public function mkdirs($dir, $mode = 0777)
	{
	    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
	    if (!$this->mkdirs(dirname($dir), $mode)) return FALSE;
	    return @mkdir($dir, $mode);
	}

	/**
	 * [addMenu 添加菜单]
	 * @param [type] $uid     [用户id]
	 * @param [type] $name    [菜单名称]
	 * @param [type] $orderid [订单id]
	 * @param [type] $pid     [父id]
	 */
	public function addMenu($uid, $name, $orderid, $pid){
		$insert = [
			'uid'		=>	$uid,
			'username'	=>	$this->getUserName($uid),
			'orderid'	=>	$orderid,
			'name'		=>	$name,
			'pid'		=>	$pid?$pid:0
		];
		$mid = DB::insert('xiaomy_cus_todo_menu', $insert, true);
		return $mid?$mid:false;
	}

	/**
	 * [getUserName 获取用户名]
	 * @param  [type] $uid [用户id]
	 * @return [type]      [用户名]
	 */
	public function getUserName($uid){
		$userName = DB::result_first('select username from %t where uid = %d', array('common_member', $uid));
		return $userName?$userName:'null';
	}

	/**
	 * [getMenu 获取菜单]
	 * @param  [type] $mid [菜单id]
	 * @return [type]      [菜单数据]
	 */
	public function getMenu($mid){
		return DB::fetch_first('select * from %t where id = %d', array('xiaomy_cus_todo_menu', $mid));
	}

	/**
	 * [getMenuAll 获取所有的菜单数据]
	 * @param  [type] $field [获取条件]
	 * @return [type]        [菜单数据]
	 */
	public function getMenuAll($field){
		return DB::fetch_all('select * from %t'.$this->getCondition($field) .'order by orderid asc', array('xiaomy_cus_todo_menu'));
	}

	/**
	 * [deleteMenu 删除菜单]
	 * @param  [type] $mid [菜单id]
	 * @return [type]      [删除结果]
	 */
	public function deleteMenu($mid){
		$condition = is_array($mid)?$mid:['id'=>$mid];
		return DB::delete('xiaomy_cus_todo_menu', $condition);
	}

	/**
	 * [addItem 添加子项目]
	 * @param [type] $mid     [菜单id]
	 * @param [type] $uid     [用户id]
	 * @param [type] $content [内容]
	 * @param [type] $pid     [父级id]
	 */
	public function addItem($mid, $uid, $content, $pid){
		$zindex = DB::result_first('select count(*) from pre_xiaomy_cus_todo_item where mid=%d and uid=%d',array($mid,$uid));
		$zindex += 1;
		$insert = [
			'mid'		=>	$mid,
			'uid'		=>	$uid,
			'username'	=>	$this->getUserName($uid),
			'content'	=>	$content,
			'pid'		=>	$pid?$pid:0,
			'dateline'	=>	time(),
			'zindex'	=>	$zindex
		];
		$itemId = DB::insert('xiaomy_cus_todo_item', $insert, true);
		return $itemId?$itemId:false;
	}

	/**
	 * [getItem 获取子项目]
	 * @param  [type] $itemId [子项目id]
	 * @return [type]         [子项目数据]
	 */
	public function getItem($itemId){
		$item = DB::fetch_first('select * from %t where id = %d', array('xiaomy_cus_todo_item', $itemId));
		if ($item) {
			$item['itemid'] = $item['id'];
		}
		return $item;
	}

	/**
	 * [getItemAll 获取所有的子项目数据]
	 * @param  [type] $field [获取条件]
	 * @return [type]        [子项目数据]
	 */
	public function getItemAll($field){
		$items = DB::fetch_all('select * from %t'.$this->getCondition($field).' order by zindex asc', array('xiaomy_cus_todo_item'));
		foreach ($items as $key => $item) {
			if ($item) {
				$items[$key]['itemid'] = $item['id'];
			}
		}
		return $items;
	}

	/**
	 * [setItem 修改子项目数据]
	 * @param [type] $data      [修改内容]
	 * @param [type] $condition [修改条件]
	 */
	public function setItem($data, $condition){
		if ($condition['itemid']) {
			$condition['id'] = $condition['itemid'];
			unset($condition['itemid']);
		}
		return DB::update('xiaomy_cus_todo_item', $data, $condition);
	}

    /**
     * [setItem 修改父项目数据]
     * @param [type] $data      [修改内容]
     * @param [type] $condition [修改条件]
     */
    public function setMenu($data, $condition){
        return DB::update('xiaomy_cus_todo_menu', $data, $condition);
    }

	/**
	 * [deleteItem 删除子项目]
	 * @param  [type] $itemId [子项目id]
	 * @return [type]         [删除结果]
	 */
	public function deleteItem($itemId){
		$condition = is_array($itemId)?$itemId:['id'=>$itemId];
		return DB::delete('xiaomy_cus_todo_item', $condition);
	}

	/**
	 * [addImage 添加图片数据]
	 * @param [type] $uid        [用户id]
	 * @param [type] $attachment [图片路径]
	 */
	public function addImage($uid, $attachment){
		$imageData = [
			'uid'		=>	$uid,
			'username'	=>	$this->getUserName($uid),
			'attachment'=>	$attachment,
			'dateline'	=>	time()
		];
		$imageId = DB::insert('xiaomy_cus_todo_image', $imageData, true);
		return $imageId?$imageId:false;
	}

	/**
	 * [getImages 获取图片数据]
	 * @param  [type] $imageID [图片id]
	 * @return [type]          [图片数据]
	 */
	public function getImages($imageID){
		return DB::fetch_first('select * from %t where id = %d', array('xiaomy_cus_todo_image', $imageID));
	}

	/**
	 * [getImageAll 获取所有的图片数据]
	 * @param  [type] $field [获取条件]
	 * @return [type]        [图片数据]
	 */
	public function getImageAll($field){
		return DB::fetch_all('select * from %t'.$this->getCondition($field), array('xiaomy_cus_todo_image'));
	}

	/**
	 * [deleteImage 删除图片]
	 * @param  [type] $imageId [图片id]
	 * @return [type]          [删除结果]
	 */
	public function deleteImage($imageId){
		$condition = is_array($imageId)?$imageId:['id'=>$imageId];
		return DB::delete('xiaomy_cus_todo_image', $condition);
	}

	/**
	 * [getCondition 将条件数据转化为字符串]
	 * @param  [type] $field [条件数据]
	 * @return [type]        [条件字符串]
	 */
	public function getCondition($field){
		if (is_array($field)) {
			$condition = '';
			foreach ($field as $f => $v) {
				$item = "{$f}='{$v}'";
				$append = $condition?' and ':'';
				$condition .= $append.$item;
			}
		}else{
			$condition = $field;
		}
		return ' where '.$condition;
	}


}
















