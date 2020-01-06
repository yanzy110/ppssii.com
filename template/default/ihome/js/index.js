// 树的基本配置
var setting = {
    edit: {
        enable: true,
         showRemoveBtn: showRemoveBtn,
        showRenameBtn: showRenameBtn
    },
    data: {
        simpleData: {
            enable: true
        }
    },
    callback: {
        beforeDrag: beforeDrag,
        beforeDrop: beforeDrop,
        beforeRename: beforeRename, //节点编辑
        beforeRemove:beforeRemove,//节点删除
        onClick: zTreeOnClick // 节点点击
    }
};

var _fromTreeId,_toTreeId;


function showRemoveBtn(treeId,treeNode){
    return treeNode.id != "root";
}
function showRenameBtn(treeId,treeNode){
    return treeNode.id != "root";
}


function beforeDrag(treeId, treeNodes) {
    // 拖出	
    for (var i = 0, l = treeNodes.length; i < l; i++) {
        if (treeNodes[i].drag === false) {
            return false;
        }
    }
    _fromTreeId = treeId;
    return true;
}

function beforeDrop(treeId, treeNodes, targetNode, moveType) {
	_toTreeId = treeId;
    // 接收
    if (_fromTreeId == "bodyTree"  && _toTreeId == "rubbishTree") {        
        // 删除父节点节点
        return Tree.delMenu(treeNodes[0]);
    }

    if (_fromTreeId == "childTree"  && _toTreeId == "rubbishTree") {    	
        // 删除子节点
        return Tree.delItem(treeNodes[0]);
    }

    // 规则1：从内容树拖拽到 todo 不允许
    if(_fromTreeId == "bodyTree" && _toTreeId == "childTree"){
    	alert("不允许这样拖拽");
    	return false;
    }

    // 规则2：从 todo 拖拽到 body  则设归宿问题
    if(_fromTreeId == "childTree" && _toTreeId == "bodyTree"){
    	 //console.log(treeId, treeNodes, targetNode, moveType);
    	return Tree.addNode(treeNodes[0],targetNode);
    }

    // 规则3： 在body树内进行拖拽和排序的处理
    if(_fromTreeId == "bodyTree" && _toTreeId == "bodyTree"){
    	//console.log(treeId, treeNodes, targetNode, moveType);
    	return Tree.sortNode(treeNodes[0],targetNode,moveType);
    }

    
    return targetNode ? targetNode.drop !== false : true;
}

// 节点编辑
function beforeRename(treeId, treeNode, newName, isCancel) {    
    if (newName.length == 0) {
        setTimeout(function() {
            var zTree = $.fn.zTree.getZTreeObj(treeId);
            zTree.cancelEditName();
            alert("节点名称不能为空");
        }, 0);
        return false;
    }
    return Tree.editNode(treeNode,newName);
}

// 节点删除
function beforeRemove(treeId, treeNode, newName, isCancel) { 
    if(treeId == "bodyTree"){
        // 删除目录
       return Tree.delMenu(treeNode);
    }else if(treeId == "childTree"){
        // 删除子节点
        return Tree.delItem(treeNode);
    }
    return false;
}

// 节点点击
function zTreeOnClick(event,treeId,treeNode){
    if(treeId == "bodyTree"){
        // 点击的是菜单树
        Tree._mid = treeNode.id;
        // 获取旗下子节点
        Tree.getChildNodes();
    }
}

var Tree = {
    _uid:0, // 启用uid
    _mid: undefined,
    _childTree: null,
    _bodyTree: null,
    _rubbishTree: null,
    init: function() {
        this.addEvents();
        this.initZtree();

        this.getMenuTree();
    },
    addEvents: function() {
        // 添加右侧树节点
        $(".btn-add").click(function() {
            this.addChildNode();
        }.bind(this));  

        // 切换类型
        $("input[name='menu']").click(function(){
            if(this.value == 1){
                // 目录
                $(".btn-add").text("添加菜单");
            }else{
                $(".btn-add").text("添加内容");
            }
        });    
    },
    initZtree: function() {
        this._bodyTree = $.fn.zTree.init($("#bodyTree"), setting);
        this._childTree = $.fn.zTree.init($("#childTree"), setting);
        this._rubbishTree = $.fn.zTree.init($("#rubbishTree"), setting);
    },
   
     /** * 获取左侧目录树
     */
    getMenuTree: function() {
        var _inThis = this;

        // 获取目录列表
        Common.Base.loadJson({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=getMenu',
            type: 'post',
            data: {
                uid: this._uid
            }
        }, function(json) {
            if (json.code == 200) {
                var _zNodes = [];
		    
                _data = json.data || [];
                // 加入一个虚拟的节点
                _data = [{name:"我的电脑",id:"root"}].concat(_data);

                _data.forEach(function(item) {
                    _zNodes.push({
			    
			    
			    
                        name: item.name,
                        id: item.id,
                        pId: item.pid
                    });
                });

                // 清空树
                var nodes = _inThis._bodyTree.getNodes();
                while(nodes != undefined && nodes.length > 0){
	                nodes.forEach(function(node){
	                	_inThis._bodyTree.removeNode(node);
	                });
	                nodes = _inThis._bodyTree.getNodes();
	            }

                // 赋值左侧内容树数据
                _inThis._bodyTree.addNodes(null, _zNodes);
                _inThis._bodyTree.expandAll(true);
            }
        });
    },
    /**
     * 添加子节点
     */
    addChildNode: function() {
        var _val = $("#txtNodeName").val().trim();
        if (_val.length == 0) {
            alert('请输入内容');
            return;
        }

        // 是添加菜单还是添加内容
        var _isMenu = $("input[name='menu']:checked").val();

        if(_isMenu == 1){
            var _node = {
                pid:0,
                name:_val
            };

            // 判断是否有选中的节点
           var _selectNodes =  Tree._bodyTree.getSelectedNodes();            
            if(_selectNodes.length > 0 && _selectNodes[0].id != "root"){
                // 传递父节点
                _node.pid = _selectNodes[0].id;
            }


            // 添加菜单
            Tree.addMenu(_node);
            $("#txtNodeName").val("");
            return;
        }

        var _inThis = this;

        Common.Base.loadJson({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=addItem',
            type: 'POST',
            data: {
                mid: _inThis._mid,
                uid:this._uid,
                content: _val
            }
        }, function(json) {
            if (json.code == 200) {
                $("#txtNodeName").val("");
                console.log("内容添加成功");

                // 给右侧树加入一个节点
                _inThis._childTree.addNodes(null, {
                    name: _val,
                    id: json.data.id,
                    pId: json.data.pid
                });

            } else {
                alert(json.msg);
            }
        });
    },
    /**
     * 删除父节点
     */
    delMenu: function(_node) {
        var _inThis = this,
            _result = false;

        Common.Base.loadJsonNoAsync({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=deleteMenu',
            type: 'POST',
            data: {
                uid:this._uid,
                mid: _node.id
            }
        }, function(json) {
            if (json.code == 200) {
                _result = true;
                console.log("父节点删除成功");
            } else {
                alert(json.msg);
            }
        });
        return _result;
    },
    /**
     * 删除子节点
     */
    delItem: function(_node) {
        var _inThis = this,
        	_result = false;

        Common.Base.loadJsonNoAsync({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=deleteItem',
            type: 'POST',
            data: {
                uid:this._uid,
                itemid: _node.id
            }
        }, function(json) {
            if (json.code == 200) {
            	_result = true;
                console.log("子节点删除成功");
            } else {
                alert(json.msg);
            }
        });
        return _result;
    },
    /**
    * 修改节点
    * @param _node:object 编辑的节点信息
    * @param _newName:string 新节点名称
    */
    editNode:function(_node,_newName){
    	var _inThis = this,
    		_result = false;


        if(_node.isParent){
            // 编辑的是父节点
            Common.Base.loadJsonNoAsync({
                url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=setMenu',
                type: 'POST',
                data: {
                    mid: _node.id,
                    name:_newName
                }
            }, function(json) {
                if (json.code == 200) {
                    console.log("父节点编辑成功");
                    _result = true;
                } else {
                    alert(json.msg);
                }
            });

            return _result;            
        }

    	// 如果是回收站内的树节点编辑 则直接返回true
    	if(_node.tId.indexOf("rubbishTree") > -1) return true;

        Common.Base.loadJsonNoAsync({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=setItem',
            type: 'POST',
            data: {
                itemid: _node.id,
                content:_newName
            }
        }, function(json) {
            if (json.code == 200) {
                console.log("子节点编辑成功");
                _result = true;
            } else {
                alert(json.msg);
            }
        });

        return _result;
    },
    /**
    * 添加菜单节点
    */
    addMenu:function(_node){
        var _result = false,
            _inThis = this;


        _node.uid = this._uid;

        // 新增正式节点
        Common.Base.loadJsonNoAsync({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=addMenu',
            type: 'POST',
            data: _node
        }, function(json) {
            if (json.code == 200) {
                _result = true;
                _inThis.getMenuTree();
                console.log("菜单添加成功");
            } else {
                alert(json.msg);
            }
        });
        return _result;
    },
    /**
    * 添加内容子节点
    * @param _node:object 拖拽的节点信息
    * @param _targetNdoe:object 目标节点对象  如果是同级节点则为null
    */
    addNode:function(_node,_targetNode){    
    	var _data = {
    		uid:this._uid,
    		name:_node.name
    	};
    	if(_targetNode){
    		_data.mid = _targetNode.id;
    	}

    	var _inThis = this,
    		_result = false;


        // 这里需要判断两个逻辑        
        if(_targetNode){
            // 逻辑1：_targetNode 非空 则表示拖拽到左侧菜单节点下 作为子节点
            Common.Base.loadJsonNoAsync({
                url:"http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=catalog",
                type:"post",             
                data:{
                    uid:this._uid,
                    mid:_targetNode.id,
                    itemid:_node.id,
                    is_catalog:2, //把文件拖拽到目录下
                    drag_file:false // 拖拽的是子节点
                }
            },function(json){
                if(json.code == 200){
                    _result = false;
                    _inThis.getMenuTree();
                    console.log("调整子节点归属成功");
                }else{
                    alert(json.msg);
                }
            });
        }        
        else{
            // 逻辑2：_targetNode 为空 则表示添加父节点

            // 不允许这样拖拽
            alert("不能这样操作");
            return false;
        	// 先删掉垃圾节点数据
         //    if(_node.id){
         //    	if(!this.delItem({id:_node.id})) return false;
         //    }

        	// // 新增正式节点
         //    Common.Base.loadJsonNoAsync({
         //        url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=addMenu',
         //        type: 'POST',
         //        data: _data
         //    }, function(json) {
         //        if (json.code == 200) {
         //        	_result = true;
         //        	_inThis.getMenuTree();
         //            console.log("菜单添加成功");
         //        } else {
         //            alert(json.msg);
         //        }
         //    });
        }
        return _result;
    },
    /**
    * 节点排序
    * @param _node:object 拖拽的节点信息
    * @param _targetNdoe:object 目标节点对象
    * @apram _moveType:string prev：排序前面；next：排序后面 inner 子节点
    */
    sortNode:function(_node,_targetNode,_moveType){
    	if(_moveType != "inner"){
    		// 排序
    		// 获取排序
    		var _nodes = Tree._bodyTree.getNodes();
    		var orders = [];
    		_nodes.forEach(function(node,index){
    			orders.push({
    				itemid:node.id,
    				index:index+1
    			})
    		});

    		var _result = false;
    		Common.Base.loadJsonNoAsync({
    			url:"http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=setItemOrders",
    			type:"get",    			
    			data:{
                    uid:this._uid,
    				orders:JSON.stringify(orders)
    			}
    		},function(json){
    			if(json.code == 200){
    				_result = true;
    				console.log("排序成功");
    			}else{
    				alert(json.msg);
    			}
    		});
    		return _result;
    	}else if(_moveType == "inner"){
            // console.log(_node,_targetNode);

            var _is_catalog = 3; // 把目录拖拽到目录下

            // if(_node.isParent && _targetNode.isParent){
            //     // 父节点拖拽到父节点
            //     _is_catalog = 3;
            // }else if(!_node.isParent && _targetNode.isParent){
            //     // 把子节点拖拽到父节点下
            //     _is_catalog = 2;
            // }

        	// 变为子节点
            var _result = false,
                _inThis = this;
                
            Common.Base.loadJsonNoAsync({
                url:"http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=catalog",
                type:"post",             
                data:{
                    uid:this._uid,
                    mid:_targetNode.id,
                    itemid:_node.id,
                    is_catalog:_is_catalog,
                    drag_file:true
                }
            },function(json){
                if(json.code == 200){
                    _result = true;
                    _inThis.getMenuTree();
                    console.log("调整子父关系成功");
                }else{
                    alert(json.msg);
                }
            });
            return _result;
        }
    },
    /**
    * 获取子节点
    */
    getChildNodes:function(){
        var _inThis = this;

        // 获取子节点列表
        Common.Base.loadJson({
            url: 'http://ppssii.com/plugin.php?id=xiaomy_cus_todo&mod=getCatyitem',
            type: 'post',
            data: {
                mid: this._mid
            }
        }, function(json) {
            if (json.code == 200) {
                var _zNodes = [],
                    _data = json.data.item || [];
                (_data).forEach(function(item) {
                    _zNodes.push({
                        name: item.content,
                        id: item.id,
                        pId: item.mid
                    });
                });

                // console.log(_zNodes);

                // 清空树                
                var nodes = _inThis._childTree.getNodes();
                while(nodes != undefined && nodes.length > 0){
                    nodes.forEach(function(node){
                        _inThis._childTree.removeNode(node);
                    });
                    nodes = _inThis._childTree.getNodes();
                }

                // 赋值右侧内容树数据
                _inThis._childTree.addNodes(null, _zNodes);
                _inThis._childTree.expandAll(true);
            }
        });
    }
};
$(document).ready(function() {
    Tree.init();
});
