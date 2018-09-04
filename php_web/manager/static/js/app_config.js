(function() {

	var hls = window.hls || {};

    // -------------------------------------------------------------
    // 加载app配置
    hls.loadAppConfig = function(successCallback, failCallback) {
        $.get('/myapp/show/' + top.globals.inst_id, {}, function(resp) {
        	if (! resp.errcode) {
        		successCallback.call(window, resp);
        	} else {
        		failCallback.call(window, resp);
        	}
        }).fail(function(err) {
        	common.alert('无法连接服务器！');
        });
    };

    // -------------------------------------------------------------
    // 保存app配置
    hls.saveAppConfig = function(params, successCallback, failCallback) {
        $.post('/myapp/save/' + top.globals.inst_id, {config: JSON.stringify(params)}, function(resp) {
        	if (! resp.errcode) {
        		successCallback.call(window, resp);
        	} else {
        		failCallback.call(window, resp);
        	}
        }).fail(function(err) {
        	common.alert('无法连接服务器！');
        });
    };

    window.hls = hls;

})();
    