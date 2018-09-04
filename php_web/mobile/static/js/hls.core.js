(function(window) {

    window.lecode = undefined;

    var hls = window.hls || {};

    hls.core = {};

    // -----------------------------------
    // hls.core.api
    hls.core.api = {};

    function matchActivity() {

    }

    hls.core.api.matchActivity = matchActivity;

    function takeActivity() {

    }

    hls.core.api.takeActivity = takeActivity;

    hls.ready = function(successCallback, failCallback) {
        // 初始化微信组件
        var params = {
            "url": location.href
        };
        $.get(hls.config.Mobile.base_url + 'weixin/jssignature', params, function(resp) {
            if (resp.errcode == 0) {
                if (typeof successCallback === 'function') {
                    window.lecode = result.options.code;
                    var jsapilist = [
                        'scanQRCode', 
                        'getLocation', 
                        'hideOptionMenu', 
                        'onMenuShareAppMessage', 
                        'hideAllNonBaseMenuItem', 
                        'hideMenuItems', 
                        'showMenuItems'
                    ];
                    var wxConfig = {
                        debug: options.debug,
                        appId: options.appId,
                        timestamp: options.timestamp,
                        nonceStr: options.nonceStr,
                        signature: options.signature,
                        jsApiList: jsapilist
                    };
                    wx.config(wxConfig);
                    wx.error(function(err) {
                        if (typeof failCallback === 'function') {
                            failCallback.call(window, err);
                        }
                    });
                    wx.ready(function() {
                        if (typeof successCallback === 'function') {
                            successCallback.call(window);
                        }
                    });
                }
            } else {
                if (typeof failCallback === 'function') {
                    failCallback.call(window, resp.errmsg);
                }
            }
        }).fail(function(err) {
            alert('无法连接服务器。');
        });
    };
})(window);