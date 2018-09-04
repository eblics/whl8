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
<script type="text/javascript" src="/static/js/charts_userscan.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">用户扫码统计</span>
            <?php 
              $mchId=$this->session->userdata('mchId');
              $arr=array('0','112','119','126','167','169','171');//贝奇专用
              if(in_array($mchId, $arr)){
                if(get_current_router(1)=='charts' && in_array(get_current_router(2),['userscan','index'])){
                  echo '<span style="color:red">(温馨提示：扫码详细数据暂仅支持按周下载)</span>';
                }
              }
              ?>
        </div>
        <div class="h30"></div>
        <div class="container">
        <?php include 'charts_menu.php';?>
        <div class="rptmain">
        <div class="rptcontent">
          <div class="head">
            <p class="ltitle">用户扫码</p>
            <div class="rtitle">
                <div class="title" id="tool" style="margin:0">
                  <span class="title-text">&nbsp;&nbsp;</span>
                  <span class="label-menu">
                    <span class="selected" data-level="day">按天</span>
                    <span data-level="week" >按周</span>
                  </span>
                </div>
            </div>
          </div>
          <div class="content">
            <div id="main" style="width:100%;height:400px;"></div>
            <div class="h50" style="clear:both"></div>
            <hr style="height:1px;border:none;border-top:1px solid #e0e0e0;margin:0px">
            <textarea id="down_data" style="display:none"></textarea>
            <div class="content">
              <table id="userscan_data" class="table">
               <thead>
                    <tr>
                        <th>用户ID</th>
                        <th>微信昵称</th>
                        <th>扫码时间</th>
                        <th>扫码次数</th>
                        <th>红包金额（元）</th>
                        <th>提现金额（元）</th>
                        <th>乐券数量（张）</th>
                        <th>积分</th>
                        <th>积分使用</th>
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
