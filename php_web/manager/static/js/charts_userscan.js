$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            var param=rptbase.getValue();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            param.area=$("#areaCode").val();
            var month=$(".month li>.active").attr('data-value');
            _this.formSubmit(param);
            _this.search();
            _this.lableMenu();
            // 下载选择
            var is_detail=$('#is_detail');
            is_detail.on('click',function(){
                if($(this).is(':checked')){
                   $(this).val(1);
                }else{
                    $(this).val(0);
                }
            });
            if(is_detail.val()==1){
                is_detail.prop("checked",true);
            }
    },
    search:function(){
        var _this=this;
        //查询按钮请求数据
        $("#getSearch").click(function(){
            var param=rptbase.getValue();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            param.area=$("#areaCode").val();
            _this.formSubmit(param);
        })
        //监测省份选择下拉选中事件
        $("#proCode").change(function(){
        var proCode=$("#proCode").val();
        if(proCode==0){
            $("#cityCode").empty();
            $("#areaCode").empty();
            $("#cityCode").append("<option value='0'>全部</option>");
            $("#areaCode").append("<option value='0'>全部</option>");
        }else{
            //请求选中省份的市
            $.post('/charts/get_city_list',{proCode:proCode},function(data){
                if(data==''){
                    $("#cityCode").hide();
                }else{
                    $("#cityCode").show();
                }
                $("#cityCode").empty();
                $("#areaCode").empty();
                $("#cityCode").append("<option value='0'>全部</option>");
                $("#areaCode").append("<option value='0'>全部</option>");
                for(var i=0; i<data.length; i++){
                        var option = "";
                        option += "<option value='"+data[i]['code']+"'>"+data[i]['name']+"</option>";
                        $("#cityCode").append(option);
                 }
            },'json')
        }
       });
        //监测城市选择下拉选中事件
       $("#cityCode").change(function(){
        var cityCode=$("#cityCode").val();
        if(cityCode==0){
            $("#areaCode").empty();
            $("#areaCode").append("<option value='0'>全部</option>");
        }else{
        //请求选中省份的市
        $("#areaCode").empty();
        $("#areaCode").append("<option value='0'>全部</option>");
        $.post('/charts/get_dist_list',{cityCode:cityCode},function(data){
            if(data==''){
                $("#areaCode").hide();
            }else{
                $("#areaCode").show();
            }
            for(var i=0; i<data.length; i++){
                    var option = "";
                    option += "<option value='"+data[i]['code']+"'>"+data[i]['name']+"</option>";
                    $("#areaCode").append(option);
             }
        },'json')
        }
       });
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
            var param=rptbase.getValue();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            param.area=$("#areaCode").val();
            _this.formSubmit(param);
        })
    },
    formSubmit:function(param){
        var _this=this;
        common.loading();
        $.post(common.getRptRootUrl()+'charts/get_userscan_data',{param:param},function(data){
            // 详细用户扫码数据
            if(param.month==0){
                $("#getDown").hide();
                $("#userscan_detail_down").hide();
            }else{
                $("#getDown").show();
                if(!param.week==0&&!param.weektime==''){
                    $("#userscan_detail_down").show();
                }else{
                    $("#userscan_detail_down").hide();
                    $('#is_detail').val(0);
                    if($('#is_detail').val()==0){
                        $('#is_detail').attr("checked", false);
                    }
                }
                param.is_detail=$("#is_detail").val();
            }
            //////////////////////////////////////////////
            _this.createLine(data);
            _this.createTable(param);

        });
        _this.getDown();
    },
    getDown:function(){
        var _this=this;
        $("#getDown").click(function(){
            var param=rptbase.getValue();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            param.area=$("#areaCode").val();
            // 下载详细数据
            var is_detail = $("#is_detail").val() || 0;
            param.is_detail=is_detail;
            if(param.month=='0'){
                common.alert('不支持年份报表下载！');
            }else{
                if(param.is_detail==1){
                    url=common.getRptRootUrl()+'charts/down_userscan_detail_data';
                }else{
                     url=common.getRptRootUrl()+'charts/down_userscan_data';
                }
                rptbase.postDowndata(url,param);
            }
        })
    },
    createLine:function(data){
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
                        data:['扫码量'],
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
                            data : data['theDate']
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
                            data:data['scanNum']
                        }
                    ]
                };
            myChart.setOption(option);
    },
    createTable:function(param){
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
                url:common.getRptRootUrl() + "charts/get_userscan_data_table",//请求数据地址
                type:"POST",//请求方式
                data:param!=undefined?{param:param}:{},//携带参数
            },
            "columns": [

                {"data":"userId","class":"center"},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.nickName){
                            return '<div><a class="btn-text noselect blue" href="/reporting/show_user_info/'+data.userId+'" target="_blank">'+ common.cutString(data.nickName,14) +'</a></div>';
                        }else{
                            return '<div>'+data.nickName+'</div>';
                        }

                    }
                },
                {"data":"theDate","class":"center"},
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div><a class="btn-text noselect blue" href="/reporting/show_scan_info/'+data.userId+'_'+data.theDate+'_'+data.level+'_'+param.productid+'_'+param.batchid+'_'+param.pro+'_'+param.city+'_'+param.area+'_'+param.tab+'" target="_blank">'+ data.scanNum +'</a></div>';
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div><a class="btn-text noselect blue" href="/reporting/show_redpack_info/'+data.userId+'_'+data.theDate+'_'+data.level+'_'+param.productid+'_'+param.batchid+'_'+param.pro+'_'+param.city+'_'+param.area+'_'+param.tab+'" target="_blank">'+ data.redNum +'</a></div>';
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div><a class="btn-text noselect blue" href="/reporting/show_trans_info/'+data.userId+'_'+data.theDate+'_'+data.level+'_'+param.productid+'_'+param.batchid+'_'+param.pro+'_'+param.city+'_'+param.area+'_'+param.tab+'" target="_blank">'+ data.transNum +'</a></div>';
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div><a class="btn-text noselect blue" href="/reporting/show_card_info/'+data.userId+'_'+data.theDate+'_'+data.level+'_'+param.productid+'_'+param.batchid+'_'+param.pro+'_'+param.city+'_'+param.area+'_'+param.tab+'" target="_blank">'+ data.cardNum +'</a></div>';
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div><a class="btn-text noselect blue" href="/reporting/show_point_info/'+data.userId+'_'+data.theDate+'_'+data.level+'_'+param.productid+'_'+param.batchid+'_'+param.pro+'_'+param.city+'_'+param.area+'_'+param.tab+'" target="_blank">'+ data.pointAmount +'</a></div>';
                    }
                },
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        return '<div><a class="btn-text noselect blue" href="/reporting/show_point_used_info/'+data.userId+'_'+data.theDate+'_'+data.level+'_'+param.productid+'_'+param.batchid+'_'+param.pro+'_'+param.city+'_'+param.area+'_'+param.tab+'" target="_blank">'+ data.pointUsed +'</a></div>';
                    }
                }
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            },
            "preDrawCallback": function() {
                common.loading();
            }
        };

        this.table=$('#userscan_data').dataTable(config);
    }
};
