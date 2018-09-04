var groupPwd={
	init:function(){
        if(top.location.hash!=''){
            top.common.refreshTitle('修改口令');
        }else{
            common.refreshTitle('创建口令');
        }
		this.event();
	},
	event:function(){
		this.pwd();
        this.over();
        this.btnNext();
        common.noOverScroll('body');
	},
    pwd:function(){
        var _this=this;
        _this.pwdOk=false;
		$('#groupPassword').on('input',function(){
            var curVal=$(this).val();
            if($.trim(curVal)==''){
                $(this).val('');
                _this.pwdOk=false;
            }else{
                _this.pwdOk=true;
            }
            _this.check();
        }).on('blur',function(){
            var curVal=$(this).val();
            var defaultVal='';
            if($.trim(curVal)==''){
                $(this).val(defaultVal);
                _this.pwdOk=false;
            }else{
                _this.pwdOk=true;
            }
            _this.check();
        });
	},
    check:function(){
        var _this=this;
        if(_this.pwdOk){
            $('#btnNext').removeClass('weui_btn_disabled');
            return;
        }
        if(!$('#btnNext').hasClass('weui_btn_disabled')){
            $('#btnNext').addClass('weui_btn_disabled');
        }
    },
    over:function(){
        var _this=this;
        $('.group_pwd .over').on('tap',function(){
            var id=$.trim($('#groupId').val());
            var data={'password':'','id':id};
            _this.postData(data);
        });
    },
    btnNext:function(){
        var _this=this;
		$('#btnNext').on('tap',function(){
            if($(this).hasClass('weui_btn_disabled')) return;
            var groupPassword=$.trim($('#groupPassword').val());
            var id=$.trim($('#groupId').val());
            if(groupPassword=='' || id==''){
                common.alert('信息填写不完整');
                return;
            }
            var data={'password':groupPassword,'id':id};
            _this.postData(data);
        });
	},
    postData:function(data){
        common.loading();
        $.ajax({
            url: '/group/save_pwd',
            type: "POST",
            dataType: 'json',
            data: data,
            success: function(d) {
                common.unloading();
                if(d.errcode!=0){
                    common.alert('保存失败，请换个口令试试');
                }else{
                    var id=$.trim($('#groupId').val());
                    if(top.location.hash!=''){
                        window.history.back(-1);
                    }else{
                        location.href='/group/add_group_ok/'+id;
                    }
                    
                }
            },
            error: function(d) {
                common.unloading();
                common.alert('请求失败，请重试');
            }
        });
    }
    
};
$(function(){
	groupPwd.init();
});
