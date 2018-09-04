<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_region.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/charts_base.js"></script>
<script type="text/javascript" src="/static/js/charts_region.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
<script type="text/javascript" src="/static/echarts/china.js"></script>
<script type="text/javascript" src="/static/echarts/china-main-city-map.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">区域分布统计</span>
        </div>
        <div class="h30"></div>
        <div class="container">
        
<!-- 筛选条件 -->
                <div class="rptmain" style="border:none">
                    <div class="tool">
                        <!-- 产品筛选框 -->
                        <p class="ltitle">产品</p>
                        <div class="rtitle">
                            <ul>
                                <select class="select select2" id="productid" name="productid">
                                    <option value="0">全部</option>
                                </select>
                            </ul>
                        </div>
                        <!-- 乐码批次筛选框 -->
                        <p class="ltitle">乐码批次</p>
                        <div class="rtitle">
                            <ul>
                                <select class="select select2" id="batchid" name="batchid">
                                    <option value="0">全部</option>
                                </select>
                            </ul>
                        </div>

                    </div>

                    <div class="tool">

                        <!-- 乐码批次筛选框 -->
                        <p class="ltitle">扫码时间</p>
                        <div class="rtitle">
                            <ul id="datepicker_container" class="datepicker-container">
                                <input class="input Wdate" id="start_time" name="startTime" value="<?=date("Y-m-d", strtotime("-1 months", time()))?>" readonly/>
                                 - 
                                <input class="input Wdate" id="end_time" name="endTime" value="<?=date('Y-m-d')?>" readonly/>
                            </ul>
                        </div>

                        <div id="getSearch" class="btn btn-blue">查询</div>
                        <div id="getDown" class="btn btn-blue" style="display: inline-block;">下载</div>
                        <span id="get_daily_down" style="display: inline;">
                          <input id="is_daily" name="is_daily" type="checkbox" value="0"> 下载日扫码数据
                        </span>

                    </div> 
                    <div class="line"></div>

                </div> <!-- 筛选条件 end -->



        <div class="rptmain">
        <div class="rptcontent">
          <!-- 数据汇总开始 -->
          <div class="re_huoyuedu" style="height:100%;">
            <div class="head">
              <p class="ltitle">数据汇总</p>
            </div>
            <div class="data_pool">
              <table class="pool_table">
                <tr>
                  <td>
                    <span class="data_num c_1" id="city">-</span><br>
                    <div class="ph">
                    </div>
                    <span class="text">扫码城市</span>
                  </td>
                  <td class="hr">
                  </td>
                  <td>
                    <span class="data_num c_2" id="red_city">-</span><br>
                    <div class="ph">
                    </div>
                    <span class="text">红包城市</span>
                  </td>
                  <td style="display:none" class="hr">
                  </td>
                  <td style="display:none">
                    <span class="data_num c_3" id="scan_all">-</span><br>
                    <div class="ph">
                    </div>
                    <span class="text">扫码次数</span>
                  </td>
                  <td class="hr">
                  </td>
                  <td>
                    <span class="data_num c_3" id="scan_num">-</span><br>
                    <div class="ph">
                    </div>
                    <span class="text">位置获取量</span>
                  </td>
                  <td class="hr">
                  </td>
                  <td>
                    <span class="data_num c_4" id="gps_per">-</span><br>
                    <div class="ph">
                    </div>
                    <span class="text">位置获取率</span>
                  </td>
                </tr>
              </table>
              <div class="line" style="width: 80%;margin: 35px auto;"></div>
              <table class="pool_table">
                <tr>
                  <td align="center">
                     <div class="region_card red">
                        <p class="num_1 red_total">0</p>
                        <p class="txt">红包总额</p>
                        <p style="height:30px;"></p>
                        <p class="num_2 red_total_none">0</p>
                        <p class="txt">无位置红包额</p>
                     </div>
                  </td>
                  <td align="center">
                    <div class="region_card point">
                        <p class="num_1 point_total">0</p>
                        <p class="txt">积分总量</p>
                        <p style="height:30px;"></p>
                        <p class="num_2 point_total_none">0</p>
                        <p class="txt">无位置积分量</p>
                     </div>
                  </td>
                  <td align="center">
                    <div class="region_card scan">
                        <p class="num_1 scan_total">0</p>
                        <p class="txt">扫码总量</p>
                        <p style="height:30px;"></p>
                        <p class="num_2 scan_total_none">0</p>
                        <p class="txt">无位置扫码量</p>
                     </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <!-- 数据汇总结束 -->
          <!-- 地域分布开始 -->
          <div class="re_map_area">
            <div class="head">
              <p class="ltitle">地域分布</p>
            </div>
            <div style="clear:both"></div>
            <div class="content">
                <div id="main" style="width: 45%;min-height:550px;float:left;"></div>
                <div id="main2" style="width: 55%;min-height:550px;float:left;"></div>
                <div id="main3" style="width: 55%;min-height:550px;float:left;"></div>
            </div>
          <div style="clear:both"></div>
          </div>
          <!-- 地域分布结束 -->
          <!-- 地域统计量开始 -->
          <div class="area_count">
          <div class="head">
            <p class="ltitle">地域统计量</p>
          </div>
          <div style="width:100%;">
              <div style="width:50%;float:left">
              <div style="clear:both"></div>
              <table id="sf_data" class="table">
               <thead>
                  <tr>
                      <th>省份</th>        
                      <th>扫码次数</th>
                      <th>参与人数</th>
                      <th>红包金额（元）</th>
                      <th>积分</th>
                  </tr>
                </thead>
              </table>
              </div>
              <div style="width:50%;float:left">
                <table id="sq_data" class="table">
                  <thead>
                    <tr>
                        <th>城市</th>        
                        <th>扫码次数</th>
                        <th>参与人数</th>
                        <th>红包金额（元）</th>
                        <th>积分</th>
                    </tr>
                  </thead>
                </table>
                <div style="clear:both"></div>
              </div>
          </div>
       </div>
          <!-- 地域统计量结束 -->
        </div>
      </div>
    </div>
    </div>
</div>
<script>
var dataurl = 'charts/get_table_pro_data/';
var dataurl2 = 'charts/get_table_city_data/';
var myChart = echarts.init(document.getElementById('main'));
var myChart2 = echarts.init(document.getElementById('main2'));
var myChart3 = echarts.init(document.getElementById('main3'));
// 定义数据源开关
var sourceSwitch = 1;// 0 = 实时数据库  1 = 本地存储优先
var nowDate = "<?=date('Y-m-d')?>";
var mchId = "<?=$_SESSION['mchId']?>";
var ENV = "<?=$_SERVER['CI_ENV']?>";
</script>

<?php include 'footer.php';?>
</body>
</html>