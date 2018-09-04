var company = {
	init:function(){
		var _this=this;
		_this.validate();
		common.uploadInit('idCardImgUrl','/user/upload');
		common.uploadInit('licenseImgUrl','/user/upload');
		$.post("/user/get_company_info",{},function(data){
			//也可以使用each方法 
            if(data.status == 1 ){
            // if(data.status == 1 || data.status == 4){
                $("#name,#contact,#licenseNo,#idCardNum").css("background-color","#D9D9D9").attr("readonly","readonly");
                $("#licenseImgUrl,#idCardImgUrl").siblings(".hls-upload").children(".noselect").remove();
            }
			$("#name").val(data.name);
			$("#addetail").val(data.address);
    		$("#contact").val(data.contact);
    		$("#mail").val(data.mail);
    		$("#phoneNum").val(data.phoneNum);
    		$("#licenseNo").val(data.licenseNo);
    		$("#licenseImgUrl img").attr('src',data.licenseImgUrl);
    		$("#idCardNum").val(data.idCardNum);
    		$("#idCardImgUrl img").attr('src',data.idCardImgUrl);
    		$("#desc").val(data.desc);
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
    	var name = $("#name").val();
    	var addetail = $("#addetail").val();
    	var contact = $("#contact").val();
    	var mail = $("#mail").val();
    	var phoneNum = $("#phoneNum").val();
    	var licenseNo = $("#licenseNo").val();
    	var licenseImgUrl = $("input[name='licenseImgUrl']").val();
    	var idCardImgUrl = $("input[name='idCardImgUrl']").val();
    	var idCardNum = $("#idCardNum").val();
    	var desc = $("#desc").val();
    	var info = {name:name,addetail:addetail,contact:contact,mail:mail,phoneNum:phoneNum,licenseNo:licenseNo,licenseImgUrl:licenseImgUrl,idCardNum:idCardNum,idCardImgUrl:idCardImgUrl,desc:desc};
        $.ajax( {    
            url:'/user/get_company_update', 
            data:info,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) {    
                common.unloading();
                //控制器中还未实现
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/user/company';
                            return;
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
    company.init();
});