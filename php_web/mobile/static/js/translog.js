// 转移记录列表界面
// Author shizq

var page        = 0;
var hasNextPage = true;
var pageSize    = 15;
var isLoading   = false;
var queryType   = 'all'; // 查询条件

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

		// 绑定菜单点击事件
		$('header nav a').on('touchend', function() {
			if (isLoading) {
				return;
			}
			isLoading = true;
			$('header nav a').removeClass('selected');
			$(this).addClass('selected');
			queryType = $(this).prop('id');

			// 初始化页码为0
			page = 0;
			hasNextPage = true;
			// 清空列表
			$('#trans_list').empty();
			currentPage.loadList();
		});
	},

	loadList: function() {
		var params = {
			"type": queryType,
			"page": page,
			"page_size": pageSize
		};
		if (!hasNextPage) {
			isLoading = false;
			return;
		}
		var loading = setTimeout(function() {
			hls.util.Dialog.showLoading();
		}, 500);
		$.get('/transfer/fetch_logs/', params, function(resp) {
			hls.util.Dialog.closeLoading();
			clearTimeout(loading);
			isLoading = false;
			if (resp.data.length !== pageSize) {
				hasNextPage = false;
			} else {
				page++;
			}
			var content;
			resp.data.forEach(function(item) {
				content =  '<li><p>';
				content +=       '<span>' + item.nickName + '</span>';
				content +=       '<span class="card-type-title">' + item.type + item.title + '</span>';
				if (item.type == '发送' || item.type == '兑换') {
					content +=   '<span>- ' + item.num + '张</span></p>';
				} else if (item.type == '接收' || item.type == '中得') {
					content +=   '<span class="text-red">+ ' + item.num + '张</span></p>';
				} else {
					content +=   '<span>' + item.num + '张</span></p>';
				}
				content +=   '<small>' + hls.util.DateTimeUtil.formatDateTime(parseInt(item.transferTime) * 1000) + '</small>';
				content += '</li>';
				$('#trans_list').append(content);
			});
			if (resp.data.length === 0) {
				if ($('#trans_list li').length != 0) return;
				content = '<li class="item-empty text-center">没有数据</li>';
				$('#trans_list').append(content);
			}
			common.copyright();
		}).fail(currentPage.netError);
	},

	netError: function(err) {
		clearTimeout(loading);
		hls.util.Dialog.closeLoading();
		hls.util.Dialog.showErrorMessage('无法连接服务器');
	}

};

$(function() {
	currentPage.init();
});