var configure = {
	init:function(){
        var _this = this;
		_this.validate();
        var val = $('#name').val();
        if(val.length>0){
            $('#sub').val('更新');
        }
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
        var name = $('#name').val();
        var desc = $('#desc').val();
        var id = $('#id').attr('value');
        if(id === null || id ===''||id === undefined){
            var txt = '商城申请提交成功，请等待审核！';
        }else{
            var txt = '商城信息更新成功！';
        }
        $.post('/mall/update_mall',{name:name,desc:desc},function(res){
            if(res.errcode === 0){
                common.alert(txt);
                return;
            }else{
                common.alert(res.errmsg);
                return;
            }
        });
    }

};
$(function(){
	configure.init();
});