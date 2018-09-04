$(function(){
    $('#birthtext').mobiscroll().date({
        theme: 'android-ics light',
        display: 'bottom',
        lang: 'zh',
        setText: '确定',
        cancelText: '取消',
        onSelect: function (valueText, inst) {
            $('#birthday').addClass('value');
            $('#birthday').text(valueText);
            validate();
        }
    });
    $('#citymenu').mobiscroll().treelist({
        theme: 'android-ics light',
        display: 'bottom',
        setText: '确定',
        cancelText: '取消',
        onSelect: function (valueText, inst) {
            var province=$('#citymenu [data-val='+inst.values[0]+']').html().split('<')[0];
            var city=$('#citymenu [data-val='+inst.values[1]+']').html();
            $('#cityinfo').addClass('value');
            $('#cityinfo').text(province+'-'+city);
            validate();
        }
    });
    if(birthday==''){
        $('#birthday').text($('#birthday').attr('placeholder'));
        birthday='1985-01-01';
    }
    else{
        $('#birthday').addClass('value');
    }
    var dates=birthday.split('-');
    $('#birthtext').mobiscroll('setValue',[parseInt(dates[0]),parseInt(dates[1])-1,parseInt(dates[2])],true);
    
    if(areacode.length>1){
        $('#citymenu').mobiscroll('setValue',areacode);
        $('#cityinfo').addClass('value');
    }
    else{
        $('#cityinfo').text($('#cityinfo').attr('placeholder'));
    }
    
    $('#cityinfo').on('touchend',function(){
        $('#citymenu').mobiscroll('show');
    });
    
    $('#birthday').on('touchend',function(){
        $('#birthtext').mobiscroll('show');
    });
    
    $('.textbox').on('input',function(){
        validate();
    });
    
    $('.textarea').on('touchstart',function(){
        if($('#address',this).attr('contenteditable')==null)
            $('#address',this).attr('contenteditable','true');
    });
    
    var validate=function(){
        var con=$('.content');
        if($('#realname',con).val()!='' &&
            /^1[34578]\d{9}$/.test($('#mobile',con).val()) &&
            $('#cityinfo',con).hasClass('value') &&
            $('#birthday',con).hasClass('value') &&
            $('#address',con).text()!='' &&
            $('#address',con).text().length<=parseInt($('#address',con).attr('maxLength'))){
            $('.save',con).removeClass('disabled');
        }
        else{
            $('.save',con).addClass('disabled');
        }
    };
    
    validate();
    
    $('.save').on('touchend',function(){
        if(!$(this).hasClass('disabled')){
            $.post('/user/api/user.update',{
                mchid:mchid,
                realname:$('#realname').val(),
                mobile:$('#mobile').val(),
                city:$('#citymenu').mobiscroll('getValue')[1],
                birthday:$('#birthday').text(),
                address:$('#address').text()
            },function(d){
                location='/user?mch_id='+mchid;
            });
        }
    });
});