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
<script type="text/javascript" src="/static/js/charts_userrank.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">用户排行</span>
        </div>
        <div class="h30"></div>
        <div class="container">
        <?php include 'charts_menu.php';?>
        <div class="rptmain">
        <div class="rptcontent">
          <div class="head">
            <p class="ltitle">用户扫码排行</p>
          </div>
          <div class="content">
            <table id="userrank_data" class="table">
               <thead>
                    <tr>
                        <th>用户排名</th>
                        <th>微信昵称</th>
                        <th>用户ID</th>
                        <th>扫码次数</th>
                        <th>提现金额</th>
                        <th>积分</th>
                        <th>积分使用</th>
                    </tr>
                </thead>
              </table>
          </div>
          <textarea id="down_data" style="display:none"></textarea>
        </div>
      </div>
    </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
