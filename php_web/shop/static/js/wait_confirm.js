/**
 * 等待用户确认兑换界面
 * 
 * @author shizq
 */
$(function() {
	
	var handle;
	var times = 3000;
	var role;
	
	function init() {
		role = $('#role').val().trim();
		$('body').addClass('hls-back-gray');
		if (! handle) {
			handle = setInterval(checkIsConfirmed, times);
		}
		
		bindEvent();
	}
	
	function bindEvent() {
		$('#finish_back_btn').on('touchend', function() {
			if ($(this).hasClass('disable')) {
				return;
			}
			if (role == hls.enum.Role.Waiter) {
				location.replace('/account/waiter?mch_id=' + $('#mch_id').val().trim());
			} else if (role == hls.enum.Role.Salesman) {
				location.replace('/account/salesman?mch_id=' + $('#mch_id').val().trim());
			}
		});
	}
	
	/**
	 * 检测用户是否确认了转移
	 */
	function checkIsConfirmed() {
		hls.api.Transfer.check_if_confirmed(function(resp) {
			if (resp.confirmed == hls.enum.ConfirmStatus.Confirmed) {
				clearInterval(handle);
				$('header img').attr('src', '/static/images/2.png');
				$('header p').addClass('green');
				$('.tip span').text('操作已完成，请点击下方按钮返回！');
				$('#finish_back_btn').removeClass('disable');
			}
		}, function(errmsg) {
			clearInterval(handle);
		});
	}
	
	init();
});