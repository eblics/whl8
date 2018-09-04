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
<script type="text/javascript" src="/static/js/mixstrategy_lists.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">组合策略管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="mixstrategyTable" class="display">
                <thead>
                    <tr>
                        <th>编号</th> 
                        <th>名称</th>
                        <th>权重</th>
                        <th>权重占比</th>
                        <th>中奖率</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>编号</th> 
                        <th>名称</th>
                        <th>权重</th>
                        <th>权重占比</th>
                        <th>中奖率</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/mixstrategy/add">新建组合策略</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
