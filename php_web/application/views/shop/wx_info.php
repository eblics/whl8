<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no"/>
    <title>微信用户信息</title>
    <style type="text/css">
        img {
            width: 150px;
        }
    </style>

    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/loader.js"></script>
</head>
<body>
    <div class="container">
        <p>nickname: <?=$wxUser->nickname?></p>
        <p>mobile: <?=$wxUser->mobile?></p>
        <p>openid: <?=$wxUser->openid?></p>
        <img src="<?=$wxUser->headimgurl?>">

        <div>
            <button id="btnGetLocation">获取地理位置</button>
            <button id="btnSendMsg">发送群消息</button>
        </div>
    </div>

    <script type="text/javascript">
        var wxConfigSuccess = true, wxConfigReady = false;
        wx.config(<?=$jssdk_params?>);
        
        wx.ready(function() { 
            if (! wxConfigSuccess) return;
            wxConfigReady = true;
        });
        wx.error(function(err) { 
            wxConfigSuccess = false;
            console.log('err: ' + JSON.stringify(err));
        });

        function getLocation() {
            if (! wxConfigReady) return;
            wx.getLocation({
                type: 'gcj02',
                success: function(res) {
                    if (res.errMsg === 'getLocation:ok') {
                        alert(JSON.stringify(res));
                        openLocation(res.latitude, res.longitude);
                    }
                },
                fail: function(err) {
                    console.log('err: ' + JSON.stringify(err));
                }
            });
        }

        function openLocation(latitude, longitude) {
            wx.openLocation({
                latitude: latitude, // 纬度，浮点数，范围为90 ~ -90
                longitude: longitude, // 经度，浮点数，范围为180 ~ -180。
                name: 'Test', // 位置名
                address: '门店一', // 地址详情说明
                scale: 15, // 地图缩放级别,整形值,范围从1~28。默认为最大
                infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
            });
        }

        function sendMsg() {
            $.post('/wx_login/send_msg', {}, function(resp) {
                if (resp.errcode === 0) {
                    alert('发送成功。');
                } else {
                    alert(resp.errmsg);
                }
            }).fail(function() {
                alert('无法连接服务器。');
            });
        }

        $('#btnGetLocation').click(function() {
            getLocation();
        });
        $('#btnSendMsg').click(function() {
            sendMsg();
        });
    </script>
</body>
</html>