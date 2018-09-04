<?php include 'common/header.php' ?></head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <table id="mchTable" class="display">
            <thead><tr><th width="50">企业ID</th>
            <th width="200">企业名称</th>
            <th width="60">负责人</th>
            <th>登录手机号</th>
            <th>创建时间</th>
            <th>审核时间</th>
            <th>支付帐号</th>
            <th>余额</th>
            <th width="50">状态</th>
            <th width="220" class="nowrap">操作</th></tr></thead></table>
        </div>
        <div id="btnSend" value="0" class="btn btn-blue">发送短信</div>
    </div>
</div>
<input type="hidden" id="type" value="<?= isset($value)? $value: ''?>">
<script type="text/javascript" src="/static/js/merchant/merchant.js"></script>
<?php include 'common/footer.php';?>