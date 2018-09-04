//执行初始化
$(function(){
    mchBalancePage.init();
});
var mchBalancePage = {
    init:function(){
        this.btnEvent();
        this.createTable();
    },
    createTable:function(){
        var _this=this;
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": true,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "serverSide":false,//开启服务器分页
            "ajax":{
                url:'/wxpay/data_lists',//请求数据地址
                type:"POST",//请求方式
            },
            "columns": [
                        {"data":"orderId","class":"center"},
                        {
                            "data":"amount","class":"center",
                            "render": function (data,type,row) {
                                return (data/100).toFixed(2)+'元';
                            }
                        },
                        {"data":"createTime","class":"center"},
                        {
                            "data":"level","class":"center",
                            "render": function (data,type,row) {
                                if(data==0){
                                    return '<font color=green>收入</front>';
                                }else if(data==1){
                                    return '<font color=red>支出</front>';
                                }else{
                                    return '无';
                                }
                            }
                        },
                        {
                            "data":"level","class":"",
                            "render": function (data,type,row) {
                                if(data==0){
                                    return '[充值]<br>账户余额充值';
                                }else if(data==1){
                                    return '[消费]<br>企业VIP套餐购买/续费';
                                }else{
                                    return '无';
                                }
                            }
                        },
                        {
                            "data":"status","class":"center",
                            "render": function (data,type,row) {
                                if(data==1){
                                    return '<font color=green>已支付</front>';
                                }else if(data==0){
                                    return '<font color=red>未支付</front>';
                                }else{
                                    return '';
                                }
                            }
                        },
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
                
            }
        };
        $('#orderLog').dataTable(config);
    },
    btnEvent:function(){
        var _this=this;
        $('.btn-order').off().on('click',function(){
            common.transDialog(function(callback){
                var t=setTimeout(common.loading,500);
                $.post('/wxpay/get_mch_order_doing',{},function(d){
                    clearTimeout(t);
                    common.unloading();
                    if(d!=null && d!=''){
                        var html='<h1 style="font-size:20px;line-height:50px;">存在未操作完成的订单，不能发起新订单</h1>';
                        html+='<table class="table-form">';
                        html+='<tr><td class="center">订单号：'+d.orderId+'</td><td class="center">生成时间：'+d.createTime+'</td></tr>';
                        html+='<tr><td class="center bg"><strong>微信扫描二维码继续支付</strong></th><td class="center bg"><strong>取消此订单</strong></td></tr>';
                        html+='<tr><td class="center"><img src="/wxpay/order?orderid='+d.orderId+'" /></th><td class="center"><span data-id="'+d.orderId+'" class="btn btn-orange noselect btn-evt-cancel">取消订单</span></td></tr>';
                        html+='<tr><td class="center bb"><strong>金额：'+(d.amount/100).toFixed(2)+'元</strong></th><td class="center bb"></td></tr>';
                        html+='</table>';
                        callback(html);
                        _this.checkOrder(d.orderId);
                    }else{
                        var html='<h1 style="font-size:20px;line-height:50px;">帐户充值</h1><table class="table-form">';
                        html+='<tr><th style="height:120px;padding-left:30px;">输入充值金额：<input type="text" class="input" id="amount" style="width:80px" /> 元 <span class="btn btn-blue noselect btn-evt-order">确定</span> <em style="color:#666">（注：充值金额为大于等于1的整数）</em></th></tr>';
                        html+='<tr><td style="height:100px;padding-left:30px;color:#333;border-top:1px solid #ddd;"><b>提醒：</b>充值总额需要扣除1%的手续费（微信支付官方收费），实际充值到帐金额需要减去1%</td></tr>';
                        html+='</table>';
                        callback(html);
                    }
                    _this.adminEvent();
                },'json');
            });
        });
        
    },
    adminEvent:function(){
        var _this=this;
        $('.btn-evt-order').off().on('click',function(){
            var amount=$('#amount').val();
            var reg = /^[1-9]\d*$/;
            if(! reg.test(amount)){
                common.alert('金额输入有误');
                return;
            }
            var t=setTimeout(common.loading,500);
            $.post('/wxpay/order_add',{'amount':amount},function(d){
                clearTimeout(t);
                common.unloading();
                if(d.errcode==0){
                    var html='<h1 style="font-size:20px;line-height:50px;">扫码支付</h1>';
                    html+='<table class="table-form">';
                    html+='<tr><th class="center bb">订单号：'+d.data.orderId+' 　　　　　生成时间： '+d.data.createTime+' </th></tr>';
                    html+='<tr><td class="center bg"><strong>微信扫描二维码继续支付</strong></th></tr>';
                    html+='<tr><td class="center bb"><img src="/wxpay/order?orderid='+d.data.orderId+'" /></td></tr>';
                    html+='<tr><td class="center bb"><strong>金额：'+d.data.amount.toFixed(2)+'元</strong></th></tr>';
                    html+='</table>';
                    $('.transDialog .con').html(html);
                    _this.checkOrder(d.data.orderId);
                }else{
                    $('.transDialog .con').html('<h1 style="font-size:20px;line-height:150px;text-align:center;">操作失败，请重试</h1>');
                }
            },'json');
        });
        $('.btn-evt-cancel').off().on('click',function(){
            var orderId=$(this).attr('data-id');
            var reg = /^[1-9]\d*$/;
            if(! reg.test(orderId)){
                common.alert('订单号有误');
                return;
            }
            var t=setTimeout(common.loading,500);
            $.post('/wxpay/order_cancel',{'orderId':orderId},function(d){
                clearTimeout(t);
                common.unloading();
                if(d.errcode==0){
                    $('.transDialog .con').html('<h1 style="font-size:20px;line-height:150px;text-align:center;">成功取消订单</h1>');
                }else{
                    $('.transDialog .con').html('<h1 style="font-size:20px;line-height:150px;text-align:center;">操作失败，请重试</h1>');
                }
            },'json');
        });
    },
    checkOrder:function(orderId){
        var _this=this;
        $.post('/wxpay/check_mch_order',{'orderId':orderId},function(d){
            if(d.errcode==0){
                $('.transDialog .con').html('<h1 style="font-size:20px;line-height:150px;text-align:center;">支付成功</h1>');
                clearTimeout(window.ckT);
                setTimeout(function(){
                    window.location.reload();
                },3000);
            }else{
                window.ckT=setTimeout(function(){
                    _this.checkOrder(orderId);
                },3000);
            }
        },'json');
    }
    
};