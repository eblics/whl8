var editGroup = {
	init:function(){
		var _this=this;
        common.uploadInit('imgUrl','/card/upload');
        _this.change();
	},
    change:function(){
        var _this = this;
        $('#priority_0,#priority_1,#priority_2').change(function(){
            $(this).attr("checked","checked");
            $('#priority_0,#priority_1,#priority_2').not($(this)).removeAttr('checked');
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
		$('form input,form textarea').each(function(e){
            var name = $(this).attr('name');
            var val = $(this).val();
            if($.trim(name) != ''){
                data[name] = val;
                $('#priority_0,#priority_1,#priority_2').each(function(){
                    if($(this).attr('checked') == 'checked'){
                        data.priority = $(this).val();
                    }
                });

            }
        });
        data['hasGroupBonus'] = $('input[name=hasGroupBonus]').prop('checked') ? 1 : 0;
        data['bonusType'] = $('select[name=bonusType]').val();
        $.post('/card/save_cgroup',{data:data},function(d){
            if(d.errcode == 0){
                common.alert('保存成功',function(d){
                    if(d==1){
                        location.href='/card/lists';
                    }
                });
            }
            if(d.errcode == 1){
                common.alert(d.errmsg);
            }
        },'json');
	}
}
$(function(){
	editGroup.init();
});