/*背景音乐	*/
(function() {
	window.bgm = new Audio();
	bgm.src = 'resource/assets/music.mp3';
	bgm.loop = "loop";
	bgm.autoplay = "autoplay";
	bgm.toggle = function() {
		if (this.paused) {
			this.play();
		} else {
			this.pause();
			this.one = false;
		}
	}
	bgm.addEventListener('play', function() { //播放
		$('#music').removeClass('pause');
	});
	bgm.addEventListener('pause', function() { //暂停
		$('#music').addClass('pause');
	});
	document.addEventListener("WeixinJSBridgeReady", function() { //自动播放1
		WeixinJSBridge.invoke('getNetworkType', {}, function(e) {
			bgm.play();
		});
	}, false);
	$(document).one('touchstart', function() { //自动播放2
		bgm.one || bgm.play();
	});
})();