var index = {

    init: function() {        
        var chartUsed = echarts.init(document.getElementById('chartUsed'));
        var chartScan = echarts.init(document.getElementById('chartScan'));
        var chartMoney = echarts.init(document.getElementById('chartMoney'));

        this.chartMoney = chartMoney;
        this.chartScan = chartScan;

        // 指定图表的配置项和数据
        var optionUsed = {
            legend:{
                top: 'bottom',
                itemGap:30,
                itemHeight:16,
                itemWidth:16,
                data: [
                       {name:'已发',icon:'image://static/echarts/yifa.png'},
                       {name:'未发',icon:'image://static/echarts/weifa.png'}
                ],
                textStyle: {
                    color:'#666666',
                    fontSize: 16
                },
                selectedMode:false
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            series: [{
                name: '金额',
                type: 'pie',
                center:['50%','115px'],
                radius: ['50%', '70%'],
                label: {
                    normal: {
                        show: false
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[{name:'已发',value:20},{name:'未发',value:2}]
            }],
            color:[
                '#f0715b','#efefef'
            ],
            emphasisColor:[
                '#e2634f','#dadada'
            ],
        };

        var optionScan = {
            grid:{
                top:30,
                left:60,
                right:50,
                bottom:50
            },
            xAxis: {
                type : 'category',
                boundaryGap : false,
                data: ["3-11","3-12","3-13","3-14","3-15","3-16",
                       "3-17","3-18","3-19","3-20","3-21","3-22"],
                axisTick:{
                    lineStyle:{
                        color:'#ccc'
                    }
                },
                axisLine: {
                    show: false
                },
                splitLine:{
                    show: false
                }
            },
            yAxis: {
                axisLine: {
                    show: false
                },
                axisTick:{
                    show: false
                }
            },
            tooltip: {},
            series: [{
                name: '次数',
                type:'line',
                symbol:'emptyCircle',
                symbolSize:[6,6],
                showAllSymbol:true,
                smooth:true,
                itemStyle: {normal: {
                    areaStyle: {type: 'default'}
                }},
                data: [256, 320, 300, 290, 360, 365, 356, 320, 300, 290, 360, 365]
            }],
            color: [
                '#63cdef'
            ]
        };

        var optionMoney = {
            grid:{
                top:20,
                bottom:25,
                left:70,
                right:50
            },
            xAxis: {
                boundaryGap : false,
                data: ["2005-3-11","2005-3-12","2005-3-13","2005-3-14","2005-3-15","2005-3-16"],
                axisLine: {
                    show: false
                },
                axisTick:{
                    show: false
                }
            },
            yAxis: {
                axisLine: {
                    show: false
                },
                axisTick:{
                    show: false
                }
            },
            tooltip: {},
            series: [{
                name: '金额',
                type: 'line',
                symbol:'image://static/echarts/symbol.png',
                //symbol:'path://static/echarts/symbol.svg',
                symbolSize:12,
                showAllSymbol:true,
                data: [256, 320, 300, 290, 360, 365],
                lineStyle:{
                    normal:{
                        width:2
                    }
                }
            }],
            color: [
                '#f9a30b'
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        this.loadUsedChart(function(data){
            var width=$('#rpLabel').width();
            var distance=width/(data.length-1);
            data.forEach(function(value,index){
                var left=distance*index;
                $('#rpLabel').append('<a class="redpacket" rpid="'+index+'" style="left:'+left+'px"><span class="circle"></span><span class="text">'+value.name+'</span></a>');
            });
            $('.redpacket').click(function(){
                var list=data[parseInt($(this).attr('rpid'),10)];
                loadData(list);
                $('.redpacket').removeClass('selected');
                $(this).addClass('selected');
            });
            
            $('.redpacket:first').triggerHandler('click');
            
            function loadData(list){
                var usedValue=list.used;
                var remainValue=list.remain;
                var name='数量';
                if(list.limitType==='1'){
                    usedValue=parseFloat(usedValue)/100;
                    remainValue=parseFloat(remainValue)/100;
                    name='金额';
                    $('#titleAmount').hide();
                    $('#titleMoney').show();
                }
                else{
                    $('#titleMoney').hide();
                    $('#titleAmount').show();
                }
                optionUsed.series[0].name=name;
                optionUsed.series[0].data = [{name:'已发',value:usedValue,itemStyle:{emphasis:{color:optionUsed.emphasisColor[0]}}},
                                             {name:'未发',value:remainValue,itemStyle:{emphasis:{color:optionUsed.emphasisColor[1]}}}];
                chartUsed.setOption(optionUsed,true);
                $('#usedPercent').html(parseFloat(list.used/list.total*100).toFixed(2)+'%');
            }
        });

        /**
         * 获取扫码记录图标数据
         * 
         * @param   data 
         */
        this.loadScanChart(function(data){
            var amount=0;
            data.forEach(function(value){
                amount+=parseInt(value.times,10);
            });
            $('#moduleScan .amount').html(amount);
            
            $('#moduleScan .label-menu>span').click(function(){
                loadData(parseInt($(this).attr('day'),10));
                $(this).siblings('span').removeClass('selected');
                if(!$(this).hasClass('selected')){
                    $(this).addClass('selected');
                }
            });
            $('#moduleScan .label-menu>span:first').triggerHandler('click');
            
            function loadData(day){
                var now=new Date();
                var valData=[];
                var xAxisData=[];
                
                for(var i=-day;i<=0;i++){
                    var date=new Date(now.getFullYear(),now.getMonth(),now.getDate()+i);
                    var year=date.getFullYear();
                    var month=date.getMonth()+1;
                    var day=date.getDate();
                    var str=year+'-'+(month>9?month:'0'+month)+'-'+(day>9?day:'0'+day);
                    xAxisData.push(str);
                    var obj=data.filter(function(val){
                        return val.date==str;
                    });
                    if(obj.length==0){
                        valData.push(0);
                    }
                    else{
                        valData.push(obj[0].times);
                    }
                }
                /*var date=new Date();
                var from=new Date(date.getFullYear(),date.getMonth(),date.getDate()-day);
                
                data.forEach(function(value){
                    if(new Date(value.date.replace('/\-/g','/'))>=from){
                        valData.push(value.times);
                        xAxisData.push(value.date);
                    }
                });*/
                optionScan.xAxis.data = xAxisData;
                optionScan.series[0].data = valData;
                chartScan.setOption(optionScan);
            }
        });

        /**
         * 发放红包金额，默认获取最新的10天记录
         * 
         * @param   data
         */
        this.loadMoneyChart(function(data) {
                       
            var amount = 0;
            data.forEach(function(value){
                amount += parseFloat(value.amount) / 100;
            });
            amount = parseFloat(amount.toPrecision(12)); 
            $('#moduleMoney .amount').html(amount); // 设置红包发放金额数量
            
            $('#moduleMoney .label-menu>span').click(function() {
                var days = parseInt($(this).attr('day'), 10);
                loadData(days);
                $(this).siblings('span').removeClass('selected');
                if (! $(this).hasClass('selected')) {
                    $(this).addClass('selected');
                }
            });
            $('#moduleMoney .label-menu>span:first').triggerHandler('click');
            
            function loadData(day){
                var now = new Date();
                var valData = [];
                var xAxisData = [];
                
                for (var i = -day; i <= 0; i++) {
                    var date=new Date(now.getFullYear(),now.getMonth(),now.getDate()+i);
                    var year=date.getFullYear();
                    var month=date.getMonth()+1;
                    var day=date.getDate();
                    var str=year+'-'+(month>9?month:'0'+month)+'-'+(day>9?day:'0'+day);
                    xAxisData.push(str);
                    var obj=data.filter(function(val){
                        return val.date==str;
                    });
                    if(obj.length==0){
                        valData.push(0);
                    }
                    else{
                        valData.push(parseFloat(obj[0].amount)/100);
                    }
                }
                
                /*
                var date=new Date();
                var from=new Date(date.getFullYear(),date.getMonth(),date.getDate()-day);
                
                data.forEach(function(value){
                    if(new Date(value.date.replace('/\-/g','/'))>=from){
                        valData.push(parseFloat(value.amount)/100);
                        xAxisData.push(value.date);
                    }
                });*/
                optionMoney.xAxis.data = xAxisData;
                optionMoney.series[0].data = valData;
                chartMoney.setOption(optionMoney);
            }
        });

        // Added by shizq
        this.bindEvent();
    },

    bindEvent: function() {
        var self = this;

        /**
         * -------------------------------------------------------
         * 红包发放金额图表事件绑定
         * -------------------------------------------------------
         */
        $('#btn_fetch_redpacket_send').click(function() {
            var startDate = $('#redpacket_date_start').val();
            var endDate = $('#redpacket_date_end').val();
            self.loadMoneyChart2(startDate, endDate);
        });
        $('#btn_fetch_redpacket_send').triggerHandler('click');

        $('#btn_export_redpacket_send').click(function() {
            var startDate = $('#redpacket_date_start').val();
            var endDate = $('#redpacket_date_end').val();
            window.open('/reporting/export_money_chart?start_date=' + 
                startDate + '&end_date=' + endDate, '_blank');
        });

        /**
         * -------------------------------------------------------
         * 扫码记录图表事件绑定
         * -------------------------------------------------------
         */
        $('#btn_fetch_scand').click(function() {
            var startDate = $('#scan_date_start').val();
            var endDate = $('#scan_date_end').val();
            self.loadScanChart2(startDate, endDate);
        });
        $('#btn_fetch_scand').triggerHandler('click');

        $('#btn_export_scand').click(function() {
            var startDate = $('#scan_date_start').val();
            var endDate = $('#scan_date_end').val();
            window.open('/reporting/export_scan_chart?start_date=' + 
                startDate + '&end_date=' + endDate, '_blank');
        });
    },

    // 获取红包使用数量
    loadUsedChart:function(callback){
        $.get('reporting/get_mch_rp_used',function(data){
            if(data.length!=0){
                callback(data);
            }
            else{
                $('#moduleUsed').hide();
            }
        },'json');
    },

    // 获取扫码记录
    loadScanChart:function(callback){
        $.get('reporting/get_mch_daily_scanning',function(data){
            if(data.length!=0){
                callback(data);
            }
            else{
                $('#moduleScan').hide();
            }
        },'json');
    },

    // 获取红包发放金额数据
    loadMoneyChart: function(callback) {
        $.get('reporting/get_mch_daily_rp_amount', function(data) {
            if (data.length != 0) {
                callback(data);
            } else {
                $('#moduleMoney').hide();
            }
        }, 'json');
    },

    /**
     * 获取某个时间间隔内的数据
     * 
     * @param  startDate 开始日期
     * @param  endDate   结束日期
     * @return 
     */
    loadMoneyChart2: function(startDate, endDate) {
        var self = this;
        var params = {
            start_date: startDate,
            end_date: endDate
        };
        $.get('/reporting/load_money_chart', params, function(content) {
            if (content.errcode == 0) {
                generateChart(content.data);
            } else {
                commen.alert(content.errmsg);
            }
        }, 'json').error(function() {
            console.log('网络错误！');
        });

        /**
         * 生成图表
         * 
         * @param  array data
         */
        function generateChart(data) {
            var valData = [], xAxisData = [];
            for (i in data) {
                var item = data[i];
                xAxisData.push(item.get_date);
                valData.push(item.amount / 100);
            }
            optionMoney.xAxis.data = xAxisData;
            optionMoney.series[0].data = valData;
            self.chartMoney.setOption(optionMoney);
        }
    }, 

    /**
     * 获取扫码记录数据并生成图表
     * 
     * @param  startDate 开始日期
     * @param  endDate   结束日期
     * @return 
     */
    loadScanChart2: function(startDate, endDate) {
        var self = this;
        var params = {
            start_date: startDate,
            end_date: endDate
        };
        $.get('/reporting/load_scan_chart', params, function(content) {
            if (content.errcode == 0) {
                generateChart(content.data);
            } else {
                commen.alert(content.errmsg);
            }
        }, 'json').error(function() {
            console.log('网络错误！');
        });

        /**
         * 生成图表
         * 
         * @param  array data
         */
        function generateChart(data) {
            var valData = [], xAxisData = [];
            for (i in data) {
                var item = data[i];
                xAxisData.push(item.scan_date);
                valData.push(item.nums);
            }
            optionScan.xAxis.data = xAxisData;
            optionScan.series[0].data = valData;
            self.chartScan.setOption(optionScan);
        }
    },
};

var optionMoney = {
    grid: {
        top:20,
        bottom:25,
        left:70,
        right:50
    },
    xAxis: {
        boundaryGap : false,
        data: [],
        axisLine: {
            show: false
        },
        axisTick:{
            show: false
        }
    },
    yAxis: {
        axisLine: {
            show: false
        },
        axisTick:{
            show: false
        }
    },
    tooltip: {},
    series: [{
        name: '金额',
        type: 'line',
        symbol: 'image://static/echarts/symbol.png',
        symbolSize: 12,
        showAllSymbol: true,
        data: [],
        lineStyle:{
            normal:{
                width:2
            }
        }
    }],
    color: [
        '#f9a30b'
    ]
};

var optionScan = {
    grid: {
        top:30,
        left:60,
        right:50,
        bottom:50
    },
    xAxis: {
        type : 'category',
        boundaryGap : false,
        data: [],
        axisTick:{
            lineStyle:{
                color:'#ccc'
            }
        },
        axisLine: {
            show: false
        },
        splitLine:{
            show: false
        }
    },
    yAxis: {
        axisLine: {
            show: false
        },
        axisTick:{
            show: false
        }
    },
    tooltip: {},
    series: [{
        name: '次数',
        type:'line',
        symbol:'emptyCircle',
        symbolSize:[6,6],
        showAllSymbol:true,
        smooth:true,
        itemStyle: {normal: {
            areaStyle: {type: 'default'}
        }},
        data: []
    }],
    color: [
        '#63cdef'
    ]
};


$(function(){
    index.init();
});
