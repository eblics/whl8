/**
 * 转移二维码界面js逻辑
 * 
 * @author shizq
 */
$(function() {
	
	var times = 3000;
	var handle;
	var ticket;
	
	function init() {
		ticket = $('#transfer_ticket').val().trim();
		$('#hls-qr-container').qrcode({text: ticket});
		if (! handle) {
			handle = setInterval(checkIsScaned, times);
		}
	}
	
	function checkIsScaned() {
		hls.api.Transfer.check_if_scaned(function(resp) {
			if (resp.scaned == hls.enum.ScanStatus.Scaned) {
				clearInterval(handle);
				location.replace('/transfer/set');
			}
		}, function(errmsg) {
			clearInterval(handle);
			hls.util.Dialog.showErrorMessage(errmsg, function() {
				history.back();
			});
		});
	}
	
	init();
});