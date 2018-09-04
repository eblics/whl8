$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            _this.formSubmit(rptbase.getValue());
            _this.search();
    },
    search:function(){
        var _this=this;
        //查询按钮请求数据
        $("#getSearch").click(function(){
            _this.formSubmit(rptbase.getValue());
        })
    },
    formSubmit:function(param){
        var _this=this;
        common.loading();
        $.post(common.getRptRootUrl() + 'charts/period_get_data',{param: param},function(data){
            common.unloading();
            $("#getDown").show();
            _this.createLine(rptbase.StringToArray(data["string"]));
            _this.createTable(data["data"]);
            $("#down_data").val(JSON.stringify(data["data"]));
        });
        rptbase.getDown(common.getRptRootUrl() + 'charts/down_period_date',JSON.stringify(param));
    },
    createLine:function(data){
        var myChart = echarts.init(document.getElementById('main'));
        option = {
                title: {
                    show:false
                },
                tooltip : {
                    show:true,
                    trigger:'axis',
                    axisPointer:{
                        lineStyle:{
                            color:'#f9320c'
                        }
                    }
                },
                legend: {
                    data:['扫码量'],
                    top:'bottom',
                    itemGap: 50,
                    align:'left',
                    selectedMode:false,
                    textStyle:{
                        fontSize:'16'
                    }
                },
                toolbox: {
                    show:false
                },
                grid: {
                    top:'60',
                    left: '3%',
                    right: '4%',
                    padding:'10',
                    borderColor:'#ccc',
                    containLabel: true
                },
                xAxis : [
                        {
                            type : 'category',
                            boundaryGap : false,
                            axisLine:{show:false},
                            axisTick:{show:false},
                            splitLine:{
                                show: false
                            },
                            axisLabel: {
                                 show: true,
                                 textStyle: {
                                     color: '#999'
                                 }
                             },
                            boundaryGap : false,
                           data : ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00']
                        }
                    ],
                yAxis : [
                        {
                            type : 'value',
                            axisLine:{show:false},
                            axisTick:{show:false},
                            splitLine:{
                                show: true,
                                color:'#999'
                            },
                            axisLabel: {
                                 show: true,
                                 textStyle: {
                                     color: '#999'
                                 }
                             }
                        }
                ],
                series : [
                    {
                        name:'扫码量',
                        type:'line',
                        stack: '总量',
                        symbol:'emptyCircle',
                        symbolSize:[10,10],
                        showAllSymbol:true,
                        smooth:true,
                        label: {
                            normal: {
                                show: false,
                                position: 'bottom',
                                textStyle:{
                                    color:'#3384d5',
                                    fontStyle:'italic'
                                },
                            }
                        },
                        itemStyle: {normal: {
                            color:'#63cdef'
                        }},
                        areaStyle: {normal: {
                            type: 'default'
                        }},
                        data:data
                    }
                ]
            };
        myChart.setOption(option);
    },
    createTable:function(data){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     false,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "dom":'tip',
            "paging": true,
            "pageLength": 12,
            "data": data,
            "columns": [
                        {"data":"time","class":"center"},
                        {"data":"scanNum","class":"center"}
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
            }
        };

        this.table=$("#period_data").dataTable(config);
    }
};
