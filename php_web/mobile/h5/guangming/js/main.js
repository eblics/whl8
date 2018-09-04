var SHAKE_THRESHOLD = 500;
var last_update = 0;
var x, y, z, last_x = 0, last_y = 0, last_z = 0;
var IsShake = false;

var Page = {

	init: function() {
		var self = this;
		hls.util.Dialog.showLoading();
		hlsjs.ready(function() {
			self.bindEvent();
			hls.util.Dialog.closeLoading();
		});
	},

	bindEvent: function() {
		var self = Page;
		window.ondevicemotion = self.onShake;

        setTimeout(function() {
        	if (IsShake) {
        		return;
        	}
        	IsShake = true;
        	hls.util.Dialog.showLoading();
            hlsjs.takeActivity(function(resp) {
				hls.util.Dialog.closeLoading();
				if (resp.errcode == 0) {
					self.showPrize(resp);
				} else {
					self.showError(resp);
				}
			});
        }, 5000);

        // 规则
        $(".rule_img").click(function () {
            $(".msk_rule").show();
        });

        // 蒙层关闭
        $(".close_btn").click(function () {
            $(this).parent().hide();
        });
	},

	onShake: function(eventData) {
		var self = Page;
        var acceleration = eventData.accelerationIncludingGravity;
        var curTime = new Date().getTime();
        if ((curTime - last_update) > 10) {
            var diffTime = curTime - last_update;
            last_update = curTime;
            x = acceleration.x;
            y = acceleration.y;
            z = acceleration.z;
            var speed = Math.abs(x +y + z - last_x - last_y - last_z) / diffTime * 10000;
            if (speed > SHAKE_THRESHOLD && !IsShake) {
            	IsShake = true;
                $('#shake_music').get(0).play();
                hls.util.Dialog.showLoading();
                setTimeout(function() {
	                hlsjs.takeActivity(function(resp) {
						hls.util.Dialog.closeLoading();
						if (resp.errcode == 0) {
							self.showPrize(resp);
						} else {
							self.showError(resp);
						}
					});
                }, 2000);
            }
            last_x = x;
            last_y = y;
            last_z = z;
        }
	},

	showPrize: function(resp) {
		var prizeName;
		if (resp.datatype == 0) {
			prizeName = (resp.amount * 0.01) + '元红包';
			$('#prize_name').text('√ 获得【' + prizeName + '】');
		} else if (resp.datatype == 3) {
			prizeName = resp.amount + '积分';
			$('#prize_name').text('√ 获得【' + prizeName + '】');
		} else if (resp.datatype == 100) {
			prizeName = '';
			resp.multiData.forEach(function(item) {
				if (item.strategyType == 0) {
					prizeName += '√ 获得【' + (item.value * 0.01) + '元红包】';
					prizeName += '<br/>';
				} else if (item.strategyType == 3) {
					prizeName += '√ 获得【' + item.value + '积分】';
					prizeName += '<br/>';
				} else {
					prizeName += '√ 无法识别的奖品';
					prizeName += '<br/>';
				}
				$('#prize_name').html(prizeName);
			});
		} else {
			$('#prize_name').text('√ 无法识别的奖品');
		}
		$('.msk_redpocket').show();
	},

	showError: function(resp) {
		$('.kxyktext').text(resp.data.alt_text);
		$(".msk_rule").hide();
		$('.msk_kxyk').show();
		alert(resp.errmsg);
	}
};
$(window).load(function() {
    Page.init();
    $('body').fadeIn();
});
