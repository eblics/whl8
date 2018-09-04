var hlsjs = {
    config: function(options) {
        this.options = options;
    },
    getQueryString: function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)
            return unescape(r[2]);
        return null;
    },

    watchLocation: function(pos, done, fail) {
        console.log(pos);
        $.post('/activity/get_best_match/', {
            code: options.code,
            pos: pos
        }).done(done).fail(fail);
    },
    test:function(pos){
        if(pos==undefined)
            pos={lng:0,lat:0};
        console.log('test');
        console.log(pos);
    },
    wx: function() {
        if (navigator.userAgent.indexOf('MicroMessenger') > 0 && wx) {
            wx.config({
                debug: this.options.debug,
                appId: this.options.appId,
                timestamp: this.options.timestamp,
                nonceStr: this.options.nonceStr,
                signature: this.options.signature,
                jsApiList: ['scanQRCode', 'getLocation','hideOptionMenu','onMenuShareAppMessage','hideAllNonBaseMenuItem','hideMenuItems','showMenuItems']
                //jsApiList: ['scanQRCode']
            });
            return wx;
        } else {
            return undefined;
        }
    },
    getLocation: function(callback) {
        var wx = this.wx();
        var pos = {
            lng: 0,
            lat: 0
        };
        return callback(pos);
        var to=setTimeout(function(){
            console.log('timeout');
            callback(pos);
        },1500);
        //var to=setInterval('callback',1000);
        if (wx) {
            wx.ready(function() {
                try {
                    console.log('try');
                    wx.getLocation({
                        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                        success: function(res) {
                            console.log('success');
                            clearTimeout(to);
                            console.log(res);
                            return callback({
                                lng: res.longitude,
                                lat: res.latitude
                            });
                        },
                        failed: function(res) {
                            console.log('failed');
                            console.log(res);
                            return callback(pos);
                        }
                    },{ maximumAge: 3000, timeout: 1000 });
                }
            catch (ex) {
                console.log('catch');
                console.log(ex);
                return callback(pos);
            }});
            wx.error(function(res){
                console.log('error');
                console.log(res);
            });
        }
        else{
            console.log('else');
            return callback(pos);
        }
    }
}
$(function(){
    var options = {
        debug: false,
        appId: '<?=$appId?>',
        timestamp: <?= $timestamp ?> ,
        nonceStr: '<?=$nonceStr?>',
        signature: '<?=$signature?>',
        code: '<?=isset($code) ? $code : "null"?>'
    };
    hlsjs.config(options);
});

