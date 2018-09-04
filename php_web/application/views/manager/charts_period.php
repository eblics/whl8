<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/charts_base.js"></script>
<script type="text/javascript" src="/static/js/charts_period.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
<style>
.ihelp{
  width: 20px;
  height: 20px;
  cursor: pointer;
  background: url(/static/images/ihelp.png);
  background-size: 20px 20px;
  position: absolute;
  right: 20px;
  top: 25px;
}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">时段扫码统计</span>
        </div>
        <div class="h30"></div>
        <div class="container">
        <?php include 'charts_menu.php';?>
      <div class="rptmain">
        <div class="rptcontent">
          <div class="head">
            <div style="float:left"><p class="ltitle">时段扫码</p></div>
            <div style="float:right">
                <i class="ihelp"></i>
            </div>
          </div>
          <div class="content">
            <div id="main" style="width:100%;height:400px;"></div>
            <div class="h50" style="clear:both"></div>
            <hr style="height:1px;border:none;border-top:1px solid #e0e0e0;margin:0px">
            <textarea id="down_data" style="display:none"></textarea>
            <div class="content">
              <table id="period_data" class="table">
                 <thead>
                      <tr>
                          <th>时段</th>
                          <th>扫码次数</th>
                      </tr>
                  </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>