var merchant_edit = {
	init:function(){
		var _this=this;
        _this.valid();
	},
    valid:function(){
        var _this=this;
        $("#sub").on("click",function(){
			if(beforeSubmitAct()){
				_this.submit();
			}
		});
    },
    submit:function(){
    	var userName = $.trim($('#userName').val());
    	var phoneNum = $.trim($('#phoneNum').val());
    	var mail = $.trim($('#mail').val());
        var name = $.trim($('#mname').val());
    	var data = {
            username: userName, 
            mobile: phoneNum,
            mail: mail,
            name: name
        };
    	$.post("/api/merchant/add",data,function(d){
    		if(d.errcode == 0){
    			common.alert('添加成功',function(e){
    				if(e == 1){
    					location.href = '/merchant';
    				}
    			});
    		} else {
                common.alert(d.errmsg);
            }
    	});
    }
};
$(function(){
	merchant_edit.init();
});