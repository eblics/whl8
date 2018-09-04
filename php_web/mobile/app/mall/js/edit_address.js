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
                        _this.redirect('/app/mall/address.html');
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