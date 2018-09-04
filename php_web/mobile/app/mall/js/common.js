var common={
    init:function(){
        // this.mallid=this.getQueryString('mallid');
        // if(this.mallid==null){
        //     this.error('没有这个商城');
        //     return;
        // }
        
        if(this.load!=null)
            this.load();
    },
    connect:function(url,data,callback){
        $.post('/mall/' + url, data,function(result){
            if(result.errcode==10){
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
                <span class="menu '+(selected=='home'?'selected':'')+'" href="/app/mall/home.html">\
                    <span class="icon home"></span>\
                    <span class="text">首页</span>\
                </span>\
                <span class="menu '+(selected=='list'?'selected':'')+'" href="/app/mall/list.html">\
                    <span class="icon list"></span>\
                    <span class="text">分类</span>\
                </span>\
                <span class="menu '+(selected=='trolley'?'selected':'')+'" href="/app/mall/trolley.html">\
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
        
        _this.connect('get_mall', {}, function(result){
            if(result.data!=null){
                $('.menuview .myboard').parent().attr('href','/user?mch_id='+result.data.mchid);
            }
            // var $body = $('body');
            // var $iframe = $("<iframe style='display:none;' src='/favicon.ico'></iframe>");
            // $iframe.on('load',function() {
            //   setTimeout(function() {
            //     $iframe.off('load').remove();
            //   }, 0);
            // }).appendTo($body);
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
        var str='v=1';
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