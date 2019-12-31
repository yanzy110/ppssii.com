var Common = {
    Base: {        
        /**
         * 获取url参数
         */
        getParam: function(b) {
            var c = document.location.search;
            if (!b) {
                return c
            }
            var d = new RegExp("[?&]" + b + "=([^&]+)", "g");
            var g = d.exec(c);
            var a = null;
            if (null != g) {
                try {
                    a = decodeURIComponent(decodeURIComponent(g[1]))
                } catch (f) {
                    try {
                        a = decodeURIComponent(g[1])
                    } catch (f) {
                        a = g[1]
                    }
                }
            }
            return a;
        },
        /**
         * ajax获取json数据 支持回调
         * @param url(string) 接口地址 同时也支持对象传递{url:",data:{}}
         */
        loadJson: function(url, callback) {
            var _data = {},
                _type = "post",
                _headers = null,
                _nomask = false;

            if (typeof url == 'object' && url.url) {
                _data = url.data;
                if (url.type)
                    _type = url.type;
                if (url.headers) {
                    _headers = url.headers;
                }
                _nomask = url.nomask || false;
                url = url.url;
            }
            if (_type == "post" || _type == "POST") {

                $.ajax({
                    url: url,
                    type: _type, // 默认post
                    dataType: "json",
                    headers: _headers,
                    data: _data,
                    success: function(json) {                                                
                        if (typeof callback == "function") {
                            callback(json);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (typeof callback == "function") {
                            callback({ code: 0, message: jqXHR.responseJSON ? jqXHR.responseJSON.message || '操作异常' : '' });
                        }
                    }
                });
            } else {
                $.ajax({
                    url: url,
                    type: _type, // 默认post
                    dataType: "json",
                    data: _data,
                    headers: _headers,
                    success: function(json) {                        
                        if (typeof callback == "function") {
                            callback(json);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        if (typeof callback == "function") {
                            callback({ code: 0, message: jqXHR.responseJSON ? jqXHR.responseJSON.message || '操作异常' : '' });
                        }
                    }
                });
            }

        },
        /**
         * 同步获取数据
         */
        loadJsonNoAsync: function(url, callback) {
            var _data = {},
                _type = "post",
                _headers = null,
                _nomask = false;

            if (typeof url == 'object' && url.url) {
                _data = url.data;
                if (url.type)
                    _type = url.type;
                if (url.headers) {
                    _headers = url.headers;
                }
                _nomask = url.nomask || false;
                url = url.url;
            }

            if (_type == "post" || _type == "POST") {
                $.ajax({
                    url: url,
                    type: _type, // 默认post
                    dataType: "json",
                    async: false,
                    data:_data,
                    headers: _headers,
                    success: function(json) {
                        _data = json;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {                        
                        if (typeof callback == "function") {
                            callback({ code: 0, message: jqXHR.responseJSON ? jqXHR.responseJSON.message || '操作异常' : '' });
                        }
                    }
                });
            } else {
                $.ajax({
                    url: url,
                    type: _type, // 默认post
                    dataType: "json",
                    async: false, //同步机制开启
                    headers: _headers,
                    data: _data,
                    success: function(json) {
                        _data = json;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {                        
                        if (typeof callback == "function") {
                            callback({ code: 0, message: jqXHR.responseJSON ? jqXHR.responseJSON.message || '操作异常' : '' });
                        }
                    }
                });

            }
            if (typeof callback == "function") {
                callback(_data);
            }
        },
        /**
         * 获取form的值并转换为键值对的对象返回
         * @param _formId:string 表单ID
         */
        getFormData: function(_formId) {
            var _formData = {};
            if ($("#" + _formId).length == 0) return null;
            var _formArr = $("#" + _formId).serializeArray();
            _formArr.forEach(function(item) {
                _formData[item.name] = item.value;
            });
            return _formData;
        },
        /**
         * 手机号码格式是否正确
         * @param mobile:string 手机号码 11位
         * @return true/false
         */
        isMobile: function(mobile) {
            if (mobile && mobile.length != 11) return false;
            var reg = /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|17[0-9]{9}$|18[0-9]{9}$|19[0-9]{9}$/;
            if (mobile == '' || !reg.test(mobile)) {
                return false;
            }
            return true;
        },
        /**
         * 邮箱地址格式是否正确
         * @param email:string 邮箱地址
         * @return true/false
         */
        isEmail: function(email) {
            var reg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (email == '' || !reg.test(email)) {
                return false;
            }
            return true;
        },
        /**
         * 身份证号码格式是否正确
         * @param cardNo:string 身份证号码
         * @return true/false
         */
        isIdCardNo: function(cardNo) {
            if (cardNo == '' || !/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i.test(cardNo)) {
                return false;
            }
            return true;
        }        
    }
};

// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
// 例子：
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
Date.prototype.Format = function(fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "H+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};

$(document).ready(function() {
    if (localStorage.getItem("username")) {
        $("#curAdminUserName").text(localStorage.getItem("username"));
    }

    // 判断用类型
    var _utype = localStorage.getItem("utype");
    if(_utype == 2){
        // 教师
        $(".g-teacher-box").css("cssText","display:block !important");
    }else if(_utype == 1){
        // 学生
        $(".g-student-box").css("cssText","display:block !important");
    }else{
        // 管理员
        $(".g-teacher-box").css("cssText","display:block !important");
        $(".g-student-box").css("cssText","display:block !important");
        $(".g-admin-box").css("cssText","display:block !important");
    }
});