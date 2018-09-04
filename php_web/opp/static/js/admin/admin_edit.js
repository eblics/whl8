var admin_edit = {
	init:function(){
		var _this = this;
		var uname = $('#userName').val();
		if(uname.length > 2){
			$('#userName').css("background-color","#D9D9D9").attr("readonly","readonly");
			$('#pass').parent().hide();
		};
		_this.validate();
	},
	validate:function(){
        var _this=this;
        $("#sub").off().on("click",function(){
            if(beforeSubmitAct()){
                _this.submit();
            }
        });
    },
    submit:function() {
    	var userName = $('#userName').val();
    	var phoneNum = $('#phoneNum').val();
    	var role = $('input:radio[name=levelType]:checked').val();
        var adminId = $('#admin_id').val();
        if (adminId) {
            var params = {
                role: role,
                mobile: phoneNum,
                admin_id: adminId
            };
            common.loading();
            hls.api.Admin.update(params, function(resp) {
                common.unloading();
                common.alert('修改成功！', function() {
                    location.assign('/admin');
                });
            }, function(err) {
                common.unloading();
                common.alert(err + '！');
            });
        } else {
            var params = {
                username: userName,
                role: role,
                mobile: phoneNum
            };
            common.loading();
            hls.api.Admin.add(params, function(resp) {
                common.unloading();
                common.alert('添加成功！', function() {
                    location.assign('/admin');
                });
            }, function(err) {
                common.unloading();
                common.alert(err + '！');
            });
        }
    }
};
$(function(){
	admin_edit.init();
});