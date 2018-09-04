<?php include 'common/header.php' ?>
<link rel="stylesheet" type="text/css" href="/static/css/merchant_lock.css">
<script type="text/javascript" src="/static/js/merchant/merchant_lock.js"></script>
</head>
<body><?php include 'common/menus.php';?>
<div class="main mlock">
    <?php include 'work_left.php';?>
    <div class="rightmain">
        <div class="path"><span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <table id="roleTable" class="">
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
        <div id="btnAdd" class="btn btn-blue">增加角色</div>
    </div>
</div>
<?php include 'common/footer.php';?>