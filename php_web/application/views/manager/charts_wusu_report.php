<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/charts_base.js"></script>
<style>
.ws_menu_btn{
    width: 260px;
    height: 160px;
    line-height: 160px;
    background: #5a8edd;
    display: block;
    color: #fff;
    font-size: 30px;
    border-radius: 8px;
    text-align: center;
    float: left;
    margin-right: 20px;
}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">乌苏定制报表</span>
        </div>
        <div class="h20"></div>
        <div id="content">
            <a class="ws_menu_btn" href="/charts/wusu_report_for_code">二维码瓶盖查询</a>&nbsp;&nbsp;
            <a class="ws_menu_btn" href="/charts/wusu_report_for_score">积分核对</a>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>