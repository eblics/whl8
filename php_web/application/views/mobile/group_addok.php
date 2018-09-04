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
	<div id="page_group_addok" class="wraper noselect group_addok center">
		<input type="hidden" id="groupId" value="<?=$groupId?>"/>
		<input type="hidden" id="mchId" value="<?=$mchId?>"/>
		<div class="icon weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
		<div class="txt weui_text_area">
			<h2 class="weui_msg_title">操作成功</h2>
		</div>
		<div class="btn"><span id="btnNext" class="weui_btn weui_btn_primary">进入群组</span></div>
	</div>
</body>
</html>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js,/static/js/group_addok.js"></script>