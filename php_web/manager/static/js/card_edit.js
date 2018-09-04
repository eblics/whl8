var cardEdit = {
	init:function(){
		var _this=this;
        common.uploadInit('imgUrl','/card/upload');
        // if(window.location.search.length != 0){
            // $('select').attr("disabled","desabled");
            $('.select').parent().parent().hide();
        // }
        _this.change();
	},
    change:function(){
        var _this = this;
        $('#select').change(function(){
            var val = $(this).children('option:selected').val(); 
            $("#select").find("option[value='"+val+"']").attr("selected",true); 
            $('option').each(function(){
                if($(this).val() != val){
                    $(this).removeAttr('selected');
                }
            });
            $('#select').attr('edit-value',val);
        });
        _this.valid();
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
		var data = {};
		$('form input,form textarea,form select').each(function(){
			var name = $(this).attr('name');
			var val = $(this).val();
			if($.trim(name)!=''){
				data[name] = val;
			}
		});

        data['thirdParty'] = thirdParty;
        // 如果是第三方卡券，判断是否有可用的优惠券或优惠码
        if (data['thirdParty']) {
            if (data['couponGroupId'] == 0) {
                common.alert('没有可用的优惠码或优惠券！');
                return;
            }
        }

        data['cardType'] = 0;
        if ($('#thirdParty')[0].checked) {
            data['cardType'] = 1;
        }
        if ($('#linkPoint')[0].checked) {
            data['cardType']  = 2;
        }
        if ($('#allowTransfer')[0].checked) {
            data['allowTransfer']  = 1;
        } else {
            data['allowTransfer']  = 0;
        }
        delete data['linkMallGoods'];
		$.ajax( {    
            url:'/card/get_update', 
            data:data,
            type:'post',
            cache:false,
            dataType:'json',
            success:function(d) {
                common.unloading();
                if(d.errorCode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/card/lists';
                        }
                    });
                }else if(d.errorCode == 3){
                    common.alert('总数量不能小于已发放的数量',function(d){
                        // if(d==1){
                        //     location.href='/card/lists';
                        // }
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

}
$(function(){
	cardEdit.init();
});