/**
 * 账户界面js逻辑
 * 
 * @author shizq
 */
$(function() {

	var role 		= $('#hidden_input_role').val();
	var page        = 0;
	var hasNextPage = true;
	var pageSize    = 15;
	var isLoading   = false;
	var queryType   = 'all'; // 查询条件

	var Page = {

		init: function() {
			this.bindEvent();
		},

		bindEvent: function() {
			var click = true, self = this;

			// 乐券转移点击
			$('.transfer-cards button').on('touchstart', function() {
				click = true;
			}).on('touchmove', function() {
				click = false;
			}).on('touchend', function() {
				if (! click) return;
				if ($(this).attr('data-id') === '0' || $(this).attr('data-id') === 0) {
					hls.util.Dialog.showMessage('没有乐券');
					return;
				}
				if ($(this).attr('data-num') === '0' || $(this).attr('data-num') === 0) {
					hls.util.Dialog.showMessage('没有乐券');
				} else {
					var cardTitle = $(this).parent().parent().find('.card-title').text(), 
						cardNum = $(this).attr('data-num'),
						cardOrParentId = $(this).attr('data-id');
					self.showSingleCardInfo(cardTitle, cardNum, cardOrParentId, '转移');
					$('#btnTransfer').on('touchend', function() {
						// 记录跳转前的页面地址，方便跳回
						localStorage.setItem('last_url', location.href);
						var href = '/transfer?role=' + role + '&obj_id=' + cardOrParentId + '&obj_type=2';
						location.href = href;
					});
				}
			});

			// 乐券兑换点击
			$('.single-cards button, .group-cards button').on('touchstart', function() {
				click = true;
			}).on('touchmove', function() {
				click = false;
			}).on('touchend', function() {
				if (! click) return;
				if ($(this).attr('data-id') === '0' || $(this).attr('data-id') === 0) {
					hls.util.Dialog.showMessage('没有乐券');
					return;
				}
				if ($(this).attr('data-num') === '0' || $(this).attr('data-num') === 0) {
					if ($(this).hasClass('card-group') && role == 0) {
						var cardTitle = $(this).parent().parent().find('.card-title').text(), 
							cardNum = $(this).attr('data-num'),
							cardOrParentId = $(this).attr('data-id');
						// 处理卡组兑换
						self.showGroupCardsInfo(cardTitle, cardNum, cardOrParentId, '兑换');
					} else {
						hls.util.Dialog.showMessage('没有乐券');
					}
				} else if (role === '0' || role === 0) {
					if ($(this).hasClass('empty-0')) {
						hls.util.Dialog.showMessage('该乐券不可转移');
						return;
					}
					var cardTitle = $(this).parent().parent().find('.card-title').text(), 
						cardNum = $(this).attr('data-num'),
						cardOrParentId = $(this).attr('data-id');
					if ($(this).hasClass('card-group')) {
						// 处理卡组兑换
						self.showGroupCardsInfo(cardTitle, cardNum, cardOrParentId, '兑换');
					} else {
						// 处理单卡兑换
						self.showSingleCardInfo(cardTitle, cardNum, cardOrParentId, '兑换');
					}
				} else {
					hls.util.Dialog.showMessage('服务员和业务员不支持此兑换');
				}
			});

			$('.single_cards').on('touchend', function() {
				location.href = '#single_cards';
			});
			$('.group_cards').on('touchend', function() {
				location.href = '#group_cards';
			});
			$('.transfer_cards').on('touchend', function() {
				location.href = '#transfer_cards';
			});
		},

		showSingleCardInfo: function(cardTitle, cardNum, cardOrParentId, action) {
			var self = this;
			var html = '';
			html += '<div class="layer">';
			html += '	<div class="layer-header">';
			html += '		<img src="/static/images/close.png?v=2.0">';
			html += '		<h2>' + cardTitle + '</h2>';
			html += '		<h5>可' + action + '总数：<span class="text-red"> ' + cardNum + ' 张</span></h5>';
			html += '		<p>(备注：单张可兑换 ' + cardTitle + ')</p>';
			html += '	</div>';
			html += '	<div class="layer-content">';
			html += '		<h5>' + cardTitle + '如下：</h5>';
			html += '		<div class="card-image">'; 
			html += '			<div class="dis-flex">';
			html += '				<div class="flex-item amount">1</div>';
			html += '				<div class="flex-item flex-grow-3"><span> x (' + cardTitle + ')</span><br />';
			html += '					<span>通用兑换券</span>';
			html += '				</div>';
			html += '			</div>';
			html += '			<p>此券现有 ' + cardNum + ' 张</p>';
			html += '		</div>';
			html += '	</div>';
			html += '	<button id="btnTransfer" data-id="' + cardOrParentId + '" class="hls-btn">' + action + '</button>';
			html += '</div>';
			layer.open({content: html});
			$('.layermchild').addClass('hls-layermchild')
			$('.layer-header img').on('touchend', function() {
				hls.util.Dialog.closeLoading();
			});
			$('#btnTransfer').on('touchend', function() {
				if (action == '转移') return;
				var yes = window.confirm('请确认' + action + '？');
				if (! yes) {
					return;
				}
				self.exchangeCard(cardOrParentId, 0);
			});
		},

		showGroupCardsInfo: function(cardTitle, cardNum, cardOrParentId, action) {
			var self = this;
			hls.util.Dialog.showLoading();
			$.get('/card/group_cards', {"group_id": cardOrParentId}, function(resp) {
				hls.util.Dialog.closeLoading();
				if (resp.errcode == 0) {
					var html = '';
						html += '<div class="layer">';
						html += '	<div class="layer-header">';
						html += '		<img src="/static/images/close.png?v=2.0">';
						html += '		<h2>' + cardTitle + '</h2>';
						html += '		<h5>可' + action + '总数：<span class="text-red"> ' + cardNum + ' 张</span></h5>';
						html += '		<p>(备注：单张可兑换 ' + cardTitle + ')</p>';
						html += '	</div>';
						html += '	<div class="layer-content">';
						html += '		<h5>' + cardTitle + '如下：</h5>';
						for (var i = 0; i < resp.data.length; i++) {
							var item = resp.data[i];
							html += '	<div class="card-image">'; 
							html += '		<div class="dis-flex">';
							html += '			<div class="flex-item amount">1</div>';
							html += '			<div class="flex-item flex-grow-3">';
							html += '				<span> x ';
							html += '					(' + item.title + ')';
							html +=	'				</span><br />';
							html += '				<span>通用兑换券</span>';
							html += '			</div>';
							html += '		</div>';
							html += '		<p>此券现有 ' + item.num + ' 张</p>';
							html += '	</div>'; }
						html += '	</div>';
						html += '	<button id="btnTransfer" data-num="' + cardNum + '" data-id="' + cardOrParentId + 
										'" class="hls-btn empty-' + cardNum + '">' + action + '</button>';
						html += '</div>';
						layer.open({content: html});
						$('.hls-page').addClass('over-hidden');
						$('.over-hidden').on('touchstart', function() {
							$(this).removeClass('over-hidden');
						});
						$('.layermchild').addClass('hls-layermchild')
						$('.layer-header img').on('touchend', function() {
							$('.hls-page').removeClass('over-hidden');
							hls.util.Dialog.closeLoading();
						});
						$('#btnTransfer').on('touchend', function() {
							if ($(this).attr('data-num') == 0) {
								hls.util.Dialog.showMessage('您还没有集齐此乐券，无法兑换。');
								return;
							}
							var yes = window.confirm('请确认兑换？');
							if (! yes) {
								return;
							}
							self.exchangeCard(cardOrParentId, 1);
						});
				} else {
					hls.util.Dialog.showMessage(resp.errmsg);
				}
			}).fail(logsArticle.netError);
		},

		exchangeCard: function(targetId, ifGroupCard) {
			var params = {
				"target_id": targetId,
				"if_group_card": ifGroupCard
			};
			$.post('/card/exchange_cards', params, function(resp) {
				if (resp.errcode === 0) {
					hls.util.Dialog.showMessage('操作完成，兑换数量：' + resp.data.settled_num + '张。', function() {
						location.reload();
					});
				} else {
					hls.util.Dialog.showMessage(resp.errmsg + '。');
				}
			}).fail(logsArticle.netError);
		},	
	};

	Page.init();

	

	var logsArticle = {

		init: function() {
			this.bindEvent();
		},

		bindEvent: function() {
			var self = this;
			// 当用户滚动页面到底部，加载下一页数据
			$(document).scroll(function() {
				if ($(this).height() - $(this).scrollTop() <= window.screen.availHeight) {
					if ($('#articleLogs').hasClass('hls-hidden')) {
						return;
					}
					if (isLoading) {
						return;
					}
					isLoading = true;
					self.loadList();
				}
			});

			self.loadList();
		},

		loadList: function() {
			var params = {
				"type": queryType,
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
			$.get('/transfer/fetch_logs/' + $('#role_str').val(), params, function(resp) {
				hls.util.Dialog.closeLoading();
				if (resp.errcode == 0) {
					if (resp.data.length === 0) {
						if ($('#trans_list li').length != 0) return;
						content = '<li class="item-empty text-center">你还没有乐券记录。</li>';
						$('#trans_list').append(content);
					}
					clearTimeout(loading);
					hls.util.Dialog.closeLoading();
					isLoading = false;
					if (resp.data.length !== pageSize) {
						hasNextPage = false;
					} else {
						page++;
					}
					var content = '';
					resp.data.forEach(function(item) {
						content += '<li>';
						content += '	<p>';
						content += '		<span>' + item.nickName + '<br/>';
						content += '			<small>' + hls.util.DateTimeUtil.formatDateTime(parseInt(item.transferTime) * 1000) + '</small>';
						content += '		</span>';
						content += '		<span class="card-type-title">' + item.type + item.title + '</span>';
						if (item.type == '发送' || item.type == '兑换') {
							content += '	<span>- ' + item.num + '张</span>';
						} else if (item.type == '接收' || item.type == '中得') {
							content += '	<span class="text-red">+ ' + item.num + '张</span>';
						} else {
							content += '	<span>' + item.num + '张</span>';
						}
						content += '	</p>';
						content += '</li>';
					});
					$('#trans_list').append(content);
				} else {
					hls.util.Dialog.showErrorMessage(resp.errmsg);
				}
			}).fail(self.netError);
		},

		netError: function(err) {
			if (loading) {
				clearTimeout(loading);
			}
			hls.util.Dialog.closeLoading();
			hls.util.Dialog.showErrorMessage('无法连接服务器');
		}

	};

	logsArticle.init();
});