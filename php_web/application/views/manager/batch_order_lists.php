<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/batch_order_lists.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/batch_order_lists.js"></script>
<script type="text/javascript">
var appid="<?=$appid?>";
var appsecret="<?=$appsecret?>";
var apiurl="<?=$apiurl?>";
</script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">入库单管理</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <table id="orderTable" class="display">
                <thead>
                    <tr>
                        <th width="35">编号</th>
                        <th>订单编号</th>
                        <th>产品编码</th>
                        <th>产品名称</th>
                        <th>入库类型</th>
                        <th>入库时间</th>
                        <th>上传时间</th>
                        <th>已扫/剩余（<span class="btn-text noselect blue get_scan_num_all">查看</span>）</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>编号</th>
                        <th>订单编号</th>
                        <th>产品编码</th>
                        <th>产品名称</th>
                        <th>入库类型</th>
                        <th>入库时间</th>
                        <th>上传时间</th>
                        <th>已扫/剩余</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </tfoot>
            </table>
            <a id="btnAdd" class="btn btn-blue noselect" href="/batch/order_add">增加入库单</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
<div id='showTooltip' class='tooltip popup'></div>
</body>
</html>