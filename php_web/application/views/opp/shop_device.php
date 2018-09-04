<?php include 'common/header.php' ?></head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <table id="deviceTable" class="display">
            <thead><tr><th width="50">设备ID</th>
            <th>设备编号</th>
            <th>备注</th>
            <th>major</th>
            <th>minor</th>
            <th width="50">状态</th>
            <th width="220" class="nowrap">操作</th></tr></thead></table>
        </div>
        <div id="btnSend" value="0" class="btn btn-blue">增加设备</div>
    </div>
</div>
<input type="hidden" id="type" value="">
<script type="text/javascript" src="/static/js/shop/device.js"></script>
<?php include 'common/footer.php';?>