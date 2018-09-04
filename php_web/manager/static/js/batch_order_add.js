$(function(){
    order.init();
});
var order = {
    init:function(){
        var _this=this;
        common.autoHeight();
        $('[name=ordertype]').click(function(){
            var id=$(this).attr('id');
            $('[name=ordertype]').each(function(){
                var curId=$(this).attr('id');
                if($(this).attr('id')==id){
                    $('.'+curId).show();
                }
                else{
                    $('.'+curId).hide();
                }
            });
        });
        $('#btnSave').click(function(){
            if(beforeSubmitAct()){
                _this.submit();
            }
        });
        $('.file-upload').each(function(){
            var html='<div class="hls-upload textarea"><div class="choose noselect">选择</div><div class="filename">&nbsp;</div></div>';
            $(html).insertBefore($(this));
            $(this).change(function(){
                $(this).siblings('.hls-upload').children('.filename').html($(this).val());
            });
        });
        $('.hls-upload .choose').off().on('click',function(){
            $(this).parent('.hls-upload').next('.file-upload').trigger('click');
        });
    },
    submit:function(){
        var _this=this;
        common.loading();
        
        var data={ordertype:$('[name=ordertype]:checked').val()};
        $('[name][type=text]:visible').each(function(){
            var obj=$(this);
            var name=obj.attr('name');
            if(obj.val().trim()=='')
                return;
            if(obj.hasClass('Wdate')){
                var dates=obj.val().split('-');
                data[name]=(new Date(dates[0],parseInt(dates[1])-1,dates[2])).getTime()/1000;
            }
            else{
                data[name]=obj.val();
            }
        });
        if(appid==''||appsecret==''){
            common.unloading();
            common.alert('接口账号信息缺失');
            return;
        }
        if($('.ordertype_0').is(':visible')){
            if(data.expiretime==null&&data.shelflife==null){
                common.unloading();
                common.alert('『保质期』或『过期时间』不能为空');
                return;
            }
            if(data.shelflife!=null&&!/^(?:\d+[dwmy])+$/.test(data.shelflife)){
                common.unloading();
                common.alert('『保质期』格式不正确');
                return;
            }
        }
        
        var f=$('[name=codes]')[0].files[0];
        if(f.size>35*1024*1024){
            common.unloading();
            common.alert('『相关码』文件不能超过35M');
            return;
        }
        
        if($('[name=codetype]:checked').val()=='private'){
            data['ifpubcode']=false;
        }        
        
        $.post('/batch/order_exists_orderno',{orderno:data.orderno},function(d){
            if(d.result!=0){
                common.unloading();
                common.alert('『订单编号』已经存在');
                return;
            }
            var reader = new FileReader();
            reader.onload = (function(e) {
                data['codes']=e.target.result.split(/\s*\n/);
                
                _this.postMessage(apiurl+'app/token',{appid:appid,appsecret:appsecret},function(d){
                    _this.postMessage(apiurl+'order/put?token='+d.token,data,function(d){
                        common.unloading();
                        if(d.errcode==0){
                        	  $.post('/mchoprlog/batchorderlog/in',{orderno:data.orderno},function(){
                                  
                        		   common.alert('提交成功',function(d){
		                                if(d==1){
		                                    location.href='/batch/order_lists';
		                                }
                            	   });
                        	  });
                        }
                        else{
                            common.alert(d.errmsg);
                        }
                    });
                });
            });
            reader.readAsText(f);
        },'json');
    },
    postMessage:function(url,data,success){
        $.ajax({
            url:url,
            data:JSON.stringify(data),
            contentType:'application/json',
            type:'POST',
            cache:false,
            dataType:'json',
            xhrFields: {
                withCredentials: false
            },
            success:function(d) {
                success(d);
            },
            error:function(e) {
                common.unloading();
                common.alert('请求失败');
            }
        });
    }
};