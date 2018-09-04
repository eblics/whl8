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
<script type="text/javascript" src="/static/js/reporting_show_scan_info.js"></script>
<style type="text/css">
    .main .table-form{margin:5px 10px 25px 10px;}
    .main .table-form .condition{margin:0 15px;}
    .main .table-form .condition .input{width:130px;background-position:98% 50%;}
    .main .table-form .btns{margin-left:20px;}
    .main .table-form .btns .btn{margin-left:5px;}
    .title{text-align:center;font-weight: 500;font-size: 26px;}
    table{width:100%;}
    .table-form th {padding: 10px;border:1px solid #f5f5f5}
    .table-form td{border:1px solid #f5f5f5} 
    .sub_title{text-align:center;display: block;font-size:18px;}
    .top_title{display:block;text-align:left;font-size:16px;padding:6px;background-color:#f5f5f5;margin:10px 0;}
    strong, b {font-weight: 500;}
    .table-form td.name {text-align: center;width:145px;font-size: 16px;font-weight: 500;}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">用户扫码记录</span>
        </div>
        <div class="h20"></div>
        <div class="content">
        <input type="hidden" id="param" value='<?php echo $data;?>'>
            <table id="reportingTable" class="display">
                <thead>
                    <tr>
                    	<th>用户ID</th>
                        <th>微信昵称</th>
                        <th>扫码时间</th>
                        <th>扫码地区</th>
                        <th>码批号</th>
                        <th>码文本</th>
                        <th>获得奖励</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    	<th>用户ID</th>
                    	<th>微信昵称</th>
                        <th>扫码时间</th>
                        <th>扫码地区</th>
                        <th>码批号</th>
                        <th>码文本</th>
                        <th>获得奖励</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>