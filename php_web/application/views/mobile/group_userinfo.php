<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/group_common.css,static/css/group.css" />
</head>
<body class="noselect">
	<div id="page_group_add" class="wraper noselect group_add">
		<input type="hidden" id="groupId" name="groupId" value="<?=$data->groupId?>"/>
		<div class="img" id="headImage">上传我的头像<p id="view"><?=$data->headImage!=''?"<img src=\"".$data->headImage."\"/>":""?></p><input id="file" type="file" /></div>
		<div class="name"><input type="text" id="nickName" maxlength="10" value="<?=$data->nickName!=''?$data->nickName:'填写我的群昵称（10个字以内）'?>" /></div>
		<div class="btn"><span id="btnNext" class="weui_btn weui_btn_disabled weui_btn_primary">保存</span></div>
	</div>
	<div class="img_cut">
		<div id="clipArea"></div>
		<div id="clipBtn" class="weui_btn weui_btn_primary">保存</div>
		<div id="cancelBtn" class="weui_btn weui_btn_default">取消</div>
	</div>
</body>
</html>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/iscroll-zoom.js,static/js/hammer.js,static/js/jquery.photoClip.js,static/js/group_common.js"></script>
<script type="text/javascript" src="/static/js/group_userinfo.js"></script>