var passwd = {

	init: function() {
		this.bindEvent();
	},

	bindEvent: function() {
		var self = this;
		$("#sub").click(function() {
            if (beforeSubmitAct()) {
                self.passwd();
            }
        });
	},

	passwd: function() {
		var oldPass = $.trim($('#old_pass').val());
		var newPass = $.trim($('#new_pass').val());
		var newPass2 = $.trim($('#new_pass2').val());
		if (newPass !== newPass2) {
			common.alert("两次密码不一致！");
			return;
		}
		common.loading();
		hls.api.Admin.passwd(oldPass, newPass, function(resp) {
			common.unloading();
			common.alert('修改成功，请重新登录！', function() {
				location.assign('/login');
			});
		}, function(err) {
			common.unloading();
			common.alert(err + '！');
		});
	}
};
passwd.init();
