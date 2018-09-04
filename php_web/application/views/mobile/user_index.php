<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/myboard.css"/>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/user/index.js"></script>
    <title>个人中心</title>
</head>
<body>
    <div class="content" <?=$mallid==null?'':'style="padding-bottom:55px;"'?>>
        <div class="head">
            <span class="photo" style="background-image:url(<?=$image?>)"></span>
            <span class="info">
                <span class="name"><?=$name?></span>
                <span class="address">
                    <span class="icon"></span>
                    <span class="text"><?=$cityinfo?></span>
                </span>
            </span>
        </div>
        <div class="shortcut">
            <span class="item" href="/user/red_packet?mch_id=<?=$mchid?>">
                <span class="icon redpacket"></span>
                <span class="text">我的红包</span>
            </span>
            <span class="item" href="/user/cards?mch_id=<?=$mchid?>">
                <span class="icon card"></span>
                <span class="text">我的乐券</span>
            </span>
            <span class="item" href="/user/points?mch_id=<?=$mchid?>">
                <span class="icon score"></span>
                <span class="text">我的积分</span>
            </span>
        </div>
        <div class="menu">
            <?php if (isProd()): // group - begin?>
            <div class="item" href="/group/lists/<?=$mchid?>">
                <span class="icon users"></span>
                <span class="label"><span class="text"><?=$groupProductName?> <font style="color:#f70;font-size:0.9rem">（戳进来 试试红包花样玩法）</font></span><span class="symbol"></span></span>
            </div>
            <?php endif; // group - end?>
            <?php if($mallid!=null):?>
            <div class="item" href="/app/mall/order.html">
                <span class="icon order"></span>
                <span class="label"><span class="text">我的订单</span><span class="symbol"></span></span>
            </div>
            <?php endif;?>
            <div class="item" href="/user/profile?mch_id=<?=$mchid?>">
                <span class="icon info"></span>
                <span class="label"><span class="text">个人信息</span><span class="symbol"></span></span>
            </div>
        </div>
        <div class="menu">
            <div class="item" style="display:none">
                <span class="icon scorerank"></span>
                <span class="label"><span class="text">积分排行</span><span class="symbol"></span></span>
            </div>
        </div>
    </div>
    <?php if($mallid!=null):?>
    <div class="menuview">
        <span class="menu" href="/app/mall/home.html">
            <span class="icon home"></span>
            <span class="text">首页</span>
        </span>
        <span class="menu" href="/app/mall/list.html">
            <span class="icon list"></span>
            <span class="text">分类</span>
        </span>
        <span class="menu" href="/app/mall/trolley.html">
            <span class="icon trolley"></span>
            <span class="text">购物车</span>
        </span>
        <span class="menu selected">
            <span class="icon myboard"></span>
            <span class="text">我的</span>
        </span>
    </div>
    <?php endif;?>
</body>
</html>