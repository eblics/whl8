var groupJoin={
	init:function(){
        if(top.location.hash!=''){
            top.common.refreshTitle('口令加群');
        }else{
            common.refreshTitle('口令加群');
        }
		this.event();
	},
	event:function(){
		this.pwd();
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
    btnNext:function(){
        var _this=this;
		$('#btnNext').on('tap',function(){
            if($(this).hasClass('weui_btn_disabled')) return;
            var groupPassword=$.trim($('#groupPassword').val());
            if(groupPassword==''){
                common.alert('信息填写不完整');
                return;
            }
            var data={'password':groupPassword};
            _this.postData(data);
        });
	},
    postData:function(data){
        common.loading();
        $.ajax({
            url: '/group/pwd_valid',
            type: "POST",
            dataType: 'json',
            data: data,
            success: function(d) {
                if(d.errcode!=0){
                    common.alert(d.errmsg);
                }else{
                    location.href='/group/chat/'+d.data;
                }
                common.unloading();
            },
            error: function(d) {
                common.unloading();
                common.alert('请求失败，请重试');
            }
        });
    }
    
};
$(function(){
	groupJoin.init();
});
