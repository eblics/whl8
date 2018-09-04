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
<script type="text/javascript" src="/static/js/member_memberlist.js"></script>
<style type="text/css">
    .main .table-form{margin:5px 10px 25px 10px;}
    .main .table-form .condition{margin:0 15px;}
    .main .table-form .condition .input{width:130px;background-position:98% 50%;}
    .main .table-form .btns{margin-left:20px;}
    .main .table-form .btns .btn{margin-left:5px;}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">会员列表</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="memberTable" class="display">
                <thead>
                    <tr>
                    	<th>ID</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th>真实姓名</th>
                        <th>性别</th>
                        <th>手机号码</th>
                        <th style="width:200px">收货地址</th>
                        <!-- <th>关注时间</th>
                        <th>创建时间</th> -->
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>ID</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th>真实姓名</th>
                        <th>性别</th>
                        <th>手机号码</th>
                        <th>收货地址</th>
                        <!-- <th>关注时间</th>
                        <th>创建时间</th> -->
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>