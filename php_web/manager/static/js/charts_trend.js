$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            var param=rptbase.getValue();
            var proText=$("select[name=proCode]").find("option:selected").text();
            var cityText=$("select[name=cityCode]").find("option:selected").text();
            var areaText=$("select[name=areaCode]").find("option:selected").text();
            var productText=$("select[name=productid]").find("option:selected").text();
            var batchText=$("select[name=batchid]").find("option:selected").text();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            param.area=$("#areaCode").val();
            if(param.pro==0){
                 param.mycity=proText;
            }
            if(param.pro!=0&&param.city==0){
                 param.mycity=proText;
            }
            if(param.city!=0&&param.area==0){
                 param.mycity=proText+cityText;
            }
            if(param.area!=0){
                 param.mycity=proText+cityText+areaText;
            }
            param.productName=productText;
            param.batchName=batchText;

            _this.formSubmit(param);
            _this.search();
            _this.getPageStatus();
    },
    search:function(){
        var _this=this;
         $("#getSearch").click(function(){
            var param=rptbase.getValue();
            var proText=$("select[name=proCode]").find("option:selected").text();
            var cityText=$("select[name=cityCode]").find("option:selected").text();
            var areaText=$("select[name=areaCode]").find("option:selected").text();
            var productText=$("select[name=productid]").find("option:selected").text();
            var batchText=$("select[name=batchid]").find("option:selected").text();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            param.area=$("#areaCode").val();
            if(param.pro==0){
                 param.mycity=proText;
            }
            if(param.pro!=0&&param.city==0){
                 param.mycity=proText;
            }
            if(param.city!=0&&param.area==0){
                 param.mycity=proText+cityText;
            }
            if(param.area!=0){
                 param.mycity=proText+cityText+areaText;
            }
            param.productName=productText;
            param.batchName=batchText;
            _this.formSubmit(param);

        });
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

        $("#cityCode").empty();
        $("#areaCode").empty();
        $("#cityCode").append("<option value='0'>全部</option>");
        $("#areaCode").append("<option value='0'>全部</option>");
        $.post('/charts/get_city_list',{proCode:proCode},function(data){
            if(data==''){
                $("#cityCode").hide();
            }else{
                $("#cityCode").show();
            }
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
    formSubmit:function(param){
        var _this=this;
        localTime=localStorage.getItem("trend_times");
        //判断是否达到5个上限
        localData=JSON.parse(localStorage.getItem("trend_datas"));
        if(localData!=null){
            if(localTime==JSON.stringify({year:param.year,month:param.month,week:param.week})){
                if(localData.length>=5){
                    common.confirm('最多允许5个筛选比较，是否重载页面？', function(confirmed) {
                        if (confirmed == 1) {
                            location.reload();
                        }
                    });
                    return false;
                }
            }else{
                localStorage.removeItem('trend_datas');
                localStorage.removeItem('trend_mytags');
            }
        }
        //判断该城市是否存在
        localMytags=JSON.parse(localStorage.getItem("trend_mytags"));
        var localmytagsdata={
            'productName':$("select[name=productid]").find("option:selected").text(),
            'batchNo':$("select[name=batchid]").find("option:selected").text(),
            'regionName':param.mycity
        };
         if(localMytags!=null){
            if(localTime==JSON.stringify({year:param.year,month:param.month,week:param.week})){
                    for($j=0;$j<localMytags.length;$j++){
                        if(JSON.stringify(localMytags[$j])==JSON.stringify(localmytagsdata)){
                            common.alert('该筛选比较已存在！');
                            return false;
                        }
                    }
            }else{
                localStorage.removeItem('trend_datas');
                localStorage.removeItem('trend_mytags');
            }

        }

        //数据请求
        common.loading();
        $.post(common.getRptRootUrl()+'charts/get_trend_data',{param:param},function(data){
            common.unloading();
            if (data.errcode == 1) {
                _this.showNeedPay(1);
                return;
            } else if (data.errcode == 2) {
                _this.showNeedPay(2);
                return;
            }
            times=localStorage.getItem("trend_times");
            if(times!=JSON.stringify({year:param.year,month:param.month,week:param.week})){
                localStorage.removeItem('trend_datas');
                localStorage.removeItem('trend_mytags');
            }

            datas=JSON.parse(localStorage.getItem("trend_datas"));
            mycitys=JSON.parse(localStorage.getItem("trend_mycitys"));
            //定义小标签内容
            mytags=JSON.parse(localStorage.getItem("trend_mytags"));
            if(datas==null){
                var datas=[data['scanNum']];
            }else{
                var obj=data['scanNum'];
                datas.push(obj);
            }
            if(mycitys==null){
                var mycitys=[data['scanNum']['name']];
            }else{
                var objcity=data['scanNum']['name'];
                mycitys.push(objcity);
            }
            if(mytags==null){
                var mytags=[
                    {
                        'productName':$("select[name=productid]").find("option:selected").text(),
                        'batchNo':$("select[name=batchid]").find("option:selected").text(),
                        'regionName':data['cityName']
                    }
                ];
            }else{
                var objmytags={
                        'productName':$("select[name=productid]").find("option:selected").text(),
                        'batchNo':$("select[name=batchid]").find("option:selected").text(),
                        'regionName':data['cityName']
                    };
                mytags.push(objmytags);
            }
            //循环获取name
            var legendName=[];
            for($i=0;$i<datas.length;$i++){
                legendName.push(datas[$i]['name']);
            }
            localStorage.setItem("trend_datas",JSON.stringify(datas));
            localStorage.setItem("trend_mycitys",JSON.stringify(mycitys));
            localStorage.setItem("trend_times",JSON.stringify({year:param.year,month:param.month,week:param.week}));
            localStorage.setItem("trend_mytags",JSON.stringify(mytags));
            //生成小标签目录
            mytags=JSON.parse(localStorage.getItem("trend_mytags"));
            $('.selector-set').empty();
            mytags.forEach(function(data) {
                var row;
                row = '<a class="ss-item" data-id="'+(data.productName+data.batchNo+data.regionName).replace(/\s/g, "")+'">';
                row += '<b>产品：</b><em>'+data.productName+'</em>&nbsp;';
                row += '<b>乐码批次：</b><em>'+data.batchNo+'</em>&nbsp;';
                row += '<b>区域：</b><em>'+data.regionName+'</em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                row += '<span>删除</span></a>'
                $('.selector-set').append(row);
            });
            //对比数据删除
            $(".ss-item").click(function(){
                var dellocalData=$(this).attr('data-id');
                var r=confirm("确认删除？")
                if (r==true){
                    var delLocaldata=JSON.parse(localStorage.getItem("trend_datas"));
                    var delLocalmytags=JSON.parse(localStorage.getItem("trend_mytags"));
                    for($i=0;$i<delLocaldata.length;$i++){
                        var s=delLocaldata[$i].name.replace("产品：","").replace("乐码批次：","").replace("区域：","").replace(/\s/g, "");
                        if(JSON.stringify(s)==JSON.stringify(dellocalData)){
                            delLocaldata.splice($i,1);
                        }
                    }
                    for($j=0;$j<delLocalmytags.length;$j++){
                        if(JSON.stringify((delLocalmytags[$j].productName+delLocalmytags[$j].batchNo+delLocalmytags[$j].regionName).replace(/\s/g, ""))==JSON.stringify(dellocalData)){
                            delLocalmytags.splice($j,1);
                        }
                    }
                    //删除本地存储数据
                    localStorage.setItem("trend_datas",JSON.stringify(delLocaldata));
                    localStorage.setItem("trend_mytags",JSON.stringify(delLocalmytags));
                    //移除小标签
                    $(this).remove();
                    _this.createLine(legendName,delLocaldata,data['theDate']);
                }
            });
            _this.createLine(legendName,datas,data['theDate']);
        })
    },

    // -------------------------------------
    // Added by shizq - begin
    showNeedPay: function(number) {
        var content = '<p>';
        if (number == 1) {
            content += '报表试用已到期，请<a href="/charts/buy/2">购买</a>';
        }
        if (number == 2) {
            content += '报表使用已到期，请<a href="/charts/buy/2">续费</a>';
        }
        content += '</p>';
        $('#main').html(content);
    },
    // Added by shizq - end

    getPageStatus:function(){
        window.onbeforeunload=function (){
            localStorage.removeItem('trend_datas');
            localStorage.removeItem('trend_mycitys');
            localStorage.removeItem('trend_times');
            localStorage.removeItem('trend_mytags');
        }
    },
    createLine:function(legendName,data,time){
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
                    show:false,
                    data:legendName,
                    top:'bottom',
                    itemGap: 50,
                    align:'left',
                    selectedMode:true,
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
                           data : time
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
                series : data
            };

        myChart.setOption(option);
        common.autoHeight();
    }
};
