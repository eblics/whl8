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
<script type="text/javascript" src="/static/js/batch_lists.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">乐码管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="batchTable" class="display">
                <thead>
                    <tr>
                        <th width="35">ID</th>
                        <th width="135">批号</th>
                        <th>数量</th>
                        <th>关联活动</th>
                        <th>关联产品</th>
                        <th>已扫/剩余（<span class="btn-text noselect blue get_scan_num_all">查看</span>）</th>
                        <th>状态</th>
                        <th>激活时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>批号</th>
                        <th>数量</th>
                        <th>关联活动</th>
                        <th>关联产品</th>
                        <th>已扫/剩余</th>
                        <th>批次状态</th>
                        <th>激活时间</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/batch/add">申请乐码</a>
            <a id="btnTool" class="btn btn-blue noselect" href="/static/doc/欢乐扫-乐码处理工具.rar">下载乐码工具</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<div id='showAct' style="border:1px solid #ccc;"></div>
<div id='oprate_time_tip' class="popup" style="border:1px solid #ccc;"></div>
</body>
</html>
