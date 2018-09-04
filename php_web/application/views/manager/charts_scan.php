<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_scan.css" />
<link type="text/css" rel="stylesheet" href="/static/libs/DateRange/dateRange.css" />
<link type="text/css" rel="stylesheet" href="/static/msgbox/msgbox.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/libs/DateRange/dateRange.js"></script>
<script type="text/javascript" src="/static/msgbox/msgbox.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=o78tmC1oaRiWGTVpZowpSyrP"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/Heatmap/2.0/src/Heatmap_min.js"></script>
<script type="text/javascript" src="/static/js/charts_base.js"></script>
<script type="text/javascript" src="/static/js/charts_scan.js"></script>
<script type="text/javascript">
var yesterday="<?=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')))?>";
var today="<?=date('Y-m-d',time())?>";
var curweek="<?=get_week_begin()?>";
var curmonth="<?=date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')))?>";
var threemonth="<?=date('Y-m-d',mktime(0,0,0,date('m')-3,1,date('Y')))?>";
</script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">扫码分布统计</span>
        </div>
        <div class="h20"></div>
        <?php include 'charts_menu.php';?>
        <div id="content">
            <div class="head" style="display:none;">
                <p class="ltitle">扫码热力图<small style="color:#999;font-size:12px;">（<span id="now_time"></span>）</small></p>
                <div class="rtitle">
                    <ul>
                        <li><a href="javascript:void(0);" date="yesterday" class="datebtn">昨天</a></li>
                        <li><a href="javascript:void(0);" date="week" class="datebtn">本周</a></li>
                        <li><a href="javascript:void(0);" date="month" class="datebtn">本月</a></li>
                    </ul>
                    <span class="l"></span>
                    <img id="scan_date_get" style="cursor:pointer" src="/static/images/rili.png" />
                </div>
            </div>
            <div id="container"></div>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>