common.extend({
    load:function(){
        var _this=this;
        var addressid=this.getQueryString('addressid');
        
        var ids=[];
        var orders=orderList.get();
        orders.forEach(function(value){
            ids.push(value.id);
        });
        this.connect('get_goods_list_by_submit',{addressid:addressid,ids:ids.join(',')},function(result){
            var list=$('.content .list');
            
            var needAddress=false;
            var sumPoint=0;
            var data=result.data.list;
            for(var i=0;i<orders.length;i++){
                for(var a=0;a<data.length;a++){
                    if(orders[i].id==data[a].id){
                        data[a].amount=orders[i].amount;
                        data[a].point=parseInt(data[a].price)*parseInt(data[a].amount);
                        sumPoint+=data[a].point;
                        list.append(_this.createItem(data[a]));
                        if(data[a].isViral==0)
                            needAddress=true;
                        break;
                    }
                }
            }
            
            if(result.data.address!=null){
                addressid=result.data.addressid;
                _this.showAddress(result.data.address.split('|'));
            }
            $('.submit .point').text(sumPoint+'积分');

            if(needAddress){
                $('.address').show();
                $('.address').on('tap',function(){
                    _this.redirect('/app/mall/address.html');
                });
            }
            else{
                $('.submit>.button').removeClass('disabled');
            }
            
            $('.submit>.button').on('touchend',function(){
                if(!$(this).hasClass('disabled') && confirm('确定要支付吗？')){
                    _this.connect('create_order',{addressid:addressid,list:JSON.stringify(orderList.get())},function(result){
                        if(result.errcode==1){
                            alert(result.errmsg);
                        }
                        else{
                            orderList.get().forEach(function(value){
                                shopCart.remove(value.id,value.amount);
                            });
                            _this.redirect('/app/mall/order.html',{status:result.data});
                        }
                    });
                }
            });
        });
    },
    createItem:function(data){
        return '\
        <div class="item">\
            <span class="name">' + data.goodsName + '</span>\
            <span class="amount">×' + data.amount + '</span>\
            <span class="point">' + data.point + '积分</span>\
        </div>';
    },
    showAddress:function(data){
        var html='\
        <span class="basic">\
            <span class="name">' + data[0] + '</span>\
            <span class="telephone">' + data[1] + '</span>\
        </span>\
        <span class="text">' + data[2] + data[3] + '</span>';
        $('.address>.info').removeClass('note');
        $('.address>.info').html(html);
        $('.submit>.button').removeClass('disabled');
    }
});