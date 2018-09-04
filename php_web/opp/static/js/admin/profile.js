/**
 * 个人信息js
 * 
 * @author shizq
 */
var profile = {

	init: function() {
		this.bindEvent();
	},

	bindEvent: function() {
		var self = this;
		$('#sub').click(function() {
	        if (beforeSubmitAct()) {
	        	self.update();
	        }
		});
	},

	update: function() {
		var params = {
			"realname": $.trim($('#realName').val()),
			"mobile": $.trim($('#phoneNum').val()),
			"mail": $.trim($('#mail').val())
		};
		common.loading();
		hls.api.Admin.update_profile(params, function(resp) {
			common.unloading();
			common.alert('修改成功！');
		}, function(err) {
			common.unloading();
			common.alert(err + '！');
		});
	}
};
$(function() {
	profile.init();
});