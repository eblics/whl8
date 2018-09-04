<?php include 'common/header.php'; ?></head>
<body><?php include 'common/menus.php';?>
<div class="main">
<?php include 'admin_lefter.php';?>
<div class="rightmain">
    <div class="path"><span class="title fleft">人员列表</span></div>
    <div class="h20"></div>
    <div class="content">
    <table id="opeTable" class="display">
    <thead><tr><th width="50">帐户ID</th>
    <th width="200">用户名</th>
    <th width="120">帐户等级</th>
    <th width="120">联系电话</th>
    <th>创建时间</th>
    <th>状态</th>
    <th>操作</th></tr></thead>
    </table></div></div></div>
<script type="text/javascript" src="/static/js/admin/admin.js"></script>
<?php include 'common/footer.php';?>