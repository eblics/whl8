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
<script type="text/javascript" src="/static/js/reporting_daylists.js"></script>
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
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">日报表统计</span>
        </div>
        <div class="h20"></div>
        <div class="table-form">
            <span class="condition">
                <span class="name">开始时间：</span>
                <span class="value">
                    <input class="input Wdate" id="startDate" value="" onfocus="WdatePicker({isShowWeek:true,minDate:'#F{$dp.$D(\'endDate\',{M:-3});}',maxDate:'#F{$dp.$D(\'endDate\');}'})" />
                </span>
            </span>
            <span class="condition">
                <span class="name">结束时间：</span>
                <span class="value">
                    <input class="input Wdate" id="endDate" value="" onfocus="WdatePicker({isShowWeek:true,maxDate:'#F{$dp.$D(\'startDate\',{M:3});}',minDate:'#F{$dp.$D(\'startDate\');}'})" />
                </span>
            </span>
            <span class="btns">
                <span id="btnSearch" class="btn btn-blue noselect">查询</span>
                <span id="btnDownload" class="btn btn-blue noselect btn-disabled">下载</span>
            </span>
        </div>
        <div class="content">
            <table id="reportingTable" class="display">
                <thead>
                    <tr>
                        <th>日期</th>
                        <th>扫码次数</th>
                        <th>红包金额（元）</th>
                        <th>提现金额（元）</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>日期</th>
                        <th>扫码次数</th>
                        <th>红包金额（元）</th>
                        <th>提现金额（元）</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>