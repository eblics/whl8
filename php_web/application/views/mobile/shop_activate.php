<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <!-- <link rel="stylesheet" type="text/css" href="/static/lib/css/mobiscroll.custom-2.5.2.min.css"/> -->
    <link rel="stylesheet" type="text/css" href="/static/css/shop_activate.css"/>
    <script type="text/javascript" src="/static/lib/jquery-1.8.3.min.js"></script>
    <!-- <script type="text/javascript" src="/static/lib/mobiscroll.custom-2.5.2.min.js"></script> -->
    <!-- <script type="text/javascript" src="/static/lib/mobiscroll.datetime-2.5.1-zh.js"></script> -->
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <script type="text/javascript" src="/static/js/hlsjs.js"></script>
    <script type="text/javascript" src="/static/js/shop_activate.js"></script>
    <title>商城信息</title>
</head>
<body>
    <div class="content">
        <div class="block">
            <div class="list">
                <div class="item">
                    <span class="info">店主姓名</span>
                    <input id="ownerName" placeholder="店主姓名" type="text" class="textbox" />
                </div>
                <div class="item">
                    <span class="info">店主手机号</span>
                    <input id="ownerPhoneNum" placeholder="店主手机号" type="number" class="textbox" />
                </div>
            </div>
        </div>
        <div class="block" id="validBlock" style="display: none">
            <div class="list">
                <div class="item">
                    <span class="info">验证码</span>
                    <input id="validCode" placeholder="手机验证码" type="text" class="textbox" />
                </div>
            </div>
        </div>
        <div class="block" id="addressBlock" style="display: none">
            <div class="list">
                <div class="item">
                    <div class="getaddress disabled">获取地址</div>
                </div>
                <div class="item">
                    <span class="info">城市</span>
                    <span id="city"></span>
                    <input id="areaCode" type="hidden" value=""/>
                    <input id="lat" type="hidden" value=""/>
                    <input id="lng" type="hidden" value=""/>
                </div>
                <div class="item">
                    <span class="info">门店地址</span>
                    <input id="address" placeholder="门店地址" type="text" class="textbox" value=""/>
                </div>
            </div>
        </div>
        <div class="buttons" id="validbuttons">
            <div class="validate disabled" data-id="<?=$id?>">确认信息</div>
        </div>
        <div class="buttons" id="activatebuttons" style="display:none;">
            <div class="activate disabled" data-id="<?=$id?>">激活设备</div>
        </div>
    </div>
</body>
</html>