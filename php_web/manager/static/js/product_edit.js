/* global common */
var productAdd = {
	init:function(){
		var _this=this;
        _this.valid();
        common.ajaxSelect('categoryId','/product/list_categories');
        common.uploadInit('imgUrl','/product/upload');
	},
    valid:function(){
        var _this=this;
        $("#btnSave").on("click",function(){
			if(beforeSubmitAct()){
                var categoryId=$('#categoryId').val();
                if(categoryId==''){
                    common.alert('所属分类不能为空');
                    return;
                }
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
        $.ajax( {    
            url:'/product/save/', 
            data:data,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) {    
                common.unloading();
                if(d.errorCode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/product/lists';
                        }
                    });
                    
                }else{
                    common.alert(d.errorMsg);
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
	productAdd.init();
});