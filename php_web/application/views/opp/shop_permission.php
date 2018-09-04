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
            <thead><tr><th width="50">企业ID</th>
            <th>企业名称</th>
            <th>企业地址</th>
            <th>企业邮箱</th>
            <th>企业电话</th>
            <th width="50">状态</th>
            <th width="220" class="nowrap">操作</th></tr></thead></table>
        </div>
    </div>
</div>
<input type="hidden" id="type" value="">
<script type="text/javascript" src="/static/js/shop/permission.js"></script>
<?php include 'common/footer.php';?>