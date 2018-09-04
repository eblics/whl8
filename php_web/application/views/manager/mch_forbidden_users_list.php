<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/mch_forbidden_users_list.js"></script>
<style type="text/css">
    .main td .btn-text{margin:0px 5px}
    .main td .headimg{width:40px;height:40px;border-radius:50%}
    .transDialog .apply{width:100%;border:1px solid #eee;}
    .transDialog .apply td,.transDialog .apply th{padding:10px;color:#666;}
    .transDialog .apply th{background:#fafafa;font-weight:700;font-size:14px;}
    .transDialog .apply td{vertical-align:top;}
    .transDialog .apply td.bb{border-bottom:1px solid #eee;background:#efefef;}
    .transDialog .apply td img{width:100%;}
    .transDialog .apply td h2{line-height:26px;padding:5px 0;border-bottom:1px solid #eee;}
    .transDialog .apply strong{font-weight:700;}
    .transDialog .admin {margin-top:20px;}
    .transDialog .admin li{padding:5px;border:1px solid #fff;border-radius:5px;text-align:center;}
    .transDialog .admin li:hover{background:#eee;border-color:#ccc;}
    .transDialog .admin li .btn{margin:0;height:29px;line-height:29px;}
    .transDialog .admin li .input{width:300px;}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">企业封禁用户列表</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="forbiddenUsersTable" class="display">
                <thead>
                    <tr>
                    	<th>用户ID</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th>openid</th>
                        <th>状态</th>
                        <th>封禁原因</th>
                        <th>封禁时间</th>
                        <th>申诉状态</th>
                        <th>申诉时间</th>
                        <th>备注</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>用户ID</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th>openid</th>
                        <th>状态</th>
                        <th>封禁原因</th>
                        <th>封禁时间</th>
                        <th>申诉状态</th>
                        <th>申诉时间</th>
                        <th>备注</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>