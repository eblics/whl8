<?php include 'common/header.php' ?>
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
            <table id="dealerTable" class="display">
            <thead>
                <tr>
                    <th width="30">ID</th>
                    <th>代理名称</th>
                    <th width="100">代理编码</th>
                    <th width="100">联系人</th>
                    <th>联系电话</th>
                    <th width="200">地址</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            </table>
        </div>
        <div id="btnAdd" class="btn btn-blue">增加代理</div>
    </div>
</div>
<script type="text/javascript" src="/static/js/dealer/dealer.js?1232"></script>
<?php include 'common/footer.php';?>