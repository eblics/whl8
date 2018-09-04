<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/charts_base.js"></script>
<style>
.datepicker-container input {
    border: 1px solid #ccc;
    color: #555;
    width: 140px;
    border-radius: 3px;
    padding: 5px 10px;
    overflow: hidden;
    background-position:98% 50%;
}
.top_title {
    display: block;
    text-align: left;
    font-size: 16px;
    padding: 6px;
    background-color: #f5f5f5;
    margin: 3px 0;
}
.table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
    padding: 0;
}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_charts.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">二维码瓶盖查询</span>
        </div>
        <div class="h20"></div>
        <div id="content">
            <div class="container">
                <!-- 筛选条件 -->
                <div class="rptmain" style="border:none;min-width: 1028px">
                    <div class="tool">
                        <!-- 产品筛选框 -->
                        <p class="ltitle">产品</p>
                        <div class="rtitle">
                            <ul>
                                <select class="select select2" id="productid" name="productId" style="width:128px;">
                                    <option value="0">全部</option>
                                </select>
                            </ul>
                        </div>
                        <!-- 乐码批次筛选框 -->
                        <p class="ltitle">乐码批次</p>
                        <div class="rtitle">
                            <ul>
                                <select class="select select2" id="batchid" name="batchId" style="width:128px;">
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
                                <input class="input Wdate" id="start_time" name="startTime" value="<?=$minDate?>" readonly/>
                                -
                                <input class="input Wdate" id="end_time" name="endTime" value="<?=date('Y-m-d', strtotime('-1 day'))?>" readonly/>
                            </ul>
                        </div>

                        <div id="btn_search" class="btn btn-blue">查询</div>
                        <div id="btn_download" class="btn btn-blue">下载</div>
                    </div>
                </div> <!-- 筛选条件 end -->
                <div class="rptmain">
                    <div class="rptcontent">
                        <div class="head">
                            <p class="ltitle">二维码瓶盖查询</p>
                        </div>
                        <div class="content">
                            <table id="wusu_report_data" class="table">
                               <thead>
                                    <tr>
                                        <th width=100>产品</th>
                                        <th>活动名称</th>
                                        <th>活动地点</th>
                                        <th>策略类型</th>
                                        <th>策略内容</th>
                                        <th>乐码批次</th>
                                        <th>瓶盖采购数</th>
                                        <th>瓶盖激活数</th>
                                        <th>总积分</th>
                                        <th>已扫积分</th>
                                        <th>已扫瓶盖数</th>
                                        <th>扫码地区</th>
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
<script>
window.chartsType = '/wusu_code';
$(function(){
    Init.init();
});
var Init = {
    init:function(){
        var _this=this;
        _this.bindTime();
        _this.formSubmit(_this.getValue());
        //查询提交
        $("#btn_search").click(function(res){
            _this.formSubmit(_this.getValue());
        });
        //下载
        $("#btn_download").click(function(res){
            _this.download();
        })
    },
    formSubmit:function(param){
        var _this=this;
        _this.createTable(param);
    },
    bindTime:function(){
        $('#start_time').focus(function() {
            WdatePicker({
                lang:'zh-cn',
                dateFmt: 'yyyy-MM-dd',
            });
        });

        $('#end_time').focus(function() {
            WdatePicker({
                lang:'zh-cn',
                dateFmt: 'yyyy-MM-dd'
            });
        });
    },
    //获取表单参数值
    getValue:function(){
        var param={
            productId:$("#productid").val(),
            batchId:$("#batchid").val(),
            startTime:$("#start_time").val(),
            endTime:$("#end_time").val()
        };
        return param;
    },
    createTable:function(params){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "lengthChange": false,
            "serverSide":true,//开启服务器分页
            "deferRender": true,
            "ajax":{
                url:'/charts/get_wusu_code_data',
                type:'POST',
                data:params!=undefined?params:{},
            },
            "columns": [
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.productName!==null){
                            return '<div title="'+ data.productName +'">'+common.cutString(data.productName,8)+'</div>';
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.activityName!==null){
                            return '<div title="'+ data.activityName +'">'+common.cutString(data.activityName,8)+'</div>';
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.areaName!==null){
                            return '<div title="'+ data.areaName +'">'+common.cutString(data.areaName,8)+'</div>';
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.strategyLevel!==null){
                            return '<div>'+common.cutString(data.strategyLevel,8)+'</div>';
                        }else{
                            return '-';
                        }
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.strategyName!==null){
                            return '<div title="'+ data.strategyName +'">'+common.cutString(data.strategyName,8)+'</div>';
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.batchNo!==null){
                            return '<div title="'+ data.batchNo +'">'+common.cutString(data.batchNo,8)+'</div>';
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.codeCount!==null){
                            return '<div>'+data.codeCount+'</div>';;
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.capsCount!==null){
                            return '<div>'+data.capsCount2+'</div>';;
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.pointsCount!==null){
                            return '<div>'+data.pointsCount2+'</div>';;
                        }else{
                            return '0';
                        }
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.pointsNum!==null){
                            return '<div>'+data.pointsNum+'</div>';;
                        }else{
                            return '0';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.scanNum!==null){
                            return '<div>'+data.scanNum+'</div>';;
                        }else{
                            return '0';
                        }
                        
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div class="blue"><a class="scanarea" href="javascript:;" data-id="'+ data.batchId +'">查看</a></div>';
                    }
                }
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();

                $('#wusu_report_data tbody td .totalScore').off('click').on("click",function(){
                    var _this=$(this);
                    var activityId=$(this).attr('data-activityId');
                    _this.html('<img src="/static/images/loading-mini.gif" />');
                    if(parseInt(activityId)>0){
                        $.post('/charts/get_totalScore_by_activityId',{activityId:activityId},function(res){
                            _this.html(res);
                        })
                    }
                });
                $('#wusu_report_data .get_score_all').off('click').on("click",function(){
                    clearInterval(window.getScanT);
                    window.getScanNums=$('.totalScore').length;
                    window.getScanCount=0;
                    window.getScanT=setInterval(function(){
                        if(window.getScanCount>=window.getScanNums-1) clearInterval(window.getScanT);
                        $('.totalScore').eq(window.getScanCount).click();
                        window.getScanCount++;
                    },600);
                });
                //查询区域相关信息
                $(".scanarea").click(function(){
                    // var param=Init.getValue();
                    // param.scanArea=$(this).attr('data-scanarea');
                    var param = {
                        "batch_id": $(this).attr('data-id'),
                        "date_time": $("#start_time").val() + ',' + $("#end_time").val(),
                    };
                    common.loading();
                    $.get('/charts/get_scanarea_info/charts',param,function(res){
                        common.unloading();
                        common.transDialog(function(callback) {
                            var html = '<div class="top_title"><b>扫码地区详情</b></div>';
                            html += '<div class="box">';
                            html += '    <table id="point_trans_log_table">';
                            html += '        <thead><tr>';
                            html += '            <th>扫码区域</th><th>详细地区</th><th>扫瓶盖次数</th><th>积分发放数</th>';
                            html += '        </tr></thead>';
                            html += '    </table>';
                            html += '<div>';
                            callback(html);
                        });
                        var table = $('#point_trans_log_table').DataTable({
                            "info": false,
                            "paging": true,
                            "searching": false,
                            "ordering":  false,
                            "lengthChange": false,
                            "language": {"url": "/static/datatables/js/dataTables.language.js"},
                            "columns": [
                                {"data":"name","class":"center"},
                                {"data":"fullName","class":"center"},
                                {
                                    "data":null,"class":"center",
                                    "render": function (data,type,row) {
                                        if(data.scanNum!=null){
                                            return '<div>'+data.scanNum +'</div>';
                                        }else{
                                            return '0';
                                        }
                                    }
                                },
                                {
                                    "data":null,"class":"center",
                                    "render": function (data,type,row) {
                                        if(data.pointsNum!=null){
                                            return '<div>'+data.pointsNum +'</div>';
                                        }else{
                                            return '0';
                                        }
                                    }
                                }
                            ]
                        });
                        table.clear();
                        table.rows.add(res);
                        table.draw();
                    },'json')
                })



            },
            "preDrawCallback": function() {
                common.loading();
            }
        };

        this.table=$('#wusu_report_data').dataTable(config);
    },
    //下载
    download:function () {
        var param = this.getValue();
        document.location.href =('/charts/get_wusu_code_data_download?param='+JSON.stringify(param));
    }
};
</script>
<?php include 'footer.php';?>
</body>
</html>