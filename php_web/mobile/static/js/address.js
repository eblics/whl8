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
        if(data==null)
            data={};
        data.mallid=this.mallid;
        $.post('/mall/'+url,data,function(result){
            if(result.errcode==10){
                location=result.data;
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
        var str='mall_id='+this.mallid + '&card_id=' + this.cardId;
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
        
        this.connect('get_addresses_list',{},function(result){
            var list=$('.content');
            result.data.forEach(function(data){
                list.append(_this.createItem(data));
            });
            
            list.find('.address>.info').on('tap',function(){
                var addressId = $(this).closest('.item').attr('addressid');
                _this.settleCards(_this.cardId, addressId);
            });
            list.find('.edit').on('tap',function(){
                var params={id:$(this).closest('.item').attr('addressid')};
                _this.redirect('/card/edit_address',params);
            });
            list.find('.delete').on('tap',function(){
                if(confirm('确定要删除吗？')){
                    var item=$(this).closest('.item');
                    _this.connect('delete_address/'+$(this).closest('.item').attr('addressid'),{},function(result){
                        item.remove();
                    });
                }
            });
            list.find('.default').on('tap',function(){
                if($(this).hasClass('checked')){
                    $('.default').removeClass('checked');
                    _this.connect('default_address',{},function(){});
                }
                else{
                    $('.default').removeClass('checked');
                    $(this).addClass('checked');
                    _this.connect('default_address/'+$(this).closest('.item').attr('addressid'),{},function(){});
                }
            });
        });
        
        $('.add').on('touchend',function(){
            _this.redirect('/card/edit_address');
        });
    },
    createItem:function(data){
        return '\
        <div class="item" addressid="' + data.id + '">\
            <div class="address">\
                <span class="info">\
                    <span class="basic">\
                        <span class="name">' + data.receiver + '</span>\
                        <span class="telephone">' + data.phoneNum + '</span>\
                    </span>\
                    <span class="text">' + data.area + data.address + '</span>\
                </span>\
                <span class="edit"></span>\
                <span class="delete"></span>\
            </div>\
            <div class="default ' + (data.isDefault==1?'checked':'') + '">\
                <span class="icon"></span>\
                <span class="info">【默认地址】</span>\
            </div>\
        </div>';
    },

    settleCards: function($cardId, $addressId) {
        hls.util.Dialog.showLoading();
        $.post('/card/settle/' + $cardId + '/' + $addressId, function(resp) {
            hls.util.Dialog.closeLoading();
            if (!resp.errcode) {
                location.href = '/card/settle_result';
            } else {
                hls.util.Dialog.showMessage(resp.errmsg);
            }
        }).fail(function(err) {
            hls.util.Dialog.showMessage('无法连接服务器！', function() {
                hls.util.Dialog.closeLoading();
            });
        });
    }
});