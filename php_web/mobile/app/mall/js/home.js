common.extend({
    load:function(){
        this.menuview('home');
        
        var _this=this;
        this.connect('get_recommand_goods',{},function(result){
            if (result.errcode != 0) return;
            var container=$('.content');
            var height=(container.width()-1)/2;
            
            for(var i=0;i<1;i++){
                container.append(_this.createClassify('新品兑换'));            
                var list=$('.content .list:last');
                result.data.forEach(function(data){
                    list.append(_this.createItem(data,height));
                });
            }
            
            container.find('.item').on('tap',function(event){
                _this.redirect('/app/mall/detail.html',{id:$(this).closest('.item').attr('goodid')});
            });
            container.find('.button').on('tap',function(event){
                event.stopPropagation();
                var data=[{id:$(this).closest('.item').attr('goodid'),amount:1}];
                orderList.set(data);
                _this.redirect('/app/mall/submit.html');
            });
        });
        this.connect('get_point',{},function(result){
            if (result.errcode != 0) return;
            $('.top>.info>.point').text(result.data);
        });
    },
    createItem:function(data,height){
        return '\
        <span class="item" goodid="' + data.id + '">\
            <span class="icon" style="height:' + height + 'px;' + (data.path==null?'':'background-image:url(' + data.path + ')') + '"></span>\
            <span class="name">' + data.goodsName + '</span>\
            <span class="point">' + data.price + '积分</span>\
            <span class="button">立即兑换</span>\
        </span>';
    },
    createClassify:function(name){
        return '\
        <div class="area">\
            <div class="title">\
                <span class="line"></span>\
                <span class="text">' + name + '</span>\
            </div>\
            <div class="list">\
            </div>\
        </div>';
    }
});
