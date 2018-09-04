var dealer_edit = {
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
    	var name = $.trim($('#name').val());
    	var address = $.trim($('#address').val());
    	var mail = $.trim($('#mail').val());
        var ownerName = $.trim($('#ownerName').val());
        var phone = $.trim($('#phone').val());
        var code = $.trim($('#code').val());
        var id = $('#sub').attr('data-id');
    	var data = {
            name:name, 
            address: address,
            mail: mail,
            ownerName: ownerName,
            phone:phone,
            code:code,
            id:id
        };
        console.log(data);
    	$.post("/dealer/save_dealer",data,function(res){
            console.log(res);
            if(res.errcode === 0){
                common.alert('新增成功',function(e){
                    if(e == 1){
                        location.href = '/dealer/lists';
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
	dealer_edit.init();
});