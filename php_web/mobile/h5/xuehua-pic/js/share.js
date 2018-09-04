var shareImgUrl = 'http://www.h6app.com/data/case/';
var shareLinkUrl = window.location.href;
var shareTitle = '分享标题';
var shareDesc = '分享内容';
$(function() {
	//查询接口注入权限验证配置信息
	var proveUrl = "http://sso.h6app.com/jssdk/config";

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