var wechat = {
	init:function(){
		var _this=this;
		$(".weinfo").css({"background-color":"#5A8EDD","color":"white"});
		_this.validate();
        common.uploadInit('wxQrcodeUrl_shop','/user/upload');
        common.uploadInit('subscribeImgUrl_shop','/user/upload');
        common.uploadInit('certPath_shop','/user/attaupload',1);
        common.uploadInit('keyPath_shop','/user/attaupload',1);
        common.uploadInit('caPath_shop','/user/attaupload',1);
		$.post("/user/get_wechat_info",{},function(data){
            if(data.data[0].status == 1){
            // if(data.status == 4 || data.status == 1){
                $("#wxAppId_shop,#wxName_shop,#wxYsId_shop,#wxAppSecret_shop,#wxPayKey_shop,#wxMchId_shop").css("background-color","#D9D9D9").attr("readonly","readonly");
                $("#wxQrcodeUrl_shop,#certPath_shop,#wxAppSecret_shop,#caPath_shop,#keyPath_shop").siblings(".hls-upload").children(".noselect").remove();
            }
		},'json');
	},
	validate:function(){
        var _this=this;
        $("#sub").bind("click",function(){
            if(beforeSubmitAct()){
                _this.submit();
            }
        });

    },
    submit:function(){
        var data={};
        $('form input,form textarea,form select').each(function(e){
            var name=$(this).attr('name');
            var val=$.trim($(this).val());
            if($.trim(name)!=''){
                data[name]=val;
            }
        });
        console.log(data);
        $.ajax( {    
            url:'/user/update_weixin_info', 
            data:data,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) { 
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/user/weixin';
                        }
                    });
                    
                }else{
                    common.alert(d.errmsg);
                }
            },
            error : function() {
                common.unloading();
                common.alert('请求失败');  
            } 
        });
    }

}
$(function(){
    wechat.init();
});