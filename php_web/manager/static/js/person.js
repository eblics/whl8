var person = {
	init:function(){
		var _this=this;
		_this.validate();
        common.uploadInit('idCardImgUrl','/user/upload');
		$.post("/user/get_person_info",{},function(data){
            console.log(data);
            $("#userName").val(data.data[0].userName);
            $("#realName").val(data.data[0].realName);
            $("#mail").val(data.data[0].mail);
            $("#phoneNum").css("background-color","#D9D9D9");
            $("#phoneNum").val(data.data[0].phoneNum);
            $("#idCardNum").val(data.data[0].idCardNum);
            $("#idCardImgUrl img").attr('src',data.data.idCardImgUrl);
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
            var val=$(this).val();
            if($.trim(name)!=''){
                data[name]=val;
            }
        });
        // console.log(data);
        $.ajax( {    
            url:'/user/update_person_info', 
            data:data,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) { 
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/user/person';
                            return;
                        }
                    });
                }else{
                    common.alert(d.errmsg);
                    return;
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
    person.init();
});