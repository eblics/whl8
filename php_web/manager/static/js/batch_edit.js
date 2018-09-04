/* global beforeSubmit */
/* global common */
var batchEdit = {
	init:function(){
		var _this=this;
        _this.valid();
        common.ajaxSelectSub('categoryId','/product/catedata','productId','/product/prodata');
        common.autoHeight();
	},
    valid:function(){
        var _this=this;
        $("#btnSave").on("click",function(){
			if(beforeSubmitAct()){
				_this.submit();
			}
		});
        var ifPubCode=$('#ifPubCode');
        ifPubCode.on('click',function(){
            if($(this).is(':checked')){
               $(this).val(1);
            }else{
                $(this).val(0);
            }
        });
        if(ifPubCode.val()==1){
            ifPubCode.prop("checked",true);
        }
        $('#onlyCategory').on('click',function(){
            if($(this).is(':checked')){
               $('.product-tr').hide();
            }else{
               $('.product-tr').show();
            }
            common.autoHeight();
        });
        $('#widthProduct').on('click',function(){
            if($(this).is(':checked')){
               $('.product').show();
               $('#onlyCategory').removeAttr('checked');
            }else{
               $('.product').hide();
            }
            common.autoHeight();
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
        if(!$('#widthProduct').is(':checked')){
            data['categoryId']='';
            data['productId']='';
        }
        if($('#onlyCategory').is(':checked')){
            data['productId']='';
        }else{
            if($('#widthProduct').is(':checked')){
                if($.trim($('#productId').val())==''){
                    common.alert('请选择产品');
                    return;
                }
            }
		}
        common.loading();
        $.ajax( {    
            url:'/batch/save/', 
            data:data,
            type:'post',
            cache:false,
            dataType:'json',
            success:function(d) {
                common.unloading();
                if(d.errorCode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/batch/lists';
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
	batchEdit.init();
});