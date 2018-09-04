var wechat = {
	init:function(){
		var _this=this;
		$(".weinfo").css({"background-color":"#5A8EDD","color":"white"});
		_this.validate();
        common.uploadInit('wxQrcodeUrl','/user/upload');
        common.uploadInit('subscribeImgUrl','/user/upload');
        common.uploadInit('certPath','/user/attaupload',1);
        common.uploadInit('keyPath','/user/attaupload',1);
        common.uploadInit('caPath','/user/attaupload',1);
        var hinfo = $('.table-form').attr("hinfo");
		$.post("/user/get_wechat_info",{},function(data){
            if(data.data[0].status == 1){
            // if(data.status == 1 || data.status == 4){
                $("#wxAppId,#wxName,#wxYsId,#wxAppSecret,#wxPayKey,#wxMchId").css("background-color","#D9D9D9").attr("readonly","readonly");
                $("#wxQrcodeUrl,#certPath,#wxAppSecret,#caPath,#keyPath").siblings(".hls-upload").children(".noselect").remove();
            }
            $("#wxAppId").val(data.data[0].wxAppId);
            $("#wxName").val(data.data[0].wxName);
            $("#wxYsId").val(data.data[0].wxYsId);
            $("#wxAppSecret").val(data.data[0].wxAppSecret);
            $("#wxPayKey").val(data.data[0].wxPayKey);
            $("#wxMchId").val(data.data[0].wxMchId);
            $("#wxQrcodeUrl img").attr('src',data.data[0].wxQrcodeUrl);
            $("#subscribeMsg").val(data.data[0].subscribeMsg);
            // $("#subscribeImgUrl img").attr('src',data.data[0].subscribeImgUrl);
            $("#wxSendName").val(data.data[0].wxSendName);
            $("#wxActName").val(data.data[0].wxActName);
            $("#wxRpTotalNum").val(data.data[0].wxRpTotalNum);
            $("#wxWishing").val(data.data[0].wxWishing);
            $("#wxRemark").val(data.data[0].wxRemark);
            $("#wxSendTip").val(data.data[0].wxSendTip);
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
        $('form input[type=radio]').each(function(e){
            var name=$(this).attr('name');
            var val=$(this).val();
            if($.trim(name)!=''){
                if($(this).is(':checked')){
                    data[name]=val;
                }
            }
        });
        $.ajax( {    
            url:'/user/update_wechat_info', 
            data:data,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) { 
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/user/wechat';
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