var tcloud = {
	init:function(){
		var _this=this;
		_this.validate();
		_this.initRadio();
        //var hinfo = $('.table-form').attr("hinfo");
		$.post("/user/get_tcloud",{},function(response){
			if(response.errcode == 0){
				$('#secretId').val(response.data.secretId);
				$('#secretKey').val(response.data.secretKey);
				$('#interfaceStatus').html(response.data.message);
				if(response.data.status == 0){
					$('#interfaceStatus').css('color','green');
				}else{
					$('#interfaceStatus').css('color','red');
					$("form input[name=ignoreLevel]").attr("disabled","disabled");
					$("form input[name=ignoreLevel]").parent().css("color","#999");
					$("form input[name=isUse]").attr("disabled","disabled");
					$("form input[name=isUse]").parent().css("color","#999");
				}
			}            
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
            var val=$.trim($(this).val());
            if($.trim(name)!=''){
                data[name]=val;
            }
        });
        $('form input[type=radio]').each(function(e){
            var name=$(this).attr('name');
            var val=$(this).val();
            if($.trim(name)!=''){
                if($(this).is(':checked')){
                    data[name]=val;
                }
            }
        });
        $.ajax( {    
            url:'/user/update_tcloud', 
            data:data,    
            type:'post',    
            cache:false,    
            dataType:'json',    
            success:function(d) { 
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/user/tcloud';
                        }
                    });
                    
                }else{
                    common.alert(d.errmsg);
                }
            },
            error : function() {
                common.unloading();
                common.alert('请求失败');  
            } 
        });
    },
    initRadio:function(){
    	  $("form input[name=validLevel]").click(function(){
    		  var val = $(this).val();
    	        $('form input[name=validLevel]').each(function(e){
    	            var val1=$(this).val();
    	            if(val1 <= val){
    	                $(this).parent().css('color','green');
    	            }else{
    	            	$(this).parent().css('color','black');
    	            }
    	        });
		  });
    	  $("form input[name=ignoreLevel]").click(function(){
    		  var val = $(this).val();
    	        $('form input[name=ignoreLevel]').each(function(e){
    	            var val1=$(this).val();
    	            if(val1 < val){
    	                $(this).parent().css('color','black');
    	            }else{
    	            	$(this).parent().css('color','red');
    	            }
    	        });
		  });
    }

}
$(function(){
    tcloud.init();
});