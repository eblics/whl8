// 转移记录列表界面
// Author shizq

var page        = 0;
var hasNextPage = true;
var pageSize    = 15;
var isLoading   = false;

var currentPage = {

	init: function() {
		this.bindEvent();
	},

	bindEvent: function() {
		// 当用户滚动页面到底部，加载下一页数据
		$('.hls-page').scroll(function() {
			if ($('#scroll_container').height() - $('.hls-page').scrollTop() <= window.screen.availHeight) {
				if (isLoading) {
					return;
				}
				isLoading = true;
				currentPage.loadList();
			}
		});

		this.loadList();
	},

	loadList: function() {
		var params = {
			"page": page,
			"page_size": pageSize
		};
		if (! hasNextPage) {
			isLoading = false;
			return;
		}
		var loading = setTimeout(function() {
			hls.util.Dialog.showLoading();
		}, 500);
		$.get('/user/fetch_red_packet_logs/' + $('#mch_id').val(), params, function(resp) {
			hls.util.Dialog.closeLoading();
			clearTimeout(loading);
			isLoading = false;
			if (resp.data.length !== pageSize) {
				hasNextPage = false;
			} else {
				page++;
			}
			var content='';
			resp.data.forEach(function(item) {
				wxStatus = '';
				wxErrcode='';
				if (item.wx_status == '3' || item.wx_status == '0') {
					wxStatus = '(处理中)';
				} else if (item.wx_status == '2') {
					wxStatus = '(失败)';
					wxErrcode = '<em class="errcode" data="'+item.wx_errcode+'">?</em>';
				}
				content +=  '<li>';
				content +=		'<p>';
				content +=       	'<span style="width:70%">' + item.action + wxStatus + wxErrcode +'</span>';
				if (item.amount < 0) {
					content +=   	'<span style="width:30%">' + item.amount + '</span>';
					content += 	'</p>';
				} else {
					content +=   	'<span style="width:30%" class="text-red">+' + item.amount + '</span>';
					content += 	'</p>';
				}
				content +=   	'<small>' + item.createTime + '</small>';
				if (item.action == '系统自动提现发放' || item.action == '提现') {
					if (item.wx_status == '1') {
						content +=   '<em>提醒：请在公众号内领取或留意微信系统消息</em>';
					} else if (item.wx_status == '2') {
						if (item.wx_errcode == 'NO_AUTH') {
							content +=   '<em class="text-red">您的微信账号活跃度过低，提现失败，已退回账户余额</em>';
						} else {
							content +=   '<em class="text-red">提现失败，已退回账户余额</em>';
						}
					}
				}
				content += '</li>';
			});
			$('#red_packet_list').append(content);
			$('#red_packet_list .errcode').on('click',function() {
				var tip = $(this).attr('data')
				$(this).removeClass('errcode').addClass('errcode_done');
				currentPage.showErrTip(tip);
			});
			if (resp.data.length === 0) {
				if ($('#red_packet_list li').length != 0) return;
				content = '<li class="item-empty text-center">你还没有红包记录。</li>';
				$('#red_packet_list').append(content);
			}
			common.copyright();
		}).fail(currentPage.netError);
	},

	netError: function(err) {
		clearTimeout(loading);
		hls.util.Dialog.closeLoading();
		hls.util.Dialog.showErrorMessage('无法连接服务器');
	},
	showErrTip:function(tip){
		if (tip == 'NO_AUTH')
			common.alert(0, '<div style="padding:0 10px;text-align:left;">提取失败！微信公司对活跃度过低的微信进行了红包拦截。<BR>以下方法能提高微信活跃度<BR>1.保持每天登陆，经常与好友聊天互动<BR>2.绑定实名认证的银行卡<BR>正常使用一段时间后，微信公司就会帮您提升活跃度。</div>', 1);
		if (tip == 'SENDNUM_LIMIT')
			common.alert(0, '提取失败！<br/><br/>您今日领取红包个数达到上限，明天再试吧。', 1);
		if (tip == 'FREQ_LIMIT')
			common.alert(0, '提取失败！<br/><br/>提现过于频繁，请稍后再试吧。', 1);
		if (tip == 'NOTENOUGH')
			common.alert(0, '提取失败！<br/><br/>红包发放助手今天忘带钱包了，明天再试吧。', 1);
		if (tip == 'MONEY_LIMIT')
			common.alert(0, '提取失败！<br/><br/>红包金额不在微信限制范围内，暂时不支持该金额提现。', 1);
		if (tip == 'PARAM_ERROR')
			common.alert(0, '提取失败！<br/><br/>服务器不给力，明天再试吧。', 1);
		if (tip == null || tip == "null") {
			common.alert(0, '提取失败！<br/><br/>您的微信账号活跃度过低<br />提现失败，已退回账户余额。', 1);
		}
	}

};

$(function() {
	currentPage.init();
});