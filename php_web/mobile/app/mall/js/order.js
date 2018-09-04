common.extend({
    load:function(){
        var _this=this;
        
        var status=this.getQueryString('status');
        if(status==1){
            $('.head>.tab:eq(1)').addClass('selected');
        }
        else{
            $('.head>.tab:first').addClass('selected');
            status=0;
        }
        $('.head>.tab').on('touchend',function(){
            location=$(this).attr('href')+'&mallid='+_this.mallid;
        });
        
        this.connect('get_orders_list/'+status,{},function(result){
            var data=[];
            var id=0;
            var children=null;
            result.data.forEach(function(value){
                if(value.id!=id){
                    children=[];
                    data.push({id:value.id,orderNum:value.orderNum,amount:value.amount,shippingStatus:value.shippingStatus,payStatus:value.payStatus,time:value.createTime,children:children});
                    id=value.id;
                }
                children.push({name:value.goodsName,price:value.goodsPrice,amount:value.goodsNumber,card:value.cardName,imageURL:value.path});
            });
            _this.createList(data,status);
            
            var list=$('.content .item');
            list.find('.delete').on('tap',function(){
                if(confirm('确定要删除吗？')){
                    var item=$(this).closest('.item');
                    _this.connect('delete_order/'+item.attr('orderid'));
                    item.remove();
                }
            });
            /*list.find('.cancel').on('tap',function(){
                if(confirm('确定要取消吗？')){
                    var item=$(this).closest('.item');
                    _this.connect('cancel_order/'+item.attr('orderid'));
                    item.remove();
                }
            });
            list.find('.payoff').on('tap',function(){
                _this.redirect('/app/mall/submit.html',{id:$(this).closest('.item').attr('orderid')});
            });*/
        });
    },
    createList:function(data,status){
        var html = '';
        data.forEach(function(value){
            var state='';
            if(status==0){
                if(value.payStatus=='0'){
                    state='未支付';
                }
                else if(value.shippingStatus=='0'){
                    state='未发货';
                }
                else if(value.shippingStatus=='1'){
                    state='已发货';
                }
                else if(value.shippingStatus=='2'){
                    state='已收货';
                }
            }
            else if(status==1){
                if(value.payStatus=='0'){
                    state='已取消';
                }
                else{
                    state='已完成';
                }
            }

            html+='\
            <div class="item" orderid="' + value.id + '">\
                <div class="body">\
                    <div class="title">';
                    if(value.orderNum!=null){
                        html+='\
                        <span class="order">订单号 : ' + value.orderNum + '</span>';
                    }
                    html+='\
                        <span class="info">\
                            <span class="time">' + value.time + '</span>\
                            <span class="state">' + state + '</span>\
                        </span>\
                    </div>';
                    
            value.children.forEach(function(v,i){
                html+='\
                    <div class="good ' + (status==0 && i==value.children.length-1 && value.payStatus!='0'?'last':'') + '">\
                        <span class="icon" style="' + (v.imageURL==null?'':'background-image:url(' + v.imageURL + ')') + '"></span>\
                        <span class="text">\
                            <span class="name">' + v.name + '</span>\
                            <span class="data">\
                                <span class="info">\
                                    <span class="point">' + (v.card==null?v.price+'积分':v.card+'卡券') + '</span>\
                                    <span class="amount">数量 : ' + v.amount + '</span>\
                                </span>';
                if(i==value.children.length-1 && value.amount!==null){
                    html+='\
                                <span class="statistics">\
                                    <span class="info">合计 :</span>\
                                    <span class="point">' + value.amount + '积分</span>\
                                </span>';
                }
                html+='\
                            </span>\
                        </span>\
                    </div>';
            });
            /*if(status==0 && value.payStatus=='0'){
                html+='\
                    <div class="buttons">\
                        <span class="cancel">取消订单</span>\
                        <span class="payoff red">去支付</span>\
                    </div>';
            }
            else
            if(status==1){
                html+='\
                    <div class="buttons">\
                        <span class="delete red">删除订单</span>\
                    </div>';
            }*/
            html+='\
                </div>\
            </div>';
        });
        $('.content').append(html);
    }
});