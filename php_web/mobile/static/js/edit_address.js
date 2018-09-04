var common={
    init:function(){
        this.mallid=this.getQueryString('mall_id');
        this.cardId=this.getQueryString('card_id');
        if(this.mallid==null){
            this.error('没有这个商城');
            return;
        }
        
        if(this.load!=null)
            this.load();
    },
    connect:function(url,data,callback){
        var self = this;
        if(data==null)
            data={};
        data.mallid=this.mallid;

        $.post('/mall/'+url,data,function(result){
            if(result.errcode==10){
                alert(result.errmsg);
                return;
            }
            if(callback!=null)
                callback(result);
        });
    },
    menuview:function(selected){
        var _this=this;
        var menuHtml='\
            <div class="menuview">\
                <span class="menu '+(selected=='home'?'selected':'')+'" href="/app/mall/home.html?mallid=' + _this.mallid + '">\
                    <span class="icon home"></span>\
                    <span class="text">首页</span>\
                </span>\
                <span class="menu '+(selected=='list'?'selected':'')+'" href="/app/mall/list.html?mallid=' + _this.mallid + '">\
                    <span class="icon list"></span>\
                    <span class="text">分类</span>\
                </span>\
                <span class="menu '+(selected=='trolley'?'selected':'')+'" href="/app/mall/trolley.html?mallid=' + _this.mallid + '">\
                    <span class="icon trolley"></span>\
                    <span class="text">购物车</span>\
                </span>\
                <span class="menu '+(selected=='myboard'?'selected':'')+'" ">\
                    <span class="icon myboard"></span>\
                    <span class="text">我的</span>\
                </span>\
            </div>\
        ';
        var height=55;
        if($('.content').nextAll().size()!=0){
            $('.content').nextAll().each(function(){
                $(this).css('bottom',parseInt($(this).css('bottom').slice(0,-2))+height);
            });
        }
        $('.content').css('padding-bottom',parseInt($('.content').css('padding-bottom').slice(0,-2))+height);
        $('body').append(menuHtml);
        $('.menuview>.menu').on('touchend',function(){
            if($(this).attr('href')!=null)
                location=$(this).attr('href');
        });
        
        _this.connect('get_mall/'+_this.mallid,{},function(result){
            var title='积分商城';
            if(result.data!=null){
                $('.menuview .myboard').parent().attr('href','/myboard/index/'+result.data.mchid);
                title=result.data.name;
            }
            var $body = $('body');
            document.title = title;
            var $iframe = $("<iframe style='display:none;' src='/favicon.ico'></iframe>");
            $iframe.on('load',function() {
              setTimeout(function() {
                $iframe.off('load').remove();
              }, 0);
            }).appendTo($body);
        });
    },
    getQueryString:function(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    },
    error:function(text){
        $('body').html('<div class="warning">' + text + '</div>');
    },
    redirect:function(uri,params){
        var str='mall_id='+this.mallid;
        if(params!=null){
            for(var name in params){
                str+='&'+name+'='+params[name];
            }
        }
        location=uri+'?'+str;
    },
    extend:function(methods){
        for(var method in methods){
            this[method]=methods[method];
        }
    }
};

var orderList={
    set:function(list){
        window.localStorage.setItem('order',JSON.stringify(list));
    },
    get:function(){
        var list=window.localStorage.getItem('order');
        if(list==null){
            list=[];
        }
        else{
            try{
                list=JSON.parse(list);
            }catch(e){
                list=[];
            }
        }
        return list;
    }
};
var ShoppingTrolley=function(){
    var trolley=window.localStorage.getItem('trolley');
    if(trolley==null){
        trolley={};
    }
    else{
        try{
            trolley=JSON.parse(trolley);
        }catch(e){
            trolley={};
        }
    }
    var updateStorage=function(){
        window.localStorage.setItem('trolley',JSON.stringify(trolley));
    };
    this.add=function(id,amount){
        if(trolley['_'+id]==null)
            trolley['_'+id]={amount:0};
        if(amount==null)
            amount=1;
        trolley['_'+id].amount+=amount;
        updateStorage();
    };
    this.remove=function(id,amount){
        if(trolley['_'+id]==null)
            return;
        if(amount==null)
            amount=1;
        trolley['_'+id].amount-=amount;
        if(trolley['_'+id].amount<=0)
            delete trolley['_'+id];
        updateStorage();
    };
    this.clear=function(id){
        if(id!=null)
            delete trolley['_'+id];
        updateStorage();
    };
    this.show=function(){
        return trolley;
    };
};
var shopCart=new ShoppingTrolley();
$(function(){
    common.init();
});
common.extend({
    load:function(){
        var _this=this;
        var addressid=this.getQueryString('id');
        
        this.connect('get_address'+(addressid!=null?'/'+addressid:''),{},function(result){
            
            $('#areamenu').html(result.data.areahtml);
            $('#areamenu').mobiscroll().treelist({
                theme: 'android-ics light',
                display: 'bottom',
                setText: '确定',
                cancelText: '取消',
                onShow: function (html, valueText, inst) {
                    $('.android-ics .dww').css('width',($(window).width()-35)/3);
                },
                onSelect: function (valueText, inst) {$('.android-ics .dww').css('width','90px');
                    var province=$('#areamenu [data-val='+inst.values[0]+']').html().split('<')[0];
                    var city=$('#areamenu [data-val='+inst.values[1]+']').html().split('<')[0];
                    var area=$('#areamenu [data-val='+inst.values[2]+']').html();
                    $('#areainfo').addClass('value');
                    $('#areainfo').text(province+city+area);
                    _this.validate();
                }
            });
            
            if(result.data.address){
                $('#receiver').val(result.data.address.receiver);
                $('#phoneNum').val(result.data.address.phoneNum);
                $('#address').val(result.data.address.address);
                $('#areainfo').addClass('value');
                $('#areainfo').text(result.data.address.area);
                var areaCode=result.data.address.areaCode;
                var level=result.data.address.level;
                var code;
                if(level==2)
                    code=[areaCode.slice(0,2)+'0000',areaCode.slice(0,4)+'00',areaCode];
                else
                    code=[areaCode.slice(0,2)+'0000',areaCode];
                $('#areamenu').mobiscroll('setValue',code);
            }
            
            $('#areainfo').on('touchend',function(){
                $('#areamenu').mobiscroll('show');
            });
            _this.validate();
            $('.textbox').on('input',function(){
                _this.validate();
            });
            $('.save').on('touchend',function(){
                if(!$(this).hasClass('disabled')){
                    var areacode=$('#areamenu').mobiscroll('getValue')[2];
                    if(areacode==null)
                        areacode=$('#areamenu').mobiscroll('getValue')[1];
                    _this.connect('update_address'+(addressid!=null?'/'+addressid:''),{
                            receiver:$('#receiver').val(),
                            phone:$('#phoneNum').val(),
                            areacode:areacode,
                            address:$('#address').val()},function(result){
                        _this.redirect('/card/choice_address', {card_id: _this.cardId});
                    });
                }
            });
        });
    },
    validate:function(){
        var con=$('.content');
        if($('#receiver',con).val()!='' &&
            /^1[34578]\d{9}$/.test($('#phoneNum',con).val()) &&
            $('#areainfo',con).hasClass('value') &&
            $('#address',con).val()!=''){
            $('.save',con).removeClass('disabled');
        }
        else{
            $('.save',con).addClass('disabled');
        }
    }
});