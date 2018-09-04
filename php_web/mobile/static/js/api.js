/**
 * shop api
 * 
 * @author shizq
 */
(function(window) {

	var SERVER_ERROR = '无法连接服务器';
	
	// 声明命名空间
	var hls = window.hls || {};
	
	hls.api = {};
	
	// ============================= 账户模块API ============================
	hls.api.Account = {};
	
	/**
	 * 业务员更新身份信息
	 * 
	 * @param object params {
	 * 		realname: 真实姓名
	 * 		mobile: 手机号
	 * 		id_card_no: 身份证号
	 * }
	 */
	function update(params, successCallback, faildCallback) {
		$.post('/account/update', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Account.update = update;
	
	
	// ============================= 扫描乐码API ============================
	hls.api.Scan = {};
	
	/**
	 * 扫码接口，当用户打开兑换或扫一扫之后扫描的结果会通过此接口分析
	 * 
	 * @param params {
	 * 		role: 角色类型
	 * 		action: 兑换或扫一扫
	 * 		mch_id: 企业id
	 * }
	 * @param lecode 扫码结果
	 */
	function analyse(params, lecode, successCallback, faildCallback) {
		$.post('/scan/analyse/' + lecode, params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Scan.analyse = analyse;
	
	
	// ============================= Transfer API ============================
	hls.api.Transfer = {};
	
	function confirm(num, successCallback, faildCallback) {
		$.post('/transfer/confirm', {"num": num}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Transfer.confirm = confirm;
	
	/**
	 * 检测二维码是否被扫描
	 */
	function check_if_scaned(successCallback, faildCallback) {
		$.get('/transfer/check_scan', {}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Transfer.check_if_scaned = check_if_scaned;
	
	/**
	 * 检测转移者是否确认了转移操作
	 */
	function check_if_confirmed(successCallback, faildCallback) {
		$.get('/transfer/check_confirm', {}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Transfer.check_if_confirmed = check_if_confirmed;
	
	
	// ============================= 核销乐券API ============================
	hls.api.Settle = {};
	
	/**
	 * 获取业务员所有的卡券
	 * 
	 */
	function cards(successCallback, faildCallback) {
		$.get('/settle/cards', {}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Settle.cards = cards;
	
	/**
	 * 获取业务员核销记录
	 * 
	 */
	function settle_notes(params, successCallback, faildCallback) {
		$.get('/settle/settle_notes', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	}
	hls.api.Settle.settle_notes = settle_notes;
	
})(window);