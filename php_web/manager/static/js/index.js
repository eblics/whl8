var index = {
    init: function() {
        var _this=this;
        _this.loadSwiper();
        _this.loadRedpack();
        _this.loadCard();
        _this.loadPoint();
        myChart.showLoading();
        $.post(common.getRptRootUrl()+'reporting/get_mch_user_xinzeng',function(data){
            myChart.hideLoading();
            _this.createLine(data);
        });
        _this.loadIndexdata();
        // 使用刚指定的配置项和数据显示图表。
        _this.tip();
    },
    // 加载幻灯片
    loadSwiper:function(){
    	var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',//分页器
            paginationClickable: true,//点击分页器圆点切换图片
            spaceBetween: 0,
            centeredSlides: true,
            autoplay: 6000,
            loop:true,
            autoplayDisableOnInteraction: false,
            effect:'fade',
            fade: {
          	  crossFade: true,
          	}
        });
    },
    // 获取红包使用数量
    loadRedUsed:function(callback){
        $.post(common.getRptRootUrl()+'reporting/get_mch_rp_used',function(data){
            if(data.length==0){
                $('#redLabel').hide();
                $('#redUsed .panel_title').hide();
                $('#redPercent').html('暂无数据').addClass('nodata');
            }
            else{
                callback(data);
            }
        },'json');
    },
    loadCardUsed:function(callback){
        $.post(common.getRptRootUrl()+'reporting/get_mch_card_used',function(data){
            if(data.length==0){
                $('#cardLabel').hide();
                $('#cardUsed .panel_title').hide();
                $('#cardPercent').html('暂无数据').addClass('nodata');
            }
            else{
                callback(data);
            }
        },'json');
    },
    loadPointUsed:function(callback){
        $.post(common.getRptRootUrl()+'reporting/get_mch_point_used',function(data){
            if(data.length==0){
                $('#pointLabel').hide();
                $('#pointUsed .panel_title').hide();
                $('#pointPercent').html('暂无数据').addClass('nodata');
            }else if(data[0].total==0||data[1].total==0){//临时处理无数据
                $('#pointLabel').hide();
                $('#pointUsed .panel_title').hide();
                $('#pointPercent').html('暂无数据').addClass('nodata');
            }
            else{
                callback(data);
            }
        },'json');
    },
    // 加载总数量
    loadIndexdata:function(){
        common.loading(1);
        $.post(common.getRptRootUrl()+'reporting/get_mch_indexdata',function(data){
            common.unloading();
            $('#yf_rednum').html(data[0].redNum ? data[0].redNum : 0+'元');
            $('#yf_cardnum').html(data[1].cardNum ? data[1].cardNum : 0+'种');
            $('#yf_pointnum').html(data[2].pointNum ? data[2].pointNum : 0+'积分');
            // 计算总扫码率和总扫码量
            $("#ys_scanNum").html(data[3].scanNum);
            var sweep;
            if(data[4].batchNum==null){
                var percent=0;
            }else{
                var percent=((data[3].scanNum/data[4].batchNum)*100).toFixed(2);
            }
            sweep=percent+'%';
            $("#ys_sweep").html('&nbsp;'+sweep);
            $("#ys_sweep").css("width",sweep);
            // if(percent<5){
            //     $("#ys_sweep").css("color","#30b4b2");
            // }else{
            //     $("#ys_sweep").css("color","#fff");
            // }
        },'json');
    },
    //加载红包仪表盘
    loadRedpack:function(){
        this.loadRedUsed(function(data){
            var width=$('#redLabel').width();
            var distance=width/(data.length-1);
            data.forEach(function(value,index){
                $('#redLabel #redTool .redList').append('<li><a class="redpacket" title="'+value.name+'" data-name="'+value.name+'" rpid="'+index+'"><span class="circle"></span></a></li>');
            });
            var red_p=1;
            var red_p1=1;
            // 计算长度
            linum = $('.redList_piclist li').length;
            w = linum * 47;
            $('.redList_piclist').css('width', w + 'px');
            // 激活左右方向键
            $('.red_next').click(function(){
                linum = $('.redList_piclist li').length;
                var timer=index.modFoat(linum/4);
                ml = parseInt($('.redList_piclist').css('left'));
                if(red_p<=timer){
                    if(red_p!=timer){
                        red_p++;
                        red_p1++;
                        $('.redList_piclist').animate({left: ml - 186 + 'px'},'fast');
                    }
                }else{
                    red_p==timer;
                    return false;
                }
            })
            $('.red_prev').click(function(){
                linum = $('.redList_piclist li').length;
                var timer=index.modFoat(linum/4);
                ml = parseInt($('.redList_piclist').css('left'));
                if(red_p1<=timer){
                    if(red_p1>1){
                        red_p1--;
                        red_p--;
                        $('.redList_piclist').animate({left: ml + 186 + 'px'},'fast');
                    }
                }else{
                    red_p1==timer;
                    return false;
                }
            })
            ///////////////////////////////////// //

            $('.redpacket').click(function(){
                var list=data[parseInt($(this).attr('rpid'),10)];
                if(list.total==0){
                    var percent=0;
                }else{
                    var percent=index.modFoat(((list.used/list.total)*100));
                }
                // 切换上线类型
                if(list.limitType==1){
                    $("#limitName").html('金额');
                    $("#limitUnit").html('（元）');
                    var text_2=(list.used/100);
                }else{
                    $("#limitName").html('数量');
                    $("#limitUnit").html('（个）');
                    var text_2=list.used;
                }
                var option={
                       percent:percent,
                       w:300,
                       text_1:"已发",
                       text_2:text_2,
                       color_1:"#c24531",
                       color_2:"#f1705c",
                       color_3:"rgba(240, 80, 80, 0.65)"
                    }
                // 判断是否支持canvas
                if (!document.getElementById("redPanel").getContext) {
                    alert('浏览器不支持canvas,请更换浏览器！');
                }
                $("#redPanel").circle(option);
                $('.redpacket').removeClass('selected');
                $(this).addClass('selected');
                $(".red_lable").remove();
                $(this).append('<span class="red_lable" style="position: absolute;margin-left:-40px;width:100px;font-size:8px;">'+common.cutString($(this).attr('data-name'),'10')+'</span>');
            });
            $('.redpacket:first').triggerHandler('click');
        });
    },
    //加载卡券仪表盘
    loadCard:function(){
        this.loadCardUsed(function(data){
            var width=$('#cardLabel').width();
            var distance=width/(data.length-1);
            data.forEach(function(value,index){
                $('#cardLabel #cardTool .cardList').append('<li><a class="cardpacket" title="'+value.name+'" data-name="'+value.name+'" rpid="'+index+'"><span class="circle"></span></a></li>');
            });
            var card_p=1;
            var card_p1=1;
            // 计算长度
            linum = $('.cardList_piclist li').length;
            w = linum * 47;
            $('.cardList_piclist').css('width', w + 'px');
            // 激活左右方向键
            $('.card_next').click(function(){
                linum = $('.cardList_piclist li').length;
                var timer=index.modFoat(linum/4);
                ml = parseInt($('.cardList_piclist').css('left'));
                if(card_p<=timer){
                    if(card_p!=timer){
                        card_p++;
                        card_p1++;
                        $('.cardList_piclist').animate({left: ml - 186 + 'px'},'fast');
                    }
                }else{
                    card_p==timer;
                    return false;
                }
            })
            $('.card_prev').click(function(){
                linum = $('.cardList_piclist li').length;
                var timer=index.modFoat(linum/4);
                ml = parseInt($('.cardList_piclist').css('left'));
                if(card_p1<=timer){
                    if(card_p1>1){
                        card_p1--;
                        card_p--;
                        $('.cardList_piclist').animate({left: ml + 186 + 'px'},'fast');
                    }
                }else{
                    card_p1==timer;
                    return false;
                }
            })
            ///////////////////////////////////// //
            $('.cardpacket').click(function(){
                var list=data[parseInt($(this).attr('rpid'),10)];
                if(list.total==0){
                    var percent=0;
                }else{
                    var percent=index.modFoat(((list.used/list.total)*100));
                }
                var option={
                       percent:percent,
                       w:300,
                       text_1:"发放",
                       text_2:list.used,
                       color_1:"#2db3b6",
                       color_2:"#7bce5a",
                       color_3:"rgba(59, 192, 195, 0.65)"
                    }
                // 判断是否支持canvas
                if (!document.getElementById("cardPanel").getContext) {
                    alert('浏览器不支持canvas,请更换浏览器！');
                }
                $("#cardPanel").circle(option);
                $('.cardpacket').removeClass('selected');
                $(this).addClass('selected');
                $(".card_lable").remove();
                $(this).append('<span class="card_lable" style="position: absolute;margin-left:-40px;width:100px">'+common.cutString($(this).attr('data-name'),'10')+'</span>');
            });
            $('.cardpacket:first').triggerHandler('click');
        });
    },
    //加载积分仪表盘
    loadPoint:function(){
        this.loadPointUsed(function(data){
            var width=$('#pointLabel').width();
            var distance=width/(data.length-1);
            data.forEach(function(value,index){
                $('#pointLabel #pointTool .pointList').append('<li><a class="pointpacket" title="'+value.name+'" data-name="'+value.name+'" rpid="'+index+'"><span class="circle"></span></a></li>');
            });
            var point_p=1;
            var point_p1=1;
            // 计算长度
            linum = $('.pointList_piclist li').length;
            w = linum * 47;
            $('.pointList_piclist').css('width', w + 'px');
            // 激活左右方向键
            $('.point_next').click(function(){
                linum = $('.pointList_piclist li').length;
                var timer=index.modFoat(linum/4);
                ml = parseInt($('.pointList_piclist').css('left'));
                if(point_p<=timer){
                    if(point_p!=timer){
                        point_p++;
                        point_p1++;
                        $('.pointList_piclist').animate({left: ml - 186 + 'px'},'fast');
                    }
                }else{
                    point_p==timer;
                    return false;
                }
            })
            $('.point_prev').click(function(){
                linum = $('.pointList_piclist li').length;
                var timer=index.modFoat(linum/4);
                ml = parseInt($('.pointList_piclist').css('left'));
                if(point_p1<=timer){
                    if(point_p1>1){
                        point_p1--;
                        point_p--;
                        $('.pointList_piclist').animate({left: ml + 186 + 'px'},'fast');
                    }
                }else{
                    point_p1==timer;
                    return false;
                }
            })
            ///////////////////////////////////// //
            $('.pointpacket').click(function(){
                var list=data[parseInt($(this).attr('rpid'),10)];
                if(list.total==0||list.remain<0){
                    var percent=0;
                }else{
                    var percent=index.modFoat(((list.used/list.total)*100));
                }
                var option={
                       percent:percent,
                       w:300,
                       text_1:"使用",
                       text_2:list.used,
                       color_1:"#ff6b22",
                       color_2:"#f8a415",
                       color_3:"rgba(248, 164, 21, 0.65)"
                    }
                // 判断是否支持canvas
                if (!document.getElementById("pointPanel").getContext) {
                    alert('浏览器不支持canvas,请更换浏览器！');
                }
                $("#pointPanel").circle(option);
                $('.pointpacket').removeClass('selected');
                $(this).addClass('selected');
                $(".point_lable").remove();
                $(this).append('<span class="point_lable" style="position: absolute;margin-left:-40px;width:100px">'+common.cutString($(this).attr('data-name'),'10')+'</span>');
            });
            $('.pointpacket:first').triggerHandler('click');
        });
    },
    loadRegevent:function(rq){
        var p=1;
        var p1=1;
        setTimeout(getMainlist,"0");
        function getMainlist(){
            linum = $('.'+rq+' li').length;
            w = linum * 47;
            $('.'+rq+'_piclist').css('width', w + 'px');
        }
        $('.t_next').click(function(){
            var rq=$(this).attr('data-class');
            linum = $('.'+rq+' li').length;
            var timer=index.modFoat(linum/4);
            ml = parseInt($('.'+rq).css('left'));
            if(p<=timer){
                if(p!=timer){
                    p++;
                    p1++;
                    $('.'+rq).animate({left: ml - 186 + 'px'},'fast');
                }
            }else{
                p==timer;
                return false;
            }
        })
        $('.t_prev').click(function(){
            var rq=$(this).attr('data-class');
            linum = $('.'+rq+' li').length;
            var timer=index.modFoat(linum/4);
            ml = parseInt($('.'+rq+'').css('left'));
            if(p1<=timer){
                if(p1>1){
                    p1--;
                    p--;
                    $('.'+rq).animate({left: ml + 186 + 'px'},'fast');
                }
            }else{
                p1==timer;
                return false;
            }
        })
        //监听鼠标
        $(".container").hover(function(){
                $(this).find('img').addClass('hover');
                $(this).find('.desc').css('color', '#666');
            },function(){
                $(this).find('img').removeClass('hover');
                $(this).find('.desc').css('color', '#999');
        });
    },
    createLine:function(data){
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
                        data:['新增人数'],
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
                        right: '6%',
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
                                color: ['#e9e9e9']
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
                            name:'新增用户',
                            type:'line',
                            smooth:true,
                            symbolSize:[10,10],
                            itemStyle: {normal: {
                                color:'#63cdef'
                            }},
                            areaStyle: {normal: {
                                type: 'default'
                            }},
                            data:data['userNum']
                        }
                    ]
                };
            myChart.setOption(option);
    },
    //存在小数就进一除0外
    modFoat:function(v) {
         var _max = parseInt(v) + 1;
         if( _max - v < 1 ) {
             return _max;
         }
         return v;
    },
    tip:function(){
        var tip = parseInt($(".bat_tip").text()); 
        if(tip == 1){
            common.alert('该帐号可申请的码量不足1W，点击续费！',function(){
                window.location.href = '/cashier/renew';
            });
        }
    }
};

$(function(){
    index.init();
});
