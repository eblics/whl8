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
<script type="text/javascript" src="/static/js/salesman.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">业务员管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="salesmanTable" class="display">
                <thead>
                    <tr>
                        <th width="30">编号</th> 
                        <th>姓名</th>
                        <th>手机号</th>
                        <th>身份证号码</th>
                        <th>OpenId</th>
                        <th>状态</th>
                        <th width="160">操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>编号</th> 
                        <th>姓名</th>
                        <th>手机号</th>
                        <th>身份证号码</th>
                        <th>OpenId</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/salesman/create">添加业务员</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<script type="text/javascript">
    Page.init();
</script>
</body>
</html>
