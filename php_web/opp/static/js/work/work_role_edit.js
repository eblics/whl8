var edit = {
	init:function(){
		var _this=this;
        _this.valid();
	},
    valid:function(){
        var _this=this;
        $("#btnAdd").on("click",function(){
			if(beforeSubmitAct()){
				_this.submit();
			}
		});
    },
    submit:function(){
        var id = $('#id').val();
    	var role = $.trim($('#role').val());
    	var name = $.trim($('#name').val());
    	var code = $.trim($('#code').val());
        var mail = $.trim($('#mail').val());
        var phoneNum = $.trim($('#phoneNum').val());
    	var data = {
            name:name, 
            code:code,
            role:role,
            id:id,
            mail:mail,
            phoneNum:phoneNum
        };
    	$.post("/workorder/save_role",data,function(res){
            console.log(res);
            if(res.errcode === 0){
                common.alert('保存成功',function(e){
                    if(e == 1){
                        location.href = '/workorder/w_role';
                    }
                });
            }else{
                common.alert('新增失败，请稍后再试！');
                return;
            }
    	});
    }
};
$(function(){
	edit.init();
});