var company = {
	init:function(){
		var _this=this;
		_this.validate();
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
        var oldpass = $("#oldpass").val();
        var newpass = $("#newpass").val();
        var renewpass = $("#renewpass").val();
        var data = {oldpass:oldpass,newpass:newpass,renewpass:renewpass}
        $.ajax( {    
            url:'/user/update_password', 
            data:data,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) { 
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/user/safe';
                            return;
                        }
                    });
                }else{
                    common.alert(d.errmsg);
                    return;
                }
            },
            error : function(d) {
                common.unloading();
                common.alert('请求失败');
            } 
        });
    }

}
$(function(){
    company.init();
});