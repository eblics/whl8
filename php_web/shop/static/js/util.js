/**
 * 红码商户微信端通用js
 * 
 * @author shizq
 */
(function(window) {

	var size = 70;
	
	// 声明命名空间
	var hls = window.hls || {};
	
	hls.util = {};
	
	hls.util.Dialog = {};
	
	function showMessage(content, callback) {
		layer.open({
		    // title: '消息',
		    content: content,
		    btn: ['确定'],
		    yes: function(handle) {
		    	layer.close(handle);
		    	if (typeof callback == 'function') {
		    		callback.call(window);
		    	}
		    },
		    style: 'width:' + size +'%;'
		});
	}
	hls.util.Dialog.showMessage = showMessage;

	function showConfirm(content, callback) {
		layer.open({
		    title: '提示',
		    content: content,
		    btn: ['确定', '取消'],
		    yes: function(handle) {
		    	layer.close(handle);
		    	if (typeof callback == 'function') {
		    		callback.call(window);
		    	}
		    },
		    no: function(handle) {
		    	layer.close(handle);
		    },
		    style: 'width:' + size +'%;'
		});
	}
	hls.util.Dialog.showConfirm = showConfirm;
	
	function showErrorMessage(content, callback) {
		layer.open({
		    // title: '错误',
		    content: content,
		    btn: ['确定'],
		    yes: function(handle) {
		    	layer.close(handle);
		    	if (typeof callback == 'function') {
		    		callback.call(window);
		    	}
		    },
		    style: 'width:' + size +'%;'
		});
	}
	hls.util.Dialog.showErrorMessage = showErrorMessage;
	
	function showLoading() {
		layer.open({
		    type: 2
		});
	}
	hls.util.Dialog.showLoading = showLoading;
	
	function closeLoading() {
		layer.closeAll();
	}
	hls.util.Dialog.closeLoading = closeLoading;

	hls.util.StringUtil = {

		"isEmpty": function(text) {
			return typeof(text) === "undefined" || text === null || text === "";
		},

		"isMobile": function(mobile) {
			if (/^17[07]{1}\d{8}$/.test(mobile)) {
				return true;
			}
			$mobileRule = /^1[34568]{1}\d{9}$/;
			return $mobileRule.test(mobile);
		}
	};

	hls.util.DateTimeUtil = {
		
		formatDateTime: function(time) {
			var datetime = new Date();  
			datetime.setTime(time);  
			var year = datetime.getFullYear();  
			var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;  
			var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();  
			var hour = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();  
			var minute = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();  
			var second = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();  
			return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;  
		}
	};
	
	window.hls = hls;
})(window);

(function(window) {
	
	var hls = window.hls || {};
	
	hls.enum = {};
	
	hls.enum.BooleanEnum = {
		"Yes": 1,
		"No": 0
	};
	
	hls.enum.ScanResEnum = {
		"Normal": 0,
		"Transfer": 1
	};
	
	hls.enum.ScanStatus = {
		"Scaned": 1,
		"NotScaned": 0
	};
	
	hls.enum.ConfirmStatus = {
		"Confirmed": 1,
		"NotConfirmed": 0
	};

	hls.enum.Role = {
		"Waiter": 1,
		"Salesman": 2
	};
	
	window.hls = hls;
})(window);

$(function(){
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        }
    });
});