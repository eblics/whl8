<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/app_my.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/app_my.js"></script>
<script type="text/javascript">
    var mchId = <?=$mch_id?>;
</script>
</head>
<body>
<?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_app.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">我的应用</span>
            </div>
            <div class="h20"></div>
            <div class="content">
                <table id="myAppTable" class="display my-app">
                    <thead>
                        <tr>
                            <th width="30">编号</th> 
                            <th>名称</th>
                            <th>链接</th>
                            <th>状态</th>
                            <th width="160">操作</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>编号</th> 
                            <th>名称</th>
                            <th>链接</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </tfoot>
                </table>
                <a id="btnAdd" class="btn btn-blue noselect" href="/app">应用商店</a>
            </div>
        </div>
    </div>
<?php include 'footer.php';?>
</body>
</html>
