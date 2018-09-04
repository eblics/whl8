common.extend({
    load:function(){
        this.menuview('trolley');
        
        var _this=this;
        var ids=[];
        var trolley=shopCart.show();
        for(var name in trolley){
            ids.push(name.substr(1));
        };
        this.connect('get_goods_list_by_trolley',{ids:ids.join(',')},function(result){
            if (result.errcode != 0) return;
            var list=$('.content .list');
            
            $('.statistics .curpoint').text(result.data.point);
            var data=result.data.list;
            for(var i=ids.length-1;i>=0;i--){
                for(var a=0;a<data.length;a++){
                    if(ids[i]==data[a].id){
                        data[a].amount=trolley['_'+ids[i]].amount;
                        list.append(_this.createItem(data[a]));
                        break;
                    }
                }
            }
            _this.calPoint();
            
            list.find('.delete').on('tap',function(){
                if(confirm('确定要删除吗？')){
                    shopCart.clear($(this).closest('.item').attr('goodid'));
                    $(this).closest('.item').remove();
                    _this.calPoint();
                }
            });
            list.find('.reduce').on('touchstart',function(){
                var obj=$(this).siblings('.amount');
                var amount=parseInt(obj.text());
                if(amount==1)
                    return;
                obj.text(amount-1);
                shopCart.remove($(this).closest('.item').attr('goodid'));
                _this.calPoint();
            });
            list.find('.increase').on('touchstart',function(){
                var obj=$(this).siblings('.amount');
                var amount=parseInt(obj.text());
                obj.text(amount+1);
                shopCart.add($(this).closest('.item').attr('goodid'));
                _this.calPoint();
            });
            list.find('.select').on('touchstart',function(){
                if($(this).hasClass('checked')){
                    $(this).removeClass('checked');
                    $('.statistics .select').removeClass('checked');
                }
                else{
                    $(this).addClass('checked');
                    var flag=false;
                    list.find('.select').each(function(){
                        if(!$(this).hasClass('checked')){
                            flag=true;
                            return false;
                        }
                    });
                    if(!flag){
                        $('.statistics .select').addClass('checked');
                    }
                }
                _this.calPoint();
            });
            $('.statistics .select').on('touchstart',function(){
                if($(this).hasClass('checked')){
                    list.find('.select').removeClass('checked');
                    $(this).removeClass('checked');
                }
                else{
                    list.find('.select').addClass('checked');
                    $(this).addClass('checked');
                }
                _this.calPoint();
            });
            $('.statistics .button').on('touchend',function(){
                if(!$(this).hasClass('disabled')){
                    orderList.set(_this.getGoodsList());
                    _this.redirect('/app/mall/submit.html');
                }
            });
        });
    },
    calPoint:function(){
        var sum=0;
        var count=0;
        $('.content .list .item').each(function(){
            if($(this).find('.select').hasClass('checked')){
                var id=$(this).attr('goodid');
                sum+=parseInt($(this).attr('price'))*shopCart.show()['_'+id].amount;
                count++;
            }
        });
        $('.statistics .sumpoint').text(sum+'积分');
        var cur=parseInt($('.statistics .curpoint').text());
        if(count==0 || cur<sum){
            $('.statistics .button').addClass('disabled');
        }
        else{
            $('.statistics .button').removeClass('disabled');
        }
    },
    getGoodsList:function(){
        var result=[];
        $('.content .list .item').each(function(){
            if($(this).find('.select').hasClass('checked')){
                var id=$(this).attr('goodid');
                result.push({id:id,amount:shopCart.show()['_'+id].amount});
            }
        });
        return result;
    },
    createItem:function(data){
        return '\
        <div class="item" price="' + data.price + '" goodid="' + data.id + '">\
            <span class="select checked"></span>\
            <span class="icon" style="' + (data.path==null?'':'background-image:url(' + data.path + ')') + '"></span>\
            <span class="info">\
                <span class="name">' + data.goodsName + '</span>\
                <span class="point">' + data.price + '积分</span>\
                <span class="spinner">\
                    <span class="reduce"></span>\
                    <span class="amount">' + data.amount + '</span>\
                    <span class="increase"></span>\
                </span>\
            </span>\
            <span class="button delete">删除</span>\
        </div>';
    }
});