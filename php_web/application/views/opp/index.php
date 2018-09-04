<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/index.css" />
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/product_category.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.min.js"></script>
<script type="text/javascript" src="/static/js/index.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <div class="content">
        <div style="display:flex">
            <div id="moduleUsed" style="width:400px">
                <div class="title title-text">
                    红包使用
                    <span id="titleAmount">数量</span>
                    <span id="titleMoney" style="display:none;">
                    金额<span class="unit">单位（元）</span></span>
                </div>
                <div id="rpLabel"><div class="line"></div></div>
                <div style="position:relative">
                    <span id="usedPercent" class="center-info"></span>
                    <div id="chartUsed" style="height:248px;"></div>
                </div>
            </div>
            <div id="moduleMoney" style="width:800px">
                <div class="title title-text">
                    发放红包金额
                    <span class="unit">单位（元）</span>
                    <span class="amount" title="发放金额总数">0</span>
                    <span class="label-menu">

                    <!-- 任务166 Removed by shizq 
                    <span class="selected" day="7">最近一周</span>
                    <span day="31">最近一月</span> -->
                    <!-- Tast 166 end -->
                    

                    <!-- 任务166 Added by shizq -->
                    <input class="input Wdate" id="redpacket_date_start" value="" 
                        onfocus="WdatePicker({isShowWeek:true,minDate:'#F{$dp.$D(\'redpacket_date_start\',{M:-3});}',maxDate:'#F{$dp.$D(\'redpacket_date_end\');}'})" /> 
                    <input class="input Wdate" id="redpacket_date_end" value="" 
                        onfocus="WdatePicker({isShowWeek:true,minDate:'#F{$dp.$D(\'redpacket_date_start\',{M:-3});}',maxDate:'#F{$dp.$D(\'redpacket_date_end\');}'})" />
                    <button id="btn_fetch_redpacket_send">生成</button>
                    <button id="btn_export_redpacket_send">导出</button>
                    <!-- Tast 166 end -->
                    </span>
                </div>
                <div id="chartMoney" style="height:300px;"></div>
            </div>
        </div>
        <div class="split"></div>
        <div id="moduleScan">
            <div class="title">
                <span class="title-text">扫码记录</span>
                <span class="amount" title="扫码总数">0</span>
                <span class="label-menu">
                    
                    <!-- 任务166 Removed by shizq 
                    <span class="selected" day="7">最近一周</span>
                    <span day="31">最近一月</span> -->

                    <!-- 任务166 Added by shizq -->
                <input class="input Wdate" id="scan_date_start" value="" 
                    onfocus="WdatePicker({isShowWeek:true,minDate:'#F{$dp.$D(\'scan_date_start\',{M:-3});}',maxDate:'#F{$dp.$D(\'scan_date_end\');}'})" /> 
                <input class="input Wdate" id="scan_date_end" value="" 
                    onfocus="WdatePicker({isShowWeek:true,minDate:'#F{$dp.$D(\'scan_date_start\',{M:-3});}',maxDate:'#F{$dp.$D(\'scan_date_end\');}'})" />
                <button id="btn_fetch_scand">生成</button>
                <button id="btn_export_scand">导出</button>
                        <!-- Tast 166 end -->
                </span>
            </div>
            <div id="chartScan" style="height:350px;"></div>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
