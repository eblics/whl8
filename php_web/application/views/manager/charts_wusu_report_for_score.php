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
    background-color: #f5f5f5;
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
            <span class="title fleft">积分核对</span>
        </div>
        <div class="h20"></div>
        <div id="content">
            <div class="container">
                <!-- 筛选条件 -->
                <div class="rptmain" style="border:none">
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
                            <p class="ltitle">积分核对表</p>
                        </div>
                        <div class="content">
                            <table id="wusu_report_data" class="table">
                               <thead>
                                    <tr>
                                        <th width=100>产品</th>
                                        <th>策略</th>
                                        <th>累计激活瓶数</th>
                                        <th>累计激活积分</th>
                                        <th>单瓶成本</th>
                                        <th>已扫瓶数</th>
                                        <th>已扫积分</th>
                                        <th>扫码率</th>
                                        <th>已扫单瓶成本</th>
                                        <!-- <th>操作</th> -->
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
window.chartsType = '/wusu_score';
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
                url:'/charts/get_wusu_score_data',
                type:'GET',
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
                        if(data.strategyName!==null){
                            return '<div title="'+ data.strategyName +'">'+common.cutString(data.strategyName,8)+'</div>';
                        }else{
                            return '-';
                        }
                        
                    }
                },
                {"data":"totalCaps2","class":"center",
                    "render": function (data,type,row) {
                        return '<div>'+ data +'</div>';
                    }},
                {"data":"totalPoints2","class":"center",
                    "render": function (data,type,row) {
                        return '<div>'+ data +'</div>';
                    }},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div class="blue">'+ (data.totalPoints / data.totalCaps * 0.01).toFixed(2) +'元</div>';
                    }},
                {"data":"scanedCaps","class":"center",
                    "render": function (data,type,row) {
                        return '<div>'+ data +'</div>';
                    }},
                {"data":"scanedPoints","class":"center",
                    "render": function (data,type,row) {
                        return '<div>'+ data +'</div>';
                    }},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div class="blue">'+ (data.scanedCaps / data.totalCaps * 100).toFixed(2) +'%</div>';
                    }
                },
                {

                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if (data.scanedCaps === 0 || data.scanedCaps === '0') {
                            return '<div class="blue">没有扫码记录</div>';
                        }
                        return '<div class="blue">'+ (data.scanedPoints / data.scanedCaps * 0.01).toFixed(2) +'元</a></div>';
                    }
                },
                // {
                //     "data":null,"class":"center",
                //     "render": function (data,type,row) {
                //         return '<div><a class="btn-text noselect blue remove">移除</a></div>';
                //     }
                // }
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
                $(".remove").click(function(){
                    console.log($(this).parents());
                    $(this).parents('tr').remove();
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
        document.location.href =('/charts/wusu_report_for_score_download?param='+JSON.stringify(param));
    }
};
</script>
<?php include 'footer.php';?>
</body>
</html>