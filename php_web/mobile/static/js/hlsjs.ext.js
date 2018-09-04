var hlsjs = {
    lecode: null,
    getQueryString: function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)
            return unescape(r[2]);
        return null;
    },
    getSegment: function(index) {
        var pathArr = window.location.pathname.split('/');
        return pathArr[index];
    },
    getRootUrl: function() {
        var search = window.location.search;
        var params = search.split('&');
        var env = params[params.length - 1].split('=');
        if (env.length === 1) {
            return 'http://m.lsa0.cn/';
        }
        env = env[1];
        if (env === 'test') {
            return 'http://test.m.lsa0.cn/';
        }
        if (env === 'dev') {
            return 'http://dev.m.lsa0.cn/';
        }
        return 'http://m.lsa0.cn/';
    },
    getRptUrl: function() {
        var hostName = window.location.hostname;
        if (hostName == 'dev.m.lsa0.cn') {
            return 'http://dev.rpt.lsa0.cn'
        } else if (hostName == 'test.m.lsa0.cn') {
            return 'http://test.rpt.lsa0.cn';
        } else
            return 'http://rpt.lsa0.cn'
    },
    getCurrentUser: function(callback) {
        $.ajax({
            url: hlsjs.getRootUrl() + 'hlsjs/get_current_user',
            success: function(result) {
                callback(result);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
    }
};

hlsjs.init = function(options) {
    hlsjs.options = options;
    /*
     * 如果是微信浏览器，取wx对象
     */
    hlsjs.wx = function(apilist) {
        if (navigator.userAgent.indexOf('MicroMessenger') > 0 && wx) {
            var jsapilist=['scanQRCode', 'getLocation', 'hideOptionMenu', 'onMenuShareAppMessage', 'hideAllNonBaseMenuItem', 'hideMenuItems', 'showMenuItems'];
            if(apilist!=undefined){
                apilist.forEach(function(value){
                    jsapilist.push(value);
                });
            }

            wx.config({
                debug: this.options.debug,
                appId: this.options.appId,
                timestamp: this.options.timestamp,
                nonceStr: this.options.nonceStr,
                signature: this.options.signature,
                jsApiList: jsapilist
            });
            return wx;
        }
        return undefined;
    };

    hlsjs.takeActivity = function(callback) {
        $.ajax({
            url: hlsjs.getRootUrl() + 'activity/take',
            data: {code: hlsjs.lecode},
            success: function(result) {
                if (result.errcode == 0 || result.errcode == 3) {
                    callback.call(window, result);
                } else {
                    // alert(result.errmsg + '。');
                    callback.call(window, result);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
    };

    hlsjs.getUserInfo = function(callback) {
        $.ajax({
            url: hlsjs.getRootUrl() + 'hlsjs/get_user_info',
            data: {code: hlsjs.lecode},
            success: function(result) {
                if (result.errcode == 0) {
                    callback.call(window, result);
                } else {
                    alert(result.errmsg + '。');
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
    };

    hlsjs.watchLocation = function(pos, code, done, fail) {
        $.ajax({
            url: hlsjs.getRootUrl() + 'activity/get_best_match',
            data: {
                code: window.lecode,
                pos: pos
            }
        }).done(done).fail(fail);
    };
    hlsjs.getLocation = function(successCallback, failCallback) {
        var wx = this.wx();
        var pos = {
            lng: 0,
            lat: 0
        };
        if (wx == undefined) {
            if (failCallback == undefined)
                return;
            return failCallback(pos);
        }
        return wx.ready(function() {
            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function(res) {
                    if (successCallback == undefined) {
                        return;
                    }
                    return successCallback({
                        lng: res.longitude,
                        lat: res.latitude
                    });
                },
                cancel: function(res) {
                    console.log('canceled');
                    if (failCallback == undefined)
                        return;
                    return failCallback(pos);
                },
                fail: function(res) {
                    console.log('failed');
                    if (failCallback == undefined)
                        return;
                    return failCallback(pos);
                }
            });
        });
    };
    hlsjs.scanQRCode = function(options) {
        var wx = hlsjs.wx();
        if (wx == undefined) {
            alert('请在微信客户端中打开');
            return;
        }
        wx.ready(function() {
            wx.scanQRCode(options);
        });
    };
}
hlsjs.ready = function(arg) {
    var data = {
        url: window.location.href
    };
    var callback = undefined;
    if (typeof arg == 'object') {
        if (arg.appid != undefined) {
            data.appid = arg.appid;
        } else if (arg.mchid != undefined) {
            data.mchid = arg.mchid;
        }
        if (data.appid == undefined && data.mchid == undefined) {
            alert('请填写appid或mchid');
            return;
        }
        callback = arg.success;
    } else if (typeof arg == 'function') {
        callback = arg;
    }

    $.ajax({
        url: hlsjs.getRootUrl() + 'weixin/jssignature',
        data: data
    }).done(function(result) {
        if (result.errcode == 0) {
            hlsjs.lecode = result.options.code;
            hlsjs.init(result.options);
            if (typeof callback === 'function') {
                callback.call(window);
            }
        } else {
            alert(result.errmsg + '。');
        }
    }).fail(function(data) {
        alert('无法连接服务器。');
    });

}
