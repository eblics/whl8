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
<body>
	<div id="page_group_pwd" class="wraper noselect group_pwd">
		<input type="hidden" id="groupId" value="<?=$groupId?>"/>
		<div class="over"><?=$isupdate?'随机生成 >':'跳过 >';?></div>
		<div class="txt"><strong>设置进群口令</strong>（口令可邀请小伙伴进群）</div>
		<div class="pwd"><input type="text" id="groupPassword" maxlength="50" value="<?=$password?>" /></div>
		<div class="btn"><span id="btnNext" class="weui_btn weui_btn_disabled weui_btn_primary">下一步</span></div>
	</div>
</body>
</html>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js,static/js/group_pwd.js"></script>