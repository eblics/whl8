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
	hls.api.Admin = {};

	/**
	 * 用户登录API
	 * 
	 * @param  params {
	 *     account: 用户名
	 *     password: 密码
	 *     verify_code: 验证码
	 * }
	 */
	hls.api.Admin.login = function(params, successCallback, faildCallback) {
		$.post("/api/login", params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 重新加载验证码图片
	 * 
	 */
	hls.api.Admin.reload_verify_img = function(successCallback, faildCallback) {
		$.get('/api/login/create_verify_img', {}, function(resp) {
            if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
        }).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 添加账户
	 * 
	 * @param params {
	 *     username: 用户名
	 *     mobile: 手机号
	 *     role: 角色
	 * }
	 */
	hls.api.Admin.add = function(params, successCallback, faildCallback) {
		$.post("/api/admin/add_admin", params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 修改账户信息
	 * 
	 * @param params {
	 *     realname: 姓名
	 *     mobile: 手机号
	 *     mail: 邮箱地址
	 * }
	 */
	hls.api.Admin.update_profile = function(params, successCallback, faildCallback) {
		$.post("/api/admin/update_profile", params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};
	/**
	 * 删除帐户
	 * 
	 * @param params {
	 *     admin_id：帐户id
	 * }
	 */
	hls.api.Admin.del_admin = function(admin_id, successCallback, faildCallback){
		$.post("/api/admin/del_admin",{id:admin_id},function(res){
			if (! res.errcode) {
				successCallback.call(window, res.data);
			} else {
				faildCallback.call(window, res.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};
	
	/**
	 * 修改其他账户的信息
	 * 
	 * @param params {
	 *     mobile: 手机号
	 *     role: 角色
	 * }
	 */
	hls.api.Admin.update = function(params, successCallback, faildCallback) {
		$.post("/api/admin/update", params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};
	
	/**
	 * 修改密码
	 * 
	 * @param old_pass 原密码
	 * @param new_pass 新密码
	 */
	hls.api.Admin.passwd = function(old_pass, new_pass, successCallback, faildCallback) {
		var params = {
			"old_pass": old_pass,
			"new_pass": new_pass
		};
		$.post('/api/admin/passwd', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 冻结管理员账户
	 * 
	 * @param admin_id 管理员ID
	 */
	hls.api.Admin.freeze = function(admin_id, successCallback, faildCallback) {
		var params = {
			"admin_id": admin_id
		};
		$.post('/api/admin/freeze_admin', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 启用管理员账户
	 * 
	 * @param admin_id 管理员ID
	 */
	hls.api.Admin.active = function(admin_id, successCallback, faildCallback) {
		var params = {
			"admin_id": admin_id
		};
		$.post('/api/admin/active_admin', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 重置管理员账户密码
	 * 
	 * @param admin_id 管理员ID
	 */
	hls.api.Admin.reset_passwd = function(admin_id, successCallback, faildCallback) {
		var params = {
			"admin_id": admin_id
		};
		$.post('/api/admin/reset_passwd', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 生成运营端登录企业后台的验证时间
	 * 
	 */
	hls.api.Admin.generate_token = function(mchId, successCallback, faildCallback) {
		$.post('/api/admin/generate_token', {"mch_id": mchId}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	
	// ============================= 企业管理API ============================
	hls.api.Merchant = {};
	
	/**
	 * 预审核企业
	 * 
	 * @param mch_id 企业ID
	 */
	hls.api.Merchant.review = function(mch_id, successCallback, faildCallback) {
		var params = {
			"mch_id": mch_id,
			"preview": hls.enum.BooleanEnum.Yes
		};
		$.post('/api/merchant/review', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};
	
	/**
	 * 冻结企业
	 * 
	 * @param mch_id 企业ID
	 */
	hls.api.Merchant.freeze = function(mch_id, successCallback, faildCallback) {
		var params = {
			"mch_id": mch_id
		};
		$.post('/api/merchant/freeze', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 激活企业账户
	 * 
	 * @param mch_id 企业ID
	 */
	hls.api.Merchant.active = function(mch_id, successCallback, faildCallback) {
		var params = {
			"mch_id": mch_id
		};
		$.post('/api/merchant/active', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	/**
	 * 密码初始化
	 * 
	 * @param  mch_id 企业ID
	 */
	hls.api.Merchant.passwd = function(mch_id, successCallback, faildCallback) {
		var params = {
			"mch_id": mch_id
		};
		$.post('/api/merchant/init_passwd', params, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function() {
			faildCallback.call(window, SERVER_ERROR);
		});
	};

	window.hls = hls;
	
})(window);