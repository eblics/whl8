<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/admin.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">账户管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <input type="hidden" id="mch" value="<?=$mchCode?>">
            <table id="admin_list_container" class="display">
                <?php if($mchCode == 0): ?>
                <thead><tr>
                    <th width="30">编号</th> 
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>角色</th>
                    <th>状态</th>
                    <th>免验证</th>
                    <th width="160">操作</th>
                </tr></thead>
                <tfoot><tr>
                    <th>编号</th> 
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>角色</th>
                    <th>状态</th>
                    <th>免验证</th>
                    <th>操作</th>
                </tr></tfoot>
                <?php else: ?>
                <thead><tr>
                    <th width="30">编号</th> 
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>角色</th>
                    <th>状态</th>
                    <th width="160">操作</th>
                </tr></thead>
                <tfoot><tr>
                    <th>编号</th> 
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>角色</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr></tfoot>
                <?php endif; ?>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/admin/add">  新建角色账户
            </a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<script type="text/javascript">
    adminList.init();
</script>
</body>
</html>