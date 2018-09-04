<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<link type="text/css" rel="stylesheet" href="/static/css/help.css?12" />
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_help.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">支付限额修改</span>
            </div>
            <div class="h20"></div>
            <div class="content">
                <div class="paragraph h3">一. 修改微信红包每日个数限制</div>
                <div class="paragraph">1. 在“产品中心”找到“现金红包”。</div>
                <div class="paragraph"><img src="/static/images/a1.jpg"></div>
                <div class="paragraph">2. 对现金红包功能进行“产品设置”。</div>
                <div class="paragraph"><img src="/static/images/a2.jpg"></div>
                <div class="paragraph">3. 选择“修改”。</div>
                <div class="paragraph"><img src="/static/images/a3.jpg"></div>
                <div class="paragraph">4. 将用户领取上限红包个数修改为适当个数，并提交保存。</div>
                <div class="paragraph"><img src="/static/images/a4.jpg"></div>
                <div class="paragraph"></div>
                <div class="paragraph h3">二. 修改企业支付每日个数限制</div>
                <div class="paragraph">1. 在“账户中心”中找到“API安全”。</div>
                <div class="paragraph"><img src="/static/images/a5.jpg"></div>
                <div class="paragraph">2. 拉至最下，修改每日向同一用户付款限制次数，并保存。</div>
                <div class="paragraph"><img src="/static/images/a6.jpg"></div>

            </div>
           
        </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
