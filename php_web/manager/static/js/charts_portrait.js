$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            _this.getPCA();
            _this.getSearch(_this.getValue());
            $("#getSearch").on('click',function(){
                _this.getSearch(_this.getValue());
            });
            common.autoHeight();
    },
    getPCA:function(){
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
    getValue:function(){
        var proCode=$("#proCode").val();
        var cityCode=$("#cityCode").val();
        var areaCode=$("#areaCode").val();
        var age=$("#age").val();
        var sex=$("#sex").val();
        var constellation=$("#constellation").val();
        var time=$("#time").val();
        var tabIdentity=$('.tab_current').attr('data-identtity');
        var param={proCode:proCode,cityCode:cityCode,areaCode:areaCode,age:age,sex:sex,constellation:constellation,time:time,tab:tabIdentity}
        return param;
    },
    getSearch:function(param){
        var _this=this;
        common.loading();
        $.post(common.getRptRootUrl()+'charts/get_portrait_data',param,function(res){
            common.unloading();
            if(res.length==0){
                $("#portraitContent").empty();
                $("#pieHtml").empty();
                $("#main").hide();
                $("#portraitContent").append('<div style="width:100%;height:450px;line-height:450px;text-align:center;font-size:100px;color:#ddd">暂无数据</div>');
                common.autoHeight();
            }
            if(res.length==1){
                html='  <div class="dashiMain">';
                html+='      <!-- 大师 -->';
                html+='      <div class="dashi">';
                html+='          <img src="'+(res[0]['sex']==0 ? '/static/images/man.png' : res[0]['sex']==1 ? '/static/images/man.png' : '/static/images/woman.png')+'" style="width:200px">';
                html+='          <div class="tag_1">'+(res[0]['time']=='NULL' ? '未知时间段' : res[0]['time'])+'</div>';
                html+='          <div class="tag_2">'+res[0]['city']+'</div>';
                html+='          <div class="tag_3">'+(res[0]['age']=='NULL' ? '未知年龄段' : res[0]['age'])+'<br>'+(res[0]['sex']==0 ? '未知性别' : res[0]['sex']==1 ? '男' : '女')+'</div>';
                html+='          <div class="tag_4">'+(res[0]['constellation']=='NULL' ? '未知星座' : res[0]['constellation'])+'</div>';
                html+='          <div class="tag_5">大&nbsp;师&nbsp;榜</div>';
                html+='      </div>';
                html+='  </div>';
                html+='<div class="h30" style="clear:both"></div>';
                html+='<div class="tag_6" style="padding:20px 0">消费者特征（前三）</div>';


                $("#portraitContent").empty();
                $("#portraitContent").append(html);
                $(".tag_6").css('margin-top','-150px');
                // 创建饼图
                html='<img src="/static/images/portrait_pie_center.png" class="pie_center">';

                html+='<div class="pieMain">';
                html+='  <p>';
                html+='    <img src="/static/images/dashi.png">';
                html+='    <span class="pieList">';
                html+='      <b style="color:#689dee">'+res[0]['per']+'%</b><br>';
                html+='      <small style="color:#999999;margin: auto 9px;">大&nbsp;师&nbsp;榜</small>';
                html+='    </span>';
                html+='  </p><br>';
                html+='</div>';
                html+='<div class="h30" style="clear:both"></div>';
                html+='<div class="tag_6">消费者特征人群占比（前三）</div>';
                $("#main").show();
                $("#pieHtml").empty();
                $("#pieHtml").append(html);
                data=[
                        {value:(res[0]['count']-(res[0]['num'])).toFixed(2), name:((100-(res[0]['per']))).toFixed(2)+'%\n 其他',itemStyle:{normal:{color:'#66cab1'}}},
                        {value:(res[0]['num']).toFixed(2), name:(res[0]['per']).toFixed(2)+'%\n 人群',itemStyle:{normal:{color:'#689dee'}}}

                    ];
                if(((100-(res[0]['per']))).toFixed(2)=='0.00'){
                    data.shift();
                }
                _this.createPie(data);
            }
            if(res.length==2){
                html='<div class="dashiMain">';
                html+='      <!-- 大师 -->';
                html+='      <div class="dashi">';
                html+='          <img src="'+(res[0]['sex']==0 ? '/static/images/man.png' : res[0]['sex']==1 ? '/static/images/man.png' : '/static/images/woman.png')+'" style="width:200px">';
                html+='          <div class="tag_1">'+(res[0]['time']=='NULL' ? '未知时间段' : res[0]['time'])+'</div>';
                html+='          <div class="tag_2">'+res[0]['city']+'</div>';
                html+='          <div class="tag_3">'+(res[0]['age']=='NULL' ? '未知年龄段' : res[0]['age'])+'<br>'+(res[0]['sex']==0 ? '未知性别' : res[0]['sex']==1 ? '男' : '女')+'</div>';
                html+='          <div class="tag_4">'+(res[0]['constellation']=='NULL' ? '未知星座' : res[0]['constellation'])+'</div>';
                html+='          <div class="tag_5">大&nbsp;师&nbsp;榜</div>';
                html+='      </div>';
                html+='  </div>';
                html+='  <div class="gaoshouMain">';
                html+='      <!-- 高手 -->';
                html+='      <div class="gaoshou">';
                html+='          <img src="'+(res[1]['sex']==0 ? '/static/images/man.png' : res[1]['sex']==1 ? '/static/images/man.png' : '/static/images/woman.png')+'" style="width:200px">';
                html+='          <div class="tag_1">'+(res[1]['time']=='NULL' ? '未知时间段' : res[1]['time'])+'</div>';
                html+='          <div class="tag_2">'+res[1]['city']+'</div>';
                html+='          <div class="tag_3">'+(res[1]['age']=='NULL' ? '未知年龄段' : res[1]['age'])+'<br>'+(res[1]['sex']==0 ? '未知性别' : res[1]['sex']==1 ? '男' : '女')+'</div>';
                html+='          <div class="tag_4">'+(res[1]['constellation']=='NULL' ? '未知星座' : res[1]['constellation'])+'</div>';
                html+='          <div class="tag_5">高&nbsp;手&nbsp;榜</div>';
                html+='      </div>';
                html+='  </div>';
                html+='<div class="h30" style="clear:both"></div>';
                html+='<div class="tag_6" style="padding:20px 0;margin-top:-50px">消费者特征（前三）</div>';

                $("#portraitContent").empty();
                $("#portraitContent").append(html);
                // 创建饼图
                html='<img src="/static/images/portrait_pie_center.png" class="pie_center">';

                html+='<div class="pieMain">';
                html+='  <p>';
                html+='    <img src="/static/images/dashi.png">';
                html+='    <span class="pieList">';
                html+='      <b style="color:#689dee">'+res[0]['per']+'%</b><br>';
                html+='      <small style="color:#999999;margin: auto 9px;">大&nbsp;师&nbsp;榜</small>';
                html+='    </span>';
                html+='  </p><br>';
                html+='  <p>';
                html+='    <img src="/static/images/gaoshou.png">';
                html+='    <span class="pieList">';
                html+='      <b style="color:#e4675e">'+res[1]['per']+'%</b><br>';
                html+='      <small style="color:#999999;margin: auto 9px;">高&nbsp;手&nbsp;榜</small>';
                html+='    </span>';
                html+='  </p><br>';
                html+='</div>';
                html+='<div class="h30" style="clear:both"></div>';
                html+='<div class="tag_6">消费者特征人群占比（前三）</div>';
                $("#main").show();
                $("#pieHtml").empty();
                $("#pieHtml").append(html);
                data=[
                        {value:(res[0]['count']-(res[0]['num']+res[1]['num'])).toFixed(2), name:((100-(res[0]['per']+res[1]['per']))).toFixed(2)+'%\n 其他',itemStyle:{normal:{color:'#66cab1'}}},
                        {value:(res[0]['num']).toFixed(2), name:(res[0]['per']).toFixed(2)+'%\n 人群',itemStyle:{normal:{color:'#689dee'}}},
                        {value:(res[1]['num']).toFixed(2), name:(res[1]['per']).toFixed(2)+'%\n 人群',itemStyle:{normal:{color:'#e4675e'}}}

                    ];
                if(((100-(res[0]['per']+res[1]['per']))).toFixed(2)=='0.00'){
                    data.shift();
                }
                _this.createPie(data);
            }
            if(res.length==3){
                html='<div class="dashiMain">';
                html+='      <!-- 大师 -->';
                html+='      <div class="dashi">';
                html+='          <img src="'+(res[0]['sex']==0 ? '/static/images/man.png' : res[0]['sex']==1 ? '/static/images/man.png' : '/static/images/woman.png')+'" style="width:200px">';
                html+='          <div class="tag_1">'+(res[0]['time']=='NULL' ? '未知时间段' : res[0]['time'])+'</div>';
                html+='          <div class="tag_2">'+res[0]['city']+'</div>';
                html+='          <div class="tag_3">'+(res[0]['age']=='NULL' ? '未知年龄段' : res[0]['age'])+'<br>'+(res[0]['sex']==0 ? '未知性别' : res[0]['sex']==1 ? '男' : '女')+'</div>';
                html+='          <div class="tag_4">'+(res[0]['constellation']=='NULL' ? '未知星座' : res[0]['constellation'])+'</div>';
                html+='          <div class="tag_5">大&nbsp;师&nbsp;榜</div>';
                html+='      </div>';
                html+='  </div>';
                html+='  <div class="gaoshouMain">';
                html+='      <!-- 高手 -->';
                html+='      <div class="gaoshou">';
                html+='          <img src="'+(res[1]['sex']==0 ? '/static/images/man.png' : res[1]['sex']==1 ? '/static/images/man.png' : '/static/images/woman.png')+'" style="width:200px">';
                html+='          <div class="tag_1">'+(res[1]['time']=='NULL' ? '未知时间段' : res[1]['time'])+'</div>';
                html+='          <div class="tag_2">'+res[1]['city']+'</div>';
                html+='          <div class="tag_3">'+(res[1]['age']=='NULL' ? '未知年龄段' : res[1]['age'])+'<br>'+(res[1]['sex']==0 ? '未知性别' : res[1]['sex']==1 ? '男' : '女')+'</div>';
                html+='          <div class="tag_4">'+(res[1]['constellation']=='NULL' ? '未知星座' : res[1]['constellation'])+'</div>';
                html+='          <div class="tag_5">高&nbsp;手&nbsp;榜</div>';
                html+='      </div>';
                html+='  </div>';
                html+='  <div class="xinshouMain">';
                html+='      <!-- 新手 -->';
                html+='      <div class="xinshou">';
                html+='          <img src="'+(res[2]['sex']==0 ? '/static/images/man.png' : res[2]['sex']==1 ? '/static/images/man.png' : '/static/images/woman.png')+'" style="width:200px">';
                html+='          <div class="tag_1">'+(res[2]['time']=='NULL' ? '未知时间段' : res[2]['time'])+'</div>';
                html+='          <div class="tag_2">'+res[2]['city']+'</div>';
                html+='          <div class="tag_3">'+(res[2]['age']=='NULL' ? '未知年龄段' : res[2]['age'])+'<br>'+(res[2]['sex']==0 ? '未知性别' : res[2]['sex']==1 ? '男' : '女')+'</div>';
                html+='          <div class="tag_4">'+(res[2]['constellation']=='NULL' ? '未知星座' : res[2]['constellation'])+'</div>';
                html+='          <div class="tag_5">新&nbsp;手&nbsp;榜</div>';
                html+='      </div>';
                html+='  </div>';
                html+='<div class="h30" style="clear:both"></div>';
                html+='<div class="tag_6" style="padding:20px 0">消费者特征（前三）</div>';

                $("#portraitContent").empty();
                $("#portraitContent").append(html);

                // 创建饼图
                html='<img src="/static/images/portrait_pie_center.png" class="pie_center">';

                html+='<div class="pieMain">';
                html+='  <p>';
                html+='    <img src="/static/images/dashi.png">';
                html+='    <span class="pieList">';
                html+='      <b style="color:#689dee">'+res[0]['per']+'%</b><br>';
                html+='      <small style="color:#999999;margin: auto 9px;">大&nbsp;师&nbsp;榜</small>';
                html+='    </span>';
                html+='  </p><br><br><br>';
                html+='  <p>';
                html+='    <img src="/static/images/gaoshou.png">';
                html+='    <span class="pieList">';
                html+='      <b style="color:#e4675e">'+res[1]['per']+'%</b><br>';
                html+='      <small style="color:#999999;margin: auto 9px;">高&nbsp;手&nbsp;榜</small>';
                html+='    </span>';
                html+='  </p><br><br><br>';
                html+='  <p>';
                html+='    <img src="/static/images/xinshou.png">';
                html+='    <span class="pieList">';
                html+='      <b style="color:#f3b14a">'+res[2]['per']+'%</b><br>';
                html+='      <small style="color:#999999;margin: auto 9px;">新&nbsp;手&nbsp;榜</small>';
                html+='    </span>';
                html+='  </p><br><br><br>';
                html+='</div>';
                html+='<div class="h30" style="clear:both"></div>';
                html+='<div class="tag_6">消费者特征人群占比（前三）</div>';
                $("#main").show();
                $("#pieHtml").empty();
                $("#pieHtml").append(html);
                data=[
                        {value:(res[0]['count']-(res[0]['num']+res[1]['num']+res[2]['num'])).toFixed(2), name:(100-(res[0]['per']+res[1]['per']+res[2]['per'])).toFixed(2)+'%\n 其他',itemStyle:{normal:{color:'#66cab1'}}},
                        {value:(res[0]['num']).toFixed(2), name:(res[0]['per']).toFixed(2)+'%\n 人群',itemStyle:{normal:{color:'#689dee'}}},
                        {value:(res[1]['num']).toFixed(2), name:(res[1]['per']).toFixed(2)+'%\n 人群',itemStyle:{normal:{color:'#e4675e'}}},
                        {value:(res[2]['num']).toFixed(2), name:(res[2]['per']).toFixed(2)+'%\n 人群',itemStyle:{normal:{color:'#f3b14a'}}}

                    ];
                if((100-(res[0]['per']+res[1]['per']+res[2]['per'])).toFixed(2)=='0.00'||(res[0]['num']+res[1]['num']+res[2]['num'])==res[0]['count']){
                    data.shift();
                }
                _this.createPie(data);
            }
        })
    },
    createPie:function(data){
        var myChart = echarts.init(document.getElementById('main'));
        option = {
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b}"
            },
            series : [
                {
                    name: '消费者特征人群占比',
                    type: 'pie',
                    radius : [60,175],
                    label: {
                        normal: {
                            position: 'inner',
                            textStyle:{
                                fontSize:14
                            },
                            show:true,
                            formatter: '{b}'
                        }
                    },
                    data:data
                }
            ]
        };
        myChart.setOption(option);
        common.autoHeight();
    }
};
