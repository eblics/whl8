/**
 * 业务员核销界面
 * 
 * @author shizq
 */
$(function() {

	var cardNumber = 0;

	/**
	 * 记录当前是否为编辑状态
	 * 
	 * @type {Boolean}
	 */
	var isEdit = false;
	
	function init() {
		$('body').addClass('hls-back-gray');
		$('form input').prop('disabled', true);
		getCards();
		bindEvent();
	}
	
	function bindEvent() {
		$('#btn_settle').on('touchend', function() {
			if (! $.trim($('#realname').val()) || ! $.trim($('#id_card_no').val())) {
				hls.util.Dialog.showMessage('请先填写个人信息');
				return;
			} else {
				if (isEdit) {
					hls.util.Dialog.showMessage('请先保存个人信息');
					return;
				}
			}
			if (cardNumber <= 0) {
				hls.util.Dialog.showMessage('你没有任何可核销的乐券哦');
				return;
			}
			hls.util.Dialog.showConfirm("确认核销以上所有乐券吗", function() {
				location.href = '/settle/settles?_=' + new Date().getTime();
			});
		});
		
		$('#btn-edit').on('touchend', function() {
			isEdit = true;
			if (! $('#btn-save').hasClass('hls-hidden')) {
				saveInfo();
				return;
			}
			$('#btn-save').removeClass('hls-hidden');
			$(this).text('完成');
			$('form input').attr('readonly', false);
			$('form input').prop('disabled', false);
		});
		
		$('#btn-save').on('touchend', function() {
			saveInfo();
		});
	}
	
	/**
	 * 保存用户信息
	 */
	function saveInfo(smsCode) {
		var params = {
			realname: $('#realname').val().trim(),
			mobile: $('#mobile').val().trim(),
			id_card_no: $('#id_card_no').val().trim()
		};
		if (hls.util.StringUtil.isEmpty(params.realname)) {
			hls.util.Dialog.showErrorMessage('姓名不能为空');
			return;
		}
		if (params.realname.length < 2 || params.realname.length > 8) {
			hls.util.Dialog.showErrorMessage('姓名格式不正确');
			return;
		}
		if (! hls.util.StringUtil.isMobile(params.mobile)) {
			hls.util.Dialog.showErrorMessage('手机号码格式不正确');
			return;
		}
		if (! /^[0-9]{17}[0-9Xx]$/.test(params.id_card_no)) {
			hls.util.Dialog.showErrorMessage('身份证格式不正确');
			return;
		}
		hls.util.Dialog.showLoading();
		if (smsCode) {
			params.sms_code = smsCode;
		}
		hls.api.Account.update(params, function(resp) {
			hls.util.Dialog.closeLoading();
			hls.util.Dialog.showMessage('保存成功');
			isEdit = false;
			$('#btn-save').addClass('hls-hidden');
			$('#btn-edit').text('编辑');
			$('form input').attr('readonly', true);
			$('form input').prop('disabled', true);
		}, function(errmsg) {
			hls.util.Dialog.closeLoading();
			if (errmsg === 'sms_code_required') {
				var smsCode = window.prompt('请输入短信验证码:');
				if (smsCode) {
					saveInfo(smsCode);
				} else {
					hls.util.Dialog.showErrorMessage('需要提供短信验证码');
				}
			} else {
				hls.util.Dialog.showErrorMessage(errmsg);
			}
			if (errmsg == '没有数据更新') {
				isEdit = false;
				$('#btn-save').addClass('hls-hidden');
				$('#btn-edit').text('编辑');
				$('form input').attr('readonly', true);
				$('form input').prop('disabled', true);
			}
		});
	}
	
	/**
	 * 获取所有的卡券信息
	 */
	function getCards() {
		hls.api.Settle.cards(function(resp) {
			viewAdapter(resp);
		}, function(errmsg) {
			hls.util.Dialog.showErrorMessage(errmsg);
		});
	}
	
	function viewAdapter(cards) {
		if (! cards.length) {
			var li = $('<li>你的兜里空空如也...快去兑换吧！</li>');
			$('#cards_container').append(li);
		}
		cards.forEach(function(card) {
			cardNumber++;
			var li = $('<li></li>');
			var span = $('<span></span>').addClass('vertical-middle');
			span.on('touchend', function() {
				hls.util.Dialog.showMessage('目前必须选择所有项');
			});
			var img = $('<img />').addClass('vertical-middle');
			img.attr('src', card.imgUrl || '/static/images/hlb.png');
			var div = $('<div></div>').addClass('vertical-middle');
			var p = $('<p></p>').text(card.title);
			var small = $('<small></small>').text('数量：' + card.num);
			div.append(p).append(small);
			li.append(span).append(img).append(div);
			
			$('#cards_container').append(li);
		});
	}
	
	init();
});