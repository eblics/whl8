<?php include 'common/header.php' ?>
<link rel="stylesheet" type="text/css" href="/static/css/merchant_lock.css">
<script type="text/javascript" src="/static/js/work/work_role.js"></script>
</head>
<body><?php include 'common/menus.php';?>
<div class="main mlock">
    <?php include 'work_left.php';?>
    <div class="rightmain">
        <div class="path">
        <span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="content">
            <table id="roleTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>角色</th>
                        <th>角色编号</th>
                        <th>名字</th>
                        <th>手机号码</th>
                        <th>邮件</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div id="btnAdd" class="btn btn-blue">添加角色</div>
    </div>
</div>
<?php include 'common/footer.php';?>