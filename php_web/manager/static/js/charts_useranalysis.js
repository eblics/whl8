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
        console.log(param);
        var _this=this;
        common.loading();
        var params = {"param": param};
        $.post(common.getRptRootUrl() + 'charts/get_useranalysis_data', params, function(data) {
            $("#getDown").show();
            _this.createLine(data);
            _this.createTable(data);
            $("#down_data").val(JSON.stringify(data));
        });
        rptbase.getDown(common.getRptRootUrl() + 'charts/down_useranalysis_date',JSON.stringify(param));
    },
    createLine:function(data){
        //循环处理数据
        var xData=[];
        var newData=[];
        var oldData=[];
        for(var i=0;i<data.length;i++){
            xData.push(data[i]['theDate']);
            newData.push(data[i]['newScan']);
            oldData.push(data[i]['oldScan']);
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
                    data:['新用户','老用户'],
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
                        data : xData
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
                        name:'新用户',
                        type:'line',
                        smooth:false,
                        symbolSize:[6,6],
                        showAllSymbol:true,
                        legendHoverLink:true,
                        itemStyle: {normal: {
                            color:'#ff4e50'
                        }},
                        data:newData
                    },
                    {
                        name:'老用户',
                        type:'line',
                        smooth:false,
                        symbolSize:[6,6],
                        showAllSymbol:true,
                        legendHoverLink:true,
                        itemStyle: {normal: {
                            color:'#ff950b'
                        }},
                        data:oldData
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
                        {"data":"newScan","class":"center"},
                        {"data":"oldScan","class":"center"}
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            }
        };

        this.table=$("#useranalysis_data").dataTable(config);
    }
};
