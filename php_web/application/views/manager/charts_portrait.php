<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_portrait.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/charts_portrait.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">消费者画像</span>
            <span class="movetitle">限时免费</span>
        </div>
        <div class="h30"></div>
        <div class="container">
          <!-- 报表公用筛选菜单 -->
        <div class="rptmain" style="border:none">
        <!-- 省市区开始 -->
        <div class="rptcontent">
        <div class="tool">
            <p class="ltitle">省份</p>
            <div class="rtitle">
                <ul>
                    <select id="proCode" class="select select2" name="proCode">
                      <option value='0'>全国</option>
                      <?php foreach ($data['data'] as $pro):?>
                      <?php if($pro->code!=='710000'&&$pro->code!=='810000'&&$pro->code!=='820000'){?>
                        <option value="<?php echo $pro->code;?>" class="txtiundefined"><?php echo $pro->name;?></option>
                      <?php }?>
                      <?php endforeach;?>            
                    </select>
                </ul>
            </div>
            <p class="ltitle">城市</p>
            <div class="rtitle">
                <ul>
                    <select style="margin-left: 20px;" id="cityCode" class="select select2" name="cityCode">
                        <option value="0">全部</option>
                      </select>
                </ul>
            </div>
            <p class="ltitle">区县</p>
            <div class="rtitle">
                <ul>
                    <select style="margin-left: 20px;" id="areaCode" class="select select2" name="areaCode">
                        <option value="0">全部</option>
                      </select>
                </ul>
            </div>
        </div>
        <!-- 年龄性别星座开始 -->
        <div class="tool">
            <p class="ltitle">年龄</p>
            <div class="rtitle">
                <ul>
                    <select id="age" class="select select2" name="age">
                      <option value="0">全部</option>
                      <option value="0-6">0-6岁</option>
                      <option value="7-15">7-15岁</option>
                      <option value="16-20">16-20岁</option>
                      <option value="21-25">21-25岁</option>
                      <option value="26-30">26-30岁</option>
                      <option value="31-35">31-35岁</option>
                      <option value="36-40">36-40岁</option>
                      <option value="41-55">41-55岁</option>
                      <option value="56-70">56-70岁</option>
                      <option value="71-100">71-100岁</option>                       
                    </select>
                </ul>
            </div>
            <p class="ltitle">性别</p>
            <div class="rtitle">
                <ul>
                    <select style="margin-left: 20px;" id="sex" class="select select2" name="sex">
                        <option value="0">全部</option>
                        <option value="1">男生</option>
                        <option value="2">女生</option>
                      </select>
                </ul>
            </div>
            <p class="ltitle">星座</p>
            <div class="rtitle">
                <ul>
                    <select style="margin-left: 20px;" id="constellation" class="select select2" name="constellation">
                        <option value="0">全部</option>
                        <option value="水瓶座">水瓶座</option>
                        <option value="双鱼座">双鱼座</option>
                        <option value="白羊座">白羊座</option>
                        <option value="金牛座">金牛座</option>
                        <option value="双子座">双子座</option>
                        <option value="巨蟹座">巨蟹座</option>
                        <option value="狮子座">狮子座</option>
                        <option value="处女座">处女座</option>
                        <option value="天秤座">天秤座</option>
                        <option value="天蝎座">天蝎座</option>
                        <option value="射手座">射手座</option>
                        <option value="摩羯座">摩羯座</option>
                      </select>
                </ul>
            </div>
        </div>
        <!-- 日消费-消费时段 -->
        <div class="tool">
            <p class="ltitle">消费时段</p>
            <div class="rtitle">
                <ul>
                    <select style="margin-left: 20px;" id="time" class="select select2" name="time">
                        <option value="0">全部</option>
                        <option value="00:00-03:00">00:00-03:00</option>
                        <option value="03:00-06:00">03:00-06:00</option>
                        <option value="06:00-09:00">06:00-09:00</option>
                        <option value="09:00-12:00">09:00-12:00</option>
                        <option value="12:00-15:00">12:00-15:00</option>
                        <option value="15:00-18:00">15:00-18:00</option>
                        <option value="18:00-21:00">18:00-21:00</option>
                        <option value="21:00-24:00">21:00-24:00</option>
                      </select>
                </ul>
            </div>
            <div id="getSearch" class="btn btn-blue">查询</div>
        </div>
        </div>
        </div>
        <!-- 报表公用筛选菜单 -->
        <div class="h10" style="clear:both"></div>
        <div style="border-top:1px solid #e0e0e0;"></div>
        <div class="h20" style="clear:both"></div>       
          <div class="rptmain">
            <div class="rptcontent">
              <div class="head">
                <p class="ltitle">消费者消费占比</p>
              </div>
              <div class="content">
              <div class="h50" style="clear:both"></div>
              <div id="portraitContent"></div>
              
                



              <div class="h30" style="clear:both"></div>
              <div style="border-top:1px dashed #e0e0e0;width:95%;margin:0 auto"></div>
              <div class="h50" style="clear:both"></div>
              <div class="pieContent">
                 <div id="main" style="width:50%;height:400px;"></div>
                 <div id="pieHtml"></div>
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