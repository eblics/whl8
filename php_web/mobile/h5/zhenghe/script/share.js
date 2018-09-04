/*
*	created by zy.
*
*	本页为分享设置页，需注入微信公众号权限以及更改焦点图绝对路径
*/

var shareImgUrl = 'http://www.h5case.com.cn/case/tsbeer/images/share.jpg';
var shareLinkUrl = window.location.href;
var shareTitle = '快来喝泰山原浆，扫码赢红包大奖！';
var shareDesc = '泰山原浆7天鲜，揭盖扫，码上有奖！';
$(function() {
	//查询接口注入权限验证配置信息
	var proveUrl = "http://sso.h6app.com/jssdk2/getSignPackage";

	function getConfig(canMethod) {
		var url = encodeURIComponent(window.location.href);
		var sct = document.createElement("script");
		sct.setAttribute("id", "dataProxy");
		sct.src = proveUrl + '?url=' + url + '&callback=' + canMethod + '';
		document.getElementsByTagName("body")[0].appendChild(sct);
	}

	function setConfig(result) {
		wx.config(result.data);
		setShareData();
	}
	window.setConfig = setConfig;

	getConfig("setConfig");
});
//设置分享参数
function setShareData() {
	wx.ready(function() {
		wx.onMenuShareAppMessage({
			"imgUrl": shareImgUrl,
			"link": shareLinkUrl,
			"desc": shareDesc,
			"title": shareTitle,
			success: function(res) {
				_czc && _czc.push(["_trackEvent", "分享", "朋友"]);
			},
			cancel: function(res) {

			},
		});
		wx.onMenuShareTimeline({
			"imgUrl": shareImgUrl,
			"link": shareLinkUrl,
			"title": shareTitle,
			success: function() {
				_czc && _czc.push(["_trackEvent", "分享", "朋友圈"]);
			},
			cancel: function() {}
		});
	});
}