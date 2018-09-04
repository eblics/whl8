$(function(){
    var wx;
    hlsjs.ready({mchid:0,success:function(){
        wx=hlsjs.wx();
        wx.ready(function() {
            $('.getaddress').removeClass('disabled');
        });
    }});
    
    var validate=function(){
        var con=$('.content');
        if($('#name',con).val()!='' &&
            $('#ownerName',con).val()!='' &&
            /^1[34578]\d{9}$/.test($('#ownerPhoneNum',con).val()) &&
            $('#city',con).text()!='' &&
            $('#address',con).val()!='' &&
            /^\d+$/.test($('#areaLen',con).val())){
            $('.submit',con).removeClass('disabled');
        }
        else{
            $('.submit',con).addClass('disabled');
        }
    };
    
    validate();
    
    $('.save').on('touchend',function(){
        if(true/*!$(this).hasClass('disabled')*/){
            $.post('/shop/save_shop_data/'+$('.save').attr('shopid'),{
                name:$('#name').val(),
                ownerName:$('#ownerName').val(),
                ownerPhoneNum:$('#ownerPhoneNum').val(),
                address:$('#address').val(),
                areaCode:$('#areaCode').val(),
                lat:$('#lat').val(),
                lng:$('#lng').val(),
                areaLen:$('#areaLen').val(),
            },function(d){
                location='/shop/shop_list';
            });
        }
    });
    
    $('.textbox').on('input',function(){
        validate();
    });
    
    $('.submit').on('touchend',function(){
        if(!$(this).hasClass('disabled')){
            $.post('/shop/submit_shop_data/'+$('.save').attr('shopid'),{
                name:$('#name').val(),
                ownerName:$('#ownerName').val(),
                ownerPhoneNum:$('#ownerPhoneNum').val(),
                address:$('#address').val(),
                areaCode:$('#areaCode').val(),
                lat:$('#lat').val(),
                lng:$('#lng').val(),
                areaLen:$('#areaLen').val(),
            },function(d){
                location='/shop/shop_list';
            });
        }
    });
    $('.getaddress').on('touchend',function(){
        if(!$(this).hasClass('disabled')){
            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                    
                    $.post('/shop/get_address_from_gps',{'lat':latitude,'lng':longitude},function(data){
                        $('#areaCode').val(data.areaCode);
                        $('#lat').val(latitude);
                        $('#lng').val(longitude);
                        $('#city').html(data.city);
                        $('#address').val(data.address);
                        validate();
                    });
                }
            });
        }
    });
});