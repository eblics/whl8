/**
 * 应用详细信息界面
 *
 * @author shizq
 */
var pageAppDesc = {

	init: function() {
		this.bindEvent();
	},

	bindEvent: function() {
		var self = this;
		$('#recommend li img, #recommend li .btn').click(function() {
			var id = $(this).prop('id');
			// 跳转到应用详细页面
			location.href = '/app/desc/' + id;
		});

		$('#apply_btn').click(function() {
			self.addNewApp($(this).attr('data-id'));
		});

		$('#re_apply_btn').click(function() {
			self.applyAppInst($(this).attr('data-id'));
		});

		$('#view_btn').click(function() {
			location.href = '/myapp';
		});

		$('#pay_layer_close').click(function() {
			$('.pay-layer').addClass('hls-hidden');
		});
	},

	// -----------------------------------------------------------------
	// 保存应用实例信息的修改
	checkOrderStatus: function() {
		$.get('/app/orderquery/' + order_id, {}, function(resp) {
			if (! resp.errcode) {
				$('.pay-layer').addClass('hls-hidden');
				clearInterval(looper);
				common.alert('支付成功！', function() {
					location.reload();
				});
			} else if (resp.errcode = 10006) {
				// 没有支付
			} else {
				clearInterval(looper);
				common.alert(resp.errmsg + '！');
			}

		}, 'json').error(function() {
			clearInterval(looper);
			common.alert('无法连接服务器！');
		});
	},


	// -----------------------------------------------------------------
	// 添加新的应用
	addNewApp: function(appId) {
		var self = this;
		var params = {
			"app_id": appId
		};
		$.get('/app/apply', params, function(resp) {
			if (! resp.errcode) {
 				common.alert('添加成功！', function() {
 					location.reload();
 				});
			} else if (resp.errcode == 10009) {
				// 生成支付订单，跳转到支付界面
				self.generateOrder(appId);
			} else {
				common.alert(resp.errmsg + '！');
			}

		}, 'json').error(function(err) {
			common.alert('无法连接服务器！');
		});
	},

	// -----------------------------------------------------------------
	// 应用已经添加过的APP
	applyAppInst: function(appId) {
		common.confirm('确定安装该应用吗？', function(confirm) {
			if (confirm) {
				$.post('/app/re_apply', {"app_id": appId}, function(resp) {
					if (! resp.errcode) {
						common.alert('操作成功！', function() {
							location.reload();
						});
					} else {
						common.alert(resp.errmsg);
					}
				}, 'json').error(function(err) {
					common.alert('无法连接服务器！');
				});
			}
		});
	},

	// -----------------------------------------------------------------
	// 生成购买订单
	generateOrder: function(appId) {
		common.loading();
		var self = this;
		$.post('/app/create_order', {"app_id": appId}, function(resp) {
			if (! resp.errcode) {
				$.get('/app/get_pay_order/' + resp.data.order_id, {}, function(_resp) {
					if (! resp.errcode) {
						common.unloading();
						$('#qrcode_container').empty();
						$('#qrcode_container').qrcode({text: _resp.data.qrcode});
						order_id = resp.data.order_id;
						looper = setInterval(function() {
							self.checkOrderStatus();
						}, times);
						$('.pay-layer').removeClass('hls-hidden');
					} else {
						common.unloading();
						common.alert(resp.errmsg + '！');
					}
				}, 'json').error(function() {
					common.unloading();
					common.alert('无法连接服务器！');
				});
			} else {
				common.unloading();
				common.alert(resp.errmsg + '！');
			}
		}, 'json').error(function(err) {
			common.unloading();
			common.alert('无法连接服务器！');
		})
	}

};

$(function() {
	window.times = 5000,
	window.looper = null,
	window.order_id;
	pageAppDesc.init();
});