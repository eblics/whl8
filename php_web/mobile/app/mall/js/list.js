common.extend({
    load:function(){
        this.menuview('list');
        var _this=this;
        
        $('.menuview .trolley').parent('.menu').on('webkitAnimationEnd',function(event){
            $(this).removeClass('animated');
        });
        
        this.loadData();
        
        this.connect('get_categories_list',{},function(result){
            if (result.errcode != 0) return;
            var menulist=$('.menulist');
            var menutitle=$('.head>.title>.menu>.text');
            menulist.append('<div class="menuitem selected">全部分类</div>');
            result.data.forEach(function(data){
                menulist.append('<div class="menuitem" categoryid="' + data.id + '">' + data.name + '</div>');
            });
            $('.menuitem',menulist).on('tap',function(event){
                closeMenu();
                menutitle.text($(this).text());
                $('.menuitem',menulist).removeClass('selected');
                $(this).addClass('selected');
                _this.loadData($(this).attr('categoryid'));
            });
        });
        
        var head=$('.head');
        var body=$('body');
        var menulist=$('.menulist');
        head.find('.title>.menu').on('tap',function(event){
            if($('.masklayer',body).size()!=0){
                closeMenu();
            }
            else{
                openMenu();
            }
        });
        var openMenu=function(){
            var height=$(window).height()-$('.menuview').height()-$('.head>.title').height()-1;
            if(menulist.height()>height){
                menulist.css('height',height);
            }
            else{
                menulist.on('touchmove',function(event){
                    event.preventDefault();
                });
            }
            menulist.show();
            body.append('<div class="masklayer"></div>');
            body.children('.masklayer').on('touchmove',function(event){
                event.preventDefault();
            });
        };
        
        var closeMenu=function(){
            menulist.hide();
            menulist.css('height','auto');
            body.children('.masklayer').off('touchmove');
            body.children('.masklayer').remove();
            menulist.off('touchmove');
        };
    },
    loadData:function(categoryid){
        var _this=this;
        var list=$('.content .list');
        list.empty();
        
        this.connect('get_goods_list'+(categoryid==null?'':'/'+categoryid),{},function(result){
            if (result.errcode != 0) return;
            result.data.forEach(function(data){
                list.append(_this.createItem(data));
            });
            list.find('.item').on('tap',function(event){
                _this.redirect('/app/mall/detail.html',{id:$(this).closest('.item').attr('goodid')});
            });
            list.find('.hollow').on('tap',function(event){
                event.stopPropagation();
                shopCart.add($(this).closest('.item').attr('goodid'));
                $('.menuview .trolley').parent('.menu').addClass('animated');
            });
            list.find('.solid').on('tap',function(event){
                event.stopPropagation();
                var data=[{id:$(this).closest('.item').attr('goodid'),amount:1}];
                orderList.set(data);
                _this.redirect('/app/mall/submit.html');
            });
        });
    },
    createItem:function(data){
        return '\
        <div class="item" goodid="' + data.id + '">\
            <span class="icon" style="' + (data.path==null?'':'background-image:url(' + data.path + ')') + '"></span>\
            <span class="info">\
                <span class="name">' + data.goodsName + '</span>\
                <span class="point">' + data.price + '积分</span>\
            </span>\
            <span class="buttons">' +
                (data.isViral==1?'':'<span class="hollow">加入购物车</span>') +
                '<span class="solid">立即支付</span>\
            </span>\
        </div>';
    }
});