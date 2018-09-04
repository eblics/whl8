var groupFishing={
	init:function(){
		this.event();
        window.currentFishingId=null;
	},
	event:function(){
        var _this=this;
        _this.count();
        setInterval(_this.count,5000);
		$('#btnRen').on('tap',_this.renZhadan);
        $('#btnLao').on('tap',_this.laoMovie);
        $('#btnLog').on('tap',_this.laoLog);
        $('.app_fishing_main,.app_fishing_ren').on('touchmove',function(){
            return false;
        });
        $('.amount li').on('tap',function(){
            $(this).addClass('cur').siblings('li').removeClass('cur');
        });
        $('.rule').on('tap',_this.rule);
	},
    laoMovie:function(){
        var _this=this;
        $('#btnLao,#btnRen,#btnLog').off('tap');
        var t1,t2,t3,t4,t5,t6,t7;
        clearTimeout(t1);
        clearTimeout(t2);
        clearTimeout(t3);
        clearTimeout(t4);
        clearTimeout(t5);
        clearTimeout(t6);
        clearTimeout(t7);
        $('.opacity').removeClass('bgcolor_flash');
        $('.dengguang').removeClass('flash');
        $('.laobg,.laotool,.laotool .shou').hide();
        $('.laobg,.laotool').show();
        var tool=$('.laotool');
        tool.css('background-position','0 0');
        t1=setTimeout(function(){
            tool.css('background-position','0 -160px');
            t2=setTimeout(function(){
                tool.css('background-position','0 -320px');
                t3=setTimeout(function(){
                    tool.css('background-position','0 -480px');
                    t4=setTimeout(function(){
                        tool.css('background-position','0 -320px');
                        t5=setTimeout(function(){
                            tool.css('background-position','0 -480px');
                            t6=setTimeout(function(){
                                tool.css('background-position','0 -320px');
                                $.ajax({
                                    url: "/hls_app/api/fishing.extract_redpacket",
                                    // data: {'groupId':groupId},
                                    type: 'post',
                                    dataType: 'json',
                                }).done(function(d){
                                    console.log(d.data + 'dd');
                                    groupFishing.count();
                                    if(d.errcode!=0){
                                        t7=setTimeout(function(){
                                            tool.css('background-position','0 -780px');
                                            tool.off().on('tap',groupFishing.unLaoMovie);
                                            $('#btnRen').on('tap',groupFishing.renZhadan);
                                            $('#btnLao').on('tap',groupFishing.laoMovie);
                                            $('#btnLog').on('tap',groupFishing.laoLog);
                                        },1000);
                                    }else{
                                        window.currentFishingId=d.data;
                                        t7=setTimeout(function(){
                                            tool.css('background-position','0 -620px');
                                            $('.laotool .shou').show();
                                            tool.off().on('tap',groupFishing.kaiXiangzi);
                                            $('#btnRen').on('tap',groupFishing.renZhadan);
                                            $('#btnLao').on('tap',groupFishing.laoMovie);
                                            $('#btnLog').on('tap',groupFishing.laoLog);
                                        },1000);
                                    }
                                }).fail(function(d){
                                    common.alert('操作失败，请重试');
                                });
                            },400);
                        },500);
                    },500);
                },500);
            },200);
        },200);
    },
    unLaoMovie:function(){
        $('.laotool').off('tap');
        $('.laobg,.laotool,.laotool .shou').hide();
        $('.opacity').addClass('bgcolor_flash');
        $('.dengguang').addClass('flash');
    },
    renZhadan:function(){
        groupFishing.unLaoMovie();
        $('.opacity').removeClass('bgcolor_flash');
        $('.dengguang').removeClass('flash');
        var dom=$('.app_fishing_ren');
        dom.hide();
        dom.find('.box').show();
        dom.find('.rening').hide().removeClass('large_to_small');
        dom.find('.close').off('tap').on('tap',function(){
            dom.hide();
            $('.opacity').addClass('bgcolor_flash');
            $('.dengguang').addClass('flash');
        });
        dom.find('.btn').off('tap').on('tap',function(){
            var amount=Number($('.amount li.cur').attr('data'));
            if(isNaN(amount)){
                return;
            }
            var t_ren=setTimeout(function(){
                common.loading();
            },600);
            $.ajax({
                url: "/hls_app/api/fishing.throw_bomb",
                data: {'amount':amount},
                type: 'post',
                dataType: 'json',
            }).done(function(d){
                clearTimeout(t_ren);
                common.unloading();
                if(d.errcode!=0){
                    common.alert(d.errmsg);
                    return;
                }
                $('.app_fishing_ren').find('.box').hide();
                $('.app_fishing_ren').find('.rening').show().addClass('large_to_small');
                setTimeout(function(){
                    $('.app_fishing_ren').hide();
                    $('.opacity').addClass('bgcolor_flash');
                    $('.dengguang').addClass('flash');
                },2500);
                // top.socket.emit('message','【捞红包】我扔了个'+(amount/100).toFixed(1)+'元红包，快来捞吧~(左下角+号进入)',1);
            }).fail(function(d){
                clearTimeout(t_ren);
                console.log(d);
                common.unloading();
                common.alert('操作失败，请重试');
                $('.opacity').addClass('bgcolor_flash');
                $('.dengguang').addClass('flash');
            });
        });
        dom.show();
    },
    kaiXiangzi:function(){
        groupFishing.unLaoMovie();
        $('.opacity').removeClass('bgcolor_flash');
        $('.dengguang').removeClass('flash');
        var dom=$('.app_fishing_kai');
        dom.hide();
        dom.find('.box').show();
        dom.find('.rening').hide().removeClass('large_to_small');
        dom.find('.close').off('tap').on('tap',function(){
            dom.hide();
            $('.opacity').addClass('bgcolor_flash');
            $('.dengguang').addClass('flash');
        });
        dom.find('#btnGoon').off('tap').on('tap',function(){
            var t_kai=setTimeout(function(){
                common.loading();
            },600);
            $.ajax({
                url: "/hls_app/api/fishing.open_box",
                data: {'box_id':window.currentFishingId},
                type: 'post',
                dataType: 'json',
            }).done(function(d){
                clearTimeout(t_kai);
                common.unloading();
                if(d.errcode!=0){
                    common.alert(d.errmsg);
                    return;
                }
                $('.app_fishing_kai').hide();
                groupFishing.kaiResult(d.data);
            }).fail(function(d){
                clearTimeout(t_kai);
                console.log(d);
                common.unloading();
                common.alert('操作失败，请重试');
                $('.opacity').addClass('bgcolor_flash');
                $('.dengguang').addClass('flash');
            });
        });
        dom.find('#btnBack').off('tap').on('tap',function(){
            $('.app_fishing_kai').find('.box').hide();
            $('.app_fishing_kai').find('.rening').show().addClass('large_to_small');
            setTimeout(function(){ 
                $('.app_fishing_kai').hide();
                $('.opacity').addClass('bgcolor_flash');
                $('.dengguang').addClass('flash');
            },2500);
        });
        dom.show();
    },
    kaiResult:function(data){
        var _this=this;
        var dom=$('.app_fishing_result');
        if(data.success==true){
            dom.find('.title').html('红包呀');
            dom.find('.pic').removeClass('baozha');
            // top.socket.emit('message','【捞红包】我捞了个'+(data.amount/100).toFixed(1)+'元红包，哈哈哈~(左下角+号进入)',1);
        }else if(data.success==false){
            dom.find('.title').html('红包炸弹');
            dom.find('.pic').addClass('baozha');
            // top.socket.emit('message','【捞红包】我捞了个炸弹，损失了'+(Math.abs(data.amount)/100).toFixed(1)+'元红包，呜呜呜~(左下角+号进入)',1);
        }
        dom.find('.pic span').html(data.amount/100+'元');
        dom.show();
        dom.find('.btn').off('tap').on('tap',function(){
            $('.app_fishing_result').hide();
            _this.laoMovie()
        });
        dom.find('.close').off('tap').on('tap',function(){
            dom.hide();
            $('.opacity').addClass('bgcolor_flash');
            $('.dengguang').addClass('flash');
        });
    },
    laoLog:function(){
        // console.log("dd");
        location.href='http://dev.m.lsa0.cn/hls_app/fishing/fishing_log';
    },
    rule:function(){
        var dom=$('.app_fishing_rule');
        dom.show();
        dom.find('.box').show();
        dom.find('.close').off('tap').on('tap',function(){
            dom.hide();
        });
    },
    count:function(){
        $.ajax({
            url: "/hls_app/api/fishing.count_box",
            // data: {'groupId':groupId},
            type: 'post',
            dataType: 'json',
        }).done(function(d){
            if(d.errcode!=0){
                $('#numTotal,#numRemain').html('0');
                return;
            }
            $('#numTotal').html(d.data.allCount);
            $('#numRemain').html(d.data.remainCount);
        }).fail(function(d){
            console.log(d);
            $('#numTotal,#numRemain').html('0');
        });
    }
};
$(function(){
	groupFishing.init();
});
