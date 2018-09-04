var url = '/hls_tools/api/lecode.toggle';
var wxSdkReady = true;

function init() {
	// wx.config(config);
	wx.ready(function() {
		$('#open_scan').addClass('ready');
	});
	wx.error(function(err) {
		wxSdkReady = false;
		layer.closeAll();
	});
}

function analyse(url, lecode, type) {
    if(lecode.indexOf('/c/')!=-1){
        var strArr=lecode.split('/c/');
        lecode=strArr[1];
    }
	$.get(url, {code: lecode, way: type}, function(result) {
		if (result.errcode == 0) {
			hls.util.Dialog.showMessage('企业名称：' + result.content.mch_name + 
				'<br />明码：' + result.content.pub_code + '<br />批次：' + 
				result.content.batch_no);
		} else {
			hls.util.Dialog.showErrorMessage(result.message);
		}
	}, 'json').error(function(err) {
		hls.util.Dialog.showErrorMessage('无法连接服务器。');
	});
}

function openScan() {
	if (! wxSdkReady) {
		hls.util.Dialog.showErrorMessage('微信组件未正确加载。');
		return;
	}
	wx.scanQRCode({
	    needResult: 1, 
	    scanType: ["qrCode", "barCode"], 
	    success: function(res) {
		    var lecode = res.resultStr;
		    $('#lecode_input').val(lecode);
		}
	});
}

$('#open_scan').click(function() {
	console.log('tapped');
	if ($('#open_scan').hasClass('ready')) {
		console.log('invoke openScan');
		openScan();
	}
});
$('#outter2inner').click(function() {
	var lecode = $('#lecode_input').val();
	if (! lecode) {
		hls.util.Dialog.showErrorMessage('请输入乐码。');
		return;
	}
	analyse(url, lecode, 1);
});
$('#inner2outter').click(function() {
	var lecode = $('#lecode_input').val();
	if (! lecode) {
		hls.util.Dialog.showErrorMessage('请输入乐码。');
		return;
	}
	analyse(url, lecode, 2);
});

init();