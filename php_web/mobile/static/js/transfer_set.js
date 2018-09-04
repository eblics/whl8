/**
 * 被扫描者设置要兑换的数量
 * 
 * @author shizq
 */
$(function() {
	
	function init() {
		$('body').addClass('hls-back-gray');
		bindEvent();
	}
	
	function bindEvent() {
		$('#btn_transfer_confirm').click(function() {
			transferConfirm();
		});
	}
	
	/**
	 * 确认兑换操作
	 */
	function transferConfirm() {
		var num = $('#trans_num').val();
		if (! /^[0-9]+$/.test(num)) {
			hls.util.Dialog.showErrorMessage('只能转移整数数量');
			return;
		}
		hls.util.Dialog.showLoading();
		hls.api.Transfer.confirm(num, function(resp) {
			hls.util.Dialog.closeLoading();
			hls.util.Dialog.showMessage('操作成功', function() {
				if (localStorage.getItem('last_url')) {
					location.href = localStorage.getItem('last_url');
				} else {
					history.back(-9);
				}
			});
		}, function(errmsg) {
			hls.util.Dialog.closeLoading();
			hls.util.Dialog.showErrorMessage(errmsg);
		});
	}
	
	init();
});