/* global beforeSubmit */
/* global common */
var redpacketEdit = {
	init:function(){
		var _this=this;
        _this.valid();
        common.autoHeight();
	},
    valid:function(){
        var _this=this;
        $("#btnSave").on("click",function(){
			if(beforeSubmitAct()){
                var minAmount=Number($('#minAmount').val());
                var maxAmount=Number($('#maxAmount').val());
                if(minAmount<=maxAmount){
                    _this.submit();
                }else{
                    common.alert('最小金额不能大于最大金额');
                }
			}
		});
        $('input[name=levelType]').on('click',function(){
            var val=$(this).val();
            if(val==1){
                $('#rpType_0,#amtType_0,#limitType_0').click();
                $('.levelType').show();
                $('#rpType_1,#amtType_1,#limitType_1').parent('label').hide();
                $('.amtType_0,.limitType_0,.levelType_0,.levelType_1').hide();
            }else{
                $('#rpType_1,#amtType_1,#limitType_1').parent('label').show();
                $('.amtType_0,.limitType_0,.levelType_0,.levelType_1').show();
                $('.levelType').hide();
            }
            $('.tip-yellowsimple').remove();
            common.autoHeight();
        });
        $('input[name=rpType').on('click',function(){
            var val = $(this).val();
            if(val==1){
                $("#payment_0").attr("checked","checked");
                $('#payment_1').attr("disabled","disabled");
                $("#payment_1").removeAttr("checked");
            }
            if(val==0){
                $("#payment_1").removeAttr('disabled');
            }
        });
        $('input[name=amtType]').on('click',function(){
            $('.amtType_0,.amtType_1,.amtType_2').hide();
            var val=$(this).val();
            $('.amtType_'+val).show();
            common.autoHeight();
        });
        $('input[name=limitType]').on('click',function(){
            $('.limitType_0,.limitType_1').hide();
            var val=$(this).val();
            $('.limitType_'+val).show();
            common.autoHeight();
        });
        $('input[name=isDirect]').on('click',function(){
            var val = $(this).val();
            if(val==1){
                $("#payment_0").prop("checked",true);
                $("#payment_1").removeAttr("checked");
                $("#payment_0").parent().parent().parent().show();

                $("#withBalance_0").prop("checked",true);
                $("#withBalance_1").removeAttr("checked");
                $("#withBalance_0").parent().parent().parent().show();
            }
            if(val==0){
                $("#payment_1").removeAttr("checked");
                $("#payment_0").prop("checked",true);
                $("#payment_0").parent().parent().parent().hide();

                $("#withBalance_0").prop("checked",true);
                $("#withBalance_1").removeAttr("checked");
                $("#withBalance_0").parent().parent().parent().hide();
            }
        });
        if(levelType==1){
            $('#levelType_1').click();
            $('#levelType_0').parent('label').hide();
        }
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
        $('form input[type=radio]').each(function(e){
            var name=$(this).attr('name');
            var val=$(this).val();
            if($.trim(name)!=''){
                if($(this).is(':checked')){
                    data[name]=val;
                }
            }
        });
        common.loading();
        $.ajax( {    
            url:'/redpacket/save/', 
            data:data,
            type:'post',
            cache:false,
            dataType:'json',
            success:function(d) {
                common.unloading();
                if(d.errorCode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/redpacket/lists';
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
	redpacketEdit.init();
});