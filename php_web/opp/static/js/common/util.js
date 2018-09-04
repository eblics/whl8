/**
 * 欢乐扫商户微信端通用js
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
		    content: content + '！',
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
		    title: '错误',
		    content: content + '！',
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
	
	window.hls = hls;
})(window);

/**
 * 定义枚举
 * 
 */
(function(window) {
	
	var hls = window.hls || {};
	
	hls.enum = {};
	
	hls.enum.BooleanEnum = {
		"Yes": 1,
		"No": 0
	};
	
	hls.enum.AdminStatusEnum = {
		"Disable": 0,
		"Enable": 1,
		"Locked": 2,
		"Del": 3
	}
	
	window.hls = hls;
})(window);

/**
 * 定公共对象
 * 
 */
(function(window) {
	
	var hls = window.hls || {};
	
	hls.common = {};
	
	var url = "/static/datatables/js/dataTables.language.js";
	hls.common.dataTable = {
		"info":       true, "paging":     true,
		"stateSave":  true, "searching":  true,
		"ordering":   true,"language":   {"url": url},
        "order":      [[0, 'desc']],

        initComplete: function() {
			common.autoHeight();
		},

        drawCallback: function() {
			common.autoHeight();
        }
	}
	
	window.hls = hls;
})(window);