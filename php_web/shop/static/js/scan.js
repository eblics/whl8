/**
 * @author shizq
 */
$(function() {

	var jsonp_url = $('#hidden_jsonp_url').val();
	var lat = 0;
	var lng = 0;
	
	wx.config(config);
	
	function init() {
		wx.ready(ready);
		wx.error(function(err) {
			console.log(err);
			layer.closeAll();
		});
	}

	/**
	 * 微信sdk初始化完毕之后调用此函数
	 * 先获取用户经纬度，成功后开始扫描
	 * 
	 */
	function ready() {
		var located = localStorage.getItem('ls_position');
		var expireTime = localStorage.getItem('ls_expire_time');
		if (located && expireTime > new Date().getTime()) {
			var position = JSON.parse(located);
			lat = position.lat;
			lng = position.lng;
			scan();
		} else {
			wx.getLocation({
			    type: 'wgs84',
			    success: function (res) {
	        		// 成功获得坐标位置
			        lat = res.latitude;
			        lng = res.longitude;
			        var position = {lat: lat, lng: lng};
			        var expireTime = new Date().getTime() + 3600 * 1000 * 2; // 两小时
			        localStorage.setItem('ls_position', JSON.stringify(position));
			        localStorage.setItem('ls_expire_time', expireTime);
					scan();
			    }
			});
		}
		
		window.scan = scan;
	}
	
	/**
	 * 启动微信二维码扫码功能
	 */
	function scan() {
		$('.hls-page').hide();
		hls.util.Dialog.showLoading();
		wx.scanQRCode({
		    needResult: 1, 
		    scanType: ["qrCode", "barCode"], 
		    success: function(res) {
			    var ticket = res.resultStr;
			    $('title').text('处理中...');
			    if (ticket.indexOf('/C/') > 0) {
			    	clip = ticket.split('/C/');
			    	ticket = clip[clip.length - 1];
			    }
			    if (ticket.indexOf('/c/') > 0) {
			    	clip = ticket.split('/c/');
			    	ticket = clip[clip.length - 1];
			    }
			    analyse(ticket);
			}
		});
	}

	// analyse('10hoHsqaXX0');

	/**
	 * 调用扫码接口，对结果进行分析
	 */
	function analyse(lecode) {
		var params = {
			role: $('#hidden_role').val().trim(),
			action: $('#hidden_action').val().trim(),
			mch_id: $('#hidden_mch_id').val().trim()
		};
		
		$('title').text('正在分析乐码...');
		hls.api.Scan.analyse(params, lecode, function(resp) {
			scanResult(resp);
		}, function(errmsg) {
			$('title').text('扫描结果');
			hls.util.Dialog.closeLoading();
			$('#error_message').text(errmsg);
			$('#error_section').show();
		});
	}
	
	function scanResult(data) {
		console.log(data);
		if (data.type == hls.enum.ScanResEnum.Transfer) {
			// 等待用户确认兑换
			location.replace('/transfer/wait_confirm');
		} else {
			// 默认停留在当前页
			takeActivity();
		}
	}

	/**
	 * 用户扫码完成记录日志之后开始参与活动并判断是否中奖
	 */
	function takeActivity() {
		var netErrorCallback = function(err) {
			$('title').text('扫描结果');
			hls.util.Dialog.closeLoading();
			$('#error_message').text('无法连接服务器');
			$('#error_section').show();
		};
		var params = {
	        // code: lecode,
	        pos: {lat: lat, lng: lng},
	        // role: hls.enum.Role.Waiter,
	        // openid: openid,
	        // common_openid: common_openid
	    };
		$.get('/activity/match_activity', params, function(resp_) {
			if (resp_.errcode) {
				$('title').text('扫描结果');
				hls.util.Dialog.closeLoading();
				$('#error_message').text(resp_.errmsg);
				$('#error_section').show();
				return;
			}
			$.get('/activity/take_activity', params, function(resp) {
		    	if (typeof resp === 'string') {
                    alert(resp.match(/<div.+div>/m));
                    return;
                };
                $('title').text('扫描结果');
		    	hls.util.Dialog.closeLoading();
		    	if (resp.errcode) {
		    		hls.util.Dialog.closeLoading();
		    		$('#error_message').text(resp.errmsg);
					$('#error_section').show();
		    	} else {
		    		$('div.hls-page').show();
		    		var name;
		    		if (resp.datatype == 0) {
		    			name = resp.data.amount + '分';
		    			$('.hls-prize-detail .prize-name').text('红包');
		    			$('#hls_prize').text(name + '红包');
		    		} else if (resp.datatype == 3) {
		    			name = resp.data.amount + '个';
		    			$('.hls-prize-detail .prize-name').text('积分');
		    			$('#hls_prize').text(name + '积分');
		    		} else {
		    			name = resp.data.name
		    			$('.hls-prize-detail .prize-name').text('乐券');
		    			$('#hls_prize').text(name + '');
		    		}
					if (name.length > 8) {
						name = name.substring(0, 8) + '</ br>' + name.substring(8, name.length);
					}
					$('.hls-prize-detail .amount').html(name);
		    	}
			}).fail(netErrorCallback);
		}).fail(netErrorCallback);
	}
	
	init();
});