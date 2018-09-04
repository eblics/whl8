<?php include 'common/header.php' ?>
<script type="text/javascript" src="/static/js/merchant/merchant_unlock_wait.js"></script>
<style>
::-webkit-scrollbar{width:0;height:0}
.userinfo p{font-size:14px;padding:5px 0;}
</style>
</head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <?php include 'merchant_lefter.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <table id="userTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户昵称</th>
                    <!--<th>openid</th>-->
                    <th>申请时间</th>
                    <th>封禁原因</th>
                    <th>封禁时间</th>
                    <th>状态</th>
                    <th>二维码图片</th>
                    <th width="250">解封理由</th>
                    <!-- <th>行为明细</th> -->
                    <th width="180" class="nowrap">操作</th>
                </tr>
            </thead>
            </table>
        </div>
    </div>
</div>
<?php include 'common/footer.php';?>