var tagEdit = {
	init:function(){
		var _this=this;
        _this.valid();
        common.autoHeight();
	},
    valid:function(){
        var _this=this;
        $("#btnSave").on("click",function(){
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
        common.loading();
        $.ajax( {    
            url:'/tag/save', 
            data:data,
            type:'post',
            cache:false,
            dataType:'json',
            success:function(d) {
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/tag/lists';
                        }
                    });
                }else if(d.errcode==45157){
                    common.alert('标签名已存在，请检查不要重名');
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
};
$(function(){
	tagEdit.init();
});