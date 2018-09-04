<?php
$userImage=[];
foreach($allMembers as $k=>$v){
	array_push($userImage,$v->headImage);
}
$userImage=json_encode($userImage);
?>
<!DOCTYPE html> 
<html id="page_chat">
<head>
	<title></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/font/iconfont.css,static/css/group_common.css,static/css/group.css" />
</head>
<body class="noselect">
	<ul class="char_bar">
		<li id="groupChatSetting" class="btn iconfont icon-users"></li>
		<li><?=$groupName?><span>（<em id="online">0</em>/<em id="memberNum">0</em>）</span></li>
	</ul>
	<div class="page_chat">
		<div id="historyLog"></div>
	</div>
	<div class="chat_input">
		<div class="chat_input_in">
			<span id="groupChatSend">发送</span><div class="plus" id="groupPlus"><i class="iconfont icon-jiahao"></i></div><div class="input"><input id="groupChatInput" type="text" maxlength="500"/></div>
		</div>
		<ul id="groupApps" class="group_apps">
			<li id="appScanPK"><span><i class="iconfont icon-faqibisai"></i></span><em>扫码PK</em></li>
			<!-- <li id="appFishing"><span><i class="iconfont icon-zhadan"></i></span><em>捞红包</em></li> -->
		</ul>
	</div>
	<div class="plus_tip"></div>
</body>
</html>
<script>
	var currentGroup={
		'id':'<?=$id?>',
		'memberNum':'<?=$memberNum?>',
		'mchId':'<?=$mchId?>'
	};
	var currentUser={
		'id':'<?=$userId?>',
		'name':'<?=$userName?>',
		'image':'<?=$userImg?>'
	};
	var CI_ENV='<?=$_SERVER['CI_ENV']?>';
	var userImage=<?=$userImage?>;
</script>
<script src="/socket.io/socket.io.js"></script>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js"></script>
<script type="text/javascript" src="/static/js/group_chat.js?t=<?=time()?>"></script>

