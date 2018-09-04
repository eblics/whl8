$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            _this.formSubmit(rptbase.getValue());
            _this.search();
            _this.lableMenu(); 
    },
    search:function(){
        var _this=this;
        //查询按钮请求数据
        $("#getSearch").click(function(){
            _this.formSubmit(rptbase.getValue());
        })
    },
    lableMenu:function(){
        var _this=this;
        //按照周/天
        $(".label-menu span").click(function(){
            if ($(this).hasClass('selected')) {
                return false;
            }
            var level=$(this).attr('data-level');
            $(".label-menu span").removeClass("selected");
            $(this).addClass("selected");
            
            _this.formSubmit(rptbase.getValue());
        })
    },
    formSubmit:function(param){
        var _this=this;
        common.loading();
        $.post(common.getRptRootUrl()+'charts/get_business_data',{param:param},function(data){
            $("#getDown").show();
            _this.createLine(data);
            _this.createTable(data);
            $("#down_data").val(JSON.stringify(data));
        });
        rptbase.getDown(common.getRptRootUrl()+'charts/down_business_date',JSON.stringify(param));
    },
    createLine:function(data){
        var theDate = [];
        var scanNum = [];
        var redNum = [];
        var transNum = [];
        var cardNum = [];
        var pointAmount = [];
        var pointNum = [];
        for(i=0;i<data.length;i++){
            theDate.push(data[i].theDate);
            scanNum.push(data[i].scanNum);
            redNum.push(data[i].red_amount);
            transNum.push(data[i].trans_amount);
            cardNum.push(data[i].card_num);
            pointAmount.push(data[i].point_amount);
            pointNum.push(data[i].point_num);
        }
        var myChart = echarts.init(document.getElementById('main'));
            option = {
                    title : {
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
                        data:['扫码量','红包','提现','乐券','积分','积分使用'],
                        top:'bottom',
                        itemGap: 50,
                        align:'left',
                        selectedMode:true,
                        textStyle:{
                            fontSize:'16'
                        }
                    },
                    toolbox: {
                        show : false,
                    },
                    grid: {
                        top:'60',
                        left: '3%',
                        right: '4%',
                        padding:'10',
                        borderColor:'#F0FFF0',
                        containLabel: true
                    },
                    calculable : false,
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
                            data : theDate
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
                    dataZoom: [{
                        type: 'slider',
                        show: false,
                        start: 0,
                        end: 100,
                        handleSize: 8
                    },{
                        type: 'inside',
                        start: 94,
                        end: 100
                    }],
                    series : [
                        {
                            name:'扫码量',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#63cdef'
                            }},
                            areaStyle: {normal: {
                                type: 'default'
                            }},
                            data:scanNum
                        },
                        {
                            name:'红包',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#ff4e50'
                            }},
                            areaStyle: {normal: {
                                type: 'default',
                                opacity:0.3
                            }},
                            data:redNum
                        },
                        {
                            name:'提现',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#ff950b'
                            }},
                            areaStyle: {normal: {
                                type: 'default',
                                opacity:0.2
                            }},
                            data:transNum
                        },
                        {
                            name:'乐券',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#47c8b2'
                            }},
                            areaStyle: {normal: {
                                type: 'default',
                                opacity:0.3
                            }},
                            data:cardNum
                        },
                        {
                            name:'积分',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#fed875'
                            }},
                            areaStyle: {normal: {
                                type: 'default',
                                opacity:0.3
                            }},
                            data:pointAmount
                        },
                        {
                            name:'积分使用',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#14b8d4'
                            }},
                            areaStyle: {normal: {
                                type: 'default',
                                opacity:0.3
                            }},
                            data:pointNum
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
            "data": data,
            "columns": [
                        {"data":"theDate","class":"center"},
                        {"data":"scanNum","class":"center"},
                        {"data":"red_amount","class":"center"},
                        {"data":"trans_amount","class":"center"},
                        {"data":"card_num","class":"center"},
                        {"data":"point_amount","class":"center"},
                        {"data":"point_num","class":"center"}
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            }
        };

        this.table=$("#business_data").dataTable(config);
    }
};