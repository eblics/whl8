// -------------------------------------------------------------
// 界面事件绑定
function bindEvent() {

	// 固定内容高度
	$('body').height($(window).height()); 

	// 阻止默认事件
	$(document).on("touchstart touchmove mousedown mousemove", function(event) {
		var thistag = event.target.tagName;
		var tagid = event.target.id;
	});

	//规则点击事件
	$('.index .ruleBtn').off().on('touchend', function(){
		$('.ruleMsk').fadeIn(200);
		$('.ruleMsk .closeBtn, .ruleMsk').off().on('touchend', function(event){
			event.stopPropagation();
			event.preventDefault();
			if(event.target.className.indexOf('closeBtn') < 0 && 
				event.target.className.indexOf("ruleMsk") < 0) {
				return;
			}
			$('.ruleMsk').fadeOut(200);
		});	
	});

	//分享点击事件
	$('.ruleSc .shareBtn').off().on('touchend',function(){
		$('.shareMsk').fadeIn(200);
		$('.shareMsk').off().on('touchend',function(event){
			event.stopPropagation();
			event.preventDefault();
			if(event.target.className.indexOf('shareImg') > -1 ) {
				return;
			}
			$('.shareMsk').fadeOut(200);
		});
	});

	//二维码图层关闭事件
	$('.codeMsk .closeBtn').off().on('touchend',function(event){
		event.stopPropagation();
		event.preventDefault();
		if(event.target.className.indexOf('closeBtn') < 0 && 
			event.target.className.indexOf("codeMsk") < 0) {
			return;
		}
		$('.codeMsk').fadeOut(200);
	});
}

// -------------------------------------------------------------
// 使用pace显示加载效果
function showLoading() {
	Pace.start({
        ajax: false,
        restartOnPushState: false,
        restartOnRequestAfter: false,
        document: false
    });

	Pace.on('done', onLoadEnd);
}

// -------------------------------------------------------------
// 页面加载完成之后执行
function onLoadEnd() {
	$('.scene').removeClass('hidden');
	$('.index').fadeIn(200);
	$('#mskImg').eraser({
		"completeRatio": .1,
		"completeFunction": onEraser
	});
	//啤酒瓶动画
	$(".jiubei, .ruleSc .jiubei").addClass("leftIn");
	$(".pijiu, .ruleSc .pijiu").addClass("rightIn");
}

// -------------------------------------------------------------
//窗口改变对应事件
function onResize() {
	var scaleX  = $(window).width() / $('body').width();
	var scaleY  = $(window).height() / $('body').height();

	$('body').css({
		'transform-origin'         : '0% 0% 0px',
		'-webkit-transform-origin' : '0% 0% 0px',
		'transform'         : 'scale('+scaleX+','+scaleY+')',
		'-webkit-transform' : 'scale('+scaleX+','+scaleY+')'
	});
}

// -------------------------------------------------------------
// 刮刮卡刮开处理
function onEraser() {
	$('.codeMsk').fadeIn(200);
	$('.index .gameSc').off().on('touchend', function() {
		$('.codeMsk').fadeIn(200);
	});
	// 清除所有的图层覆盖
	$('#mskImg').hide();
	if (isWinner == 1) {
		$('.scene .codeMsk').fadeIn(200);
		$('#pricemsg').html(amount);
		$('.errmsg').hide();
		$('#pricemsg').show();
	} else {
		$('.errmsg').text(errmsg);
		$('.errmsg').show();
		$('.pricemsg').hide();
	}
}

// ====================== begin =======================
var fromScan         = 1, // 是否是扫码进入，1：是，0：否
	isWinner         = 0, // 是否中奖，1：是，0：否
	isWinnerComplete = 0, // 抽奖完成，1：是，0：否
	errmsg           = 'Loading...', // 错误消息
	amount           = 'Loading...', // 中奖金额
	wxQrcodeUrl;
$(function() {

	// 显示加载进度
	showLoading();

	// 初始化绑定所有事件
	bindEvent();

	// 初始化刮奖事件
	hlsjs.ready(function() {
		$('.sc_msk').on('touchstart', function() {
			$('.sc_msk').off('touchstart');
			hlsjs.takeActivity(function(result) {
				isWinnerComplete = 1;
				wxQrcodeUrl = result.wx_qrcode_url;
				if (result.errcode == 0) {
				    isWinner = 1;
				    amount = '恭喜你！<br/>获得了' + result.amount + '分红包';
				    $('#pricemsg').html('恭喜你！<br/>获得了' + result.amount + '分红包');
				    $('.errmsg').hide();
					$('#pricemsg').show();
				} else if (result.errcode == 20) {
				    errmsg = '谢谢惠顾';
				} else if (result.errcode == 2) {
				    errmsg = '此码已被他人扫过';
				} else {
				    errmsg = result.errmsg;
				}
				$('.errmsg').text(errmsg);
				console.log(result);
			});
		});
	});
});