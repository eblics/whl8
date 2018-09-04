<?php include 'common/header.php' ?></head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <table id="shopTable" class="display">
            <thead><tr><th width="50">门店ID</th>
            <th>门店名称</th>
            <th>门店所在区域</th>
            <th>门店地址</th>
            <th>店主姓名</th>
            <th>店主手机号</th>
            <th>创建时间</th>
            <th width="50">状态</th>
            <th width="220" class="nowrap">操作</th></tr></thead></table>
        </div>
        <div id="btnSend" class="btn btn-blue">增加门店</div>
    </div>
</div>
<input type="hidden" id="type" value="">
<script type="text/javascript" src="/static/js/shop/shop.js"></script>
<?php include 'common/footer.php';?>