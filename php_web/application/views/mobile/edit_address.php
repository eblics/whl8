<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <link rel="stylesheet" type="text/css" href="/app/mall/css/mobiscroll.custom-2.5.2.min.css"/>
    <link rel="stylesheet" type="text/css" href="/app/mall/css/common.css">
    <link rel="stylesheet" type="text/css" href="/app/mall/css/edit_address.css">
    <script type="text/javascript" src="/app/mall/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/app/mall/js/mobiscroll.custom-2.5.2.min.js"></script>
    <script type="text/javascript" src="/static/js/edit_address.js?v=2.0"></script>
    <title>收货地址</title>
</head>
<body>
    <div class="content">
        <div class="block">
            <div class="list">
                <div class="item">
                    <span class="info">联系人</span>
                    <input id="receiver" placeholder="你的姓名" maxLength="20" type="text" class="textbox"/>
                </div>
                <div class="item">
                    <span class="info">联系电话</span>
                    <input id="phoneNum" placeholder="你的手机号" type="number" class="textbox"/>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="list">
                <div class="item">
                    <span class="info">地址</span>
                    <span id="areainfo" class="textspan">你的省市区</span>
                </div>
                <div class="item">
                    <span class="info">住址</span>
                    <input id="address" placeholder="例：XX小区XX号楼XXX室" maxLength="100" type="text" class="textbox"/>
                </div>
            </div>
        </div>
        <div class="save disabled">保存</div>
        <ul id="areamenu" style="display:none"></ul>
    </div>
</body>
</html>