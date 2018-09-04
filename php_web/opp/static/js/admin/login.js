/**
 * 
 * 登录界面js
 * 
 * @author shizq
 * 
 */
var login = {

	init: function() {
        var account = localStorage.getItem('account');
        $("#account").val(account);
		this.bindEvent();
	},

	bindEvent: function() {
		var self = this;
		$("#ishover").click(function() {
            if (beforeSubmitAct()) {
                self.submit();
            }
        });
        $('#verify_img').click(function() {
            $(this).prop('src', '/login/verify_img?t=' + new Date().getTime());
        });
        $('#verifica').blur(function() {
            $('#coderrmsg').hide();
            $('#coderrmsg').html();
        });
        $("#account, #password").keydown(function(event) {
            if (event.keyCode == 13) {
                $("#ishover").trigger('click');
            }
        });
	},

	/**
	 * 刷新验证码
	 * 
	 */
	reload_verify_img: function() {
		var self = this;
		common.loading();
        hls.api.Admin.reload_verify_img(function(resp) {
        	common.unloading();
        	$('.validpic').children('img').remove();
            $('.validpic').append($(resp));
            $('#Imageid').click(function() {
	            self.reload_verify_img();
	        });
        }, function(err) {
        	common.unloading();
        	common.alert(err + '！');
        });
	},

	submit: function() {
        var params = {
        	"account": $("#account").val(),
        	"password": $("#password").val(),
        	"verify_code": $("#verifica").val()
        };
		common.loading();
		hls.api.Admin.login(params, function(resp) {
            if ($('#ison')[0].checked) {
                localStorage.setItem('account', params.account);
            } else {
                localStorage.setItem('account', '');
            }
            location.assign('/merchant');
		}, function(err) {
			common.unloading();
			common.alert(err + '。');
		});
	}
};
$(function() {
    login.init();
});