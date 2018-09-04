<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_trend.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/charts_base.js"></script>
<script type="text/javascript" src="/static/js/charts_trend.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">对比分析</span>
            <span class="movetitle">限时免费</span>
        </div>
        <div class="h30"></div>
        <div class="container">
          <?php include 'charts_menu.php';?>
          <div class="crumbs-nav-item">
            <div class="selector-set">
            </div>
          </div>
          <div class="h20" style="clear:both"></div>
          <div class="rptmain">
            <div class="rptcontent">
              <div class="head">
                <p class="ltitle">扫码对比分析<small>（区域趋势值）</small></p>
              </div>
              <div class="content">
                <div id="main" style="width:100%;height:450px;line-height:450px;text-align:center;font-size:100px;"></div>
                <div class="h50" style="clear:both"></div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
<!-- </div> -->
<?php include 'footer.php';?>
</body>
</html>