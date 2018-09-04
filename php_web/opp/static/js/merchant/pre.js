var pre = {
	init:function(){
		var _this=this;
        _this.valid();
	},
    valid:function(){
        var _this = this;
        $("select").change(function(){
            $('#codeVersion').css({'background':'','border':''});
            $('#codeVersion').attr('edit-value',$(this).val());
        });
        $('#cancle_sub').on('click',function(){
            _this.cancle();
        });
        $('#sub').on('click',function(){
            var value = $('#codeVersion').attr('edit-value');
            if(value == '' || value == null || value == undefined){
                $('#codeVersion').css({'background':'#E8E89B','border-style':'solid','border-width':'1px','border-color':'#F57981'});
                common.alert('请选择码版本');
                return;
            }
            if(value.length > 0 ){
                _this.submit();
            }
        });
    },
    cancle:function(){
        window.location.href="/merchant"; 
    },
    submit:function(){
        var id = $('#mid').val();
        var value = $('#codeVersion').attr('edit-value');
        common.confirm('确定预审核',function(r){
            if(r == 1){
                $.post('/api/merchant/pre',{id:id,value:value},function(res){
                    if(res.errcode == 0){
                        common.alert('预审核成功',function(response){
                            if(response == 1){
                                window.location.href = "/merchant";
                            }
                        });
                    }else{
                        common.alert(res.errmsg);
                        return;
                    }
                });
            }
        });
    }
};
$(function(){
	pre.init();
});