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
<script type="text/javascript" src="/static/js/multistrategy_lists.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">叠加策略管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="multistrategyTable" class="display">
                <thead>
                    <tr>
                        <th>编号</th> 
                        <th>名称</th>
                        <th>中奖率</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>编号</th> 
                        <th>名称</th>
                        <th>中奖率</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/multistrategy/add">新建叠加策略</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
