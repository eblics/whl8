<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/index.css" />
<link type="text/css" rel="stylesheet" href="/static/libs/swiper/css/swiper.min.css"/>
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/product_category.js"></script>
<script type="text/javascript" src="/static/libs/swiper/js/swiper.jquery.min.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/js/charts_circle.js"></script>
<script type="text/javascript" src="/static/js/index.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
</head>
<body>
<?php include 'header_center.php';?>
<!-- 幻灯片开始 -->
<div class="bat_tip" style="display: none"><?=$tip?></div>
<div class="Swiper_content">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide banner_1"></div>
            <div class="swiper-slide banner_2""></div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
<!-- 幻灯片结束 -->
<!-- 模块开始 -->
<div class="dh_content">
	<div class="main">
		<div class="container">
			<a href="/product/category">
				<div class="con_1">
					<p><img src="/static/images/manager.png"></p>
					<p><span class="title">管理中心</span></p>
					<p><span class="desc">快速便捷的管理产品，完善的活动推广方案</span></p>
				</div>
			</a>
		</div>
		<div class="container">
			<a href="/charts/index">
				<div class="con_1">
					<p><img src="/static/images/datacenter.png"></p>
					<p><span class="title">数据中心</span></p>
					<p><span class="desc">掌握消费者信息，实现精准快速营销</span></p>
				</div>
			</a>
		</div>
		<div class="container">
			<a href="/service/help_read">
				<div class="con_1">
					<p><img src="/static/images/help.png"></p>
					<p><span class="title">帮助中心</span></p>
					<p><span class="desc">提供业务常见问题</span></p>
				</div>
			</a>
		</div>
	</div>
</div>
<!-- 模块结束 -->
<!-- 奖品发放模块 -->
<div class="main" style="padding:65px 0;">
    <div class="content">
<!-- 奖品发放情况开始 -->
<p class="c_title">奖品发放情况</p>
<p style="color:#5c5c5c">------------------------------------------------</p>
<p class="sub_c_title">红包、卡券、积分</p>
<br><br><br>
<div style="display:flex">
            <!-- 红包 -->
            <div id="redUsed" style="width: 33%;">
                <div class="title title-text">
                    <p class="sub_title">
                      <img src="/static/images/index_c_1.png">已发红包总额：
                      <span id="yf_rednum">-</span>
                    </p><br>
                    <p class="sub_title">
                        <img src="/static/images/index_c_2.png">各类红包发放<t id="limitName"></t>如下<small id="limitUnit"></small>：
                    </p>
                </div>
                <div id="redLabel">
                <div class="toolgroup">
                    <div class="tool_prev">
                        <span class="red_prev" data-class="redList"></span>
                    </div>
                    <div class="tool_next">
                        <span class="red_next" data-class="redList"></span>
                    </div>
                </div>
                <div id="redTool">
                  <ul class="redList_piclist redList"></ul>
                </div>
                </div>
                <div class="panel_con" style="position:relative">
                    <span id="redPercent" class="center-info"></span>
                    <canvas id="redPanel">您的浏览器不支持canvas标签，建议使用chrome,firefox,ie10+</canvas>
                    <span class="panel_title">红 包</span>
                    <span class="red_center-name"></span>
                </div>
            </div>
            <div style="float:left;margin-top:30px;width: 1px;height: 390px; border-right:1px solid #e0e0e0"></div> 
            <!-- 卡券 -->
            <div id="cardUsed" style="width: 33%;">
                <div class="title title-text">
                    <p class="sub_title">
                      <img src="/static/images/index_c_1.png">已发卡券种类：
                      <span id="yf_cardnum">-</span>
                    </p><br>
                    <p class="sub_title">
                        <img style="margin-right:12px" src="/static/images/index_c_2.png">各类卡券发放数量如下<small>（张）：</small>
                    </p>
                </div>
                <div id="cardLabel">
                <div class="toolgroup">
                    <div class="tool_prev">
                        <span class="card_prev" data-class="cardList"></span>
                    </div>
                    <div class="tool_next">
                        <span class="card_next" data-class="cardList"></span>
                    </div>
                </div>
                <div id="cardTool">
                  <ul class="cardList_piclist cardList"></ul>
                </div>
                </div>
                <div class="panel_con">
                    <span id="cardPercent" class="center-info"></span>
                    <canvas id="cardPanel">您的浏览器不支持canvas标签，建议使用chrome,firefox,ie10+</canvas>
                    <span class="panel_title">卡 券</span>
                    <span class="card_center-name"></span>
                </div>
            </div>
            <div style="float:left;margin-top:30px;width: 1px;height: 390px; border-right:1px solid #e0e0e0"></div> 
            <!-- 积分 -->
            <div id="pointUsed" style="width: 33%;">
                <div class="title title-text">
                    <p class="sub_title">
                      <img src="/static/images/index_c_1.png">已发积分：
                      <span id="yf_pointnum">-</span>
                      </p><br>
                    <p class="sub_title">
                        <img style="margin-right:12px" src="/static/images/index_c_2.png">各类积分使用情况如下<small>（积分）：</small>
                    </p>
                </div>
                <div id="pointLabel">
                <div class="toolgroup">
                    <div class="tool_prev">
                        <span class="point_prev" data-class="pointList"></span>
                    </div>
                    <div class="tool_next">
                        <span class="point_next" data-class="pointList"></span>
                    </div>
                </div>
                <div id="pointTool">
                  <ul class="pointList_piclist pointList"></ul>
                </div>
                </div>
                <div class="panel_con">
                    <span id="pointPercent" class="center-info"></span>
                    <canvas id="pointPanel">您的浏览器不支持canvas标签，建议使用chrome,firefox,ie10+</canvas>
                    <span class="panel_title">积 分</span>
                    <span class="point_center-name"></span>
                </div>
            </div>
        </div>
</div>
</div>
<div style="border-bottom: 1px dashed #ccc;height:1px;width:100%"></div>
<!-- 营销分析模块 -->
<div class="main" style="padding:65px 0;">
<div class="content">
<!-- 奖品发放情况开始 -->
<p class="c_title">营销分析</p>
<p style="color:#5c5c5c">--------------------------------------------------------</p>
<p class="sub_c_title">用户行为、增长趋势分析</p>
<br><br><br>

<div style="display:flex">
<!-- 营销分析左边板块 -->
<div class="clow" style="width:40%;height:400px">
<div class="title title-text">
    <p class="cl_title">
        <img style="margin-right:12px" src="/static/images/index_c_3.png"><a href="/charts/userscan" target="_blank">用户行为分析</a>
    </p>
    <br>
    <p class="cl_scannum">总扫码量</p>
    <p id="ys_scanNum" style="font-size: 45px;color:#30b4b2">0</p>
    <hr style="height:1px;border:none;border-top:1px dashed #5c5c5c;margin: 20px 0;">
    <p class="cl_totalscan"> 总扫码率</p>
    <!-- 进度条 -->
    <p>
        <div class="progress_bar">
          <span id="ys_sweep" class="progress_sweep"></span>
        </div>
    </p>
</div>
</div>
<div style="float:left;margin-top:30px;width: 1px;height: 310px; border-right:1px solid #e0e0e0"></div> 
<!-- 营销分析右边板块 -->
<div class="clow" style="width:60%;height:400px">
<div class="title title-text">
    <p class="cl_title">
        <img style="margin-right:12px" src="/static/images/index_c_4.png">用户增长趋势分析
    </p>
    <div id="main" style="width:100%;height:350px;margin-top:-20px"></div>
</div>
</div>
</div>
</div>
</div>
<!-- 结束 -->
<?php include 'footer.php';?>
<script>
var myChart = echarts.init(document.getElementById('main'));
</script>
</body>
</html>
