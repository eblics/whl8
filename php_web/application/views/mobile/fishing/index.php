<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/font/iconfont.css,static/css/group_common.css,static/css/group_fishing.css<?='&t='.time()?>" />
</head>
<body class="noselect">
	<div class="page_fishing app_fishing_main">
		<div class="opacity bgcolor_flash"></div>
		<div class="dengguang flash"></div>
		<div class="laobg"></div>
		<div class="laotool"><div class="shou shoushi"></div></div>
		<div class="rule">游戏规则</div>
		<div class="count">红包池：累计<span id="numTotal"></span> 剩余<span id="numRemain"></span></div>
		<ul class="menu">
			<li id="btnRen"></li>
			<li id="btnLao"></li>
			<li id="btnLog"></li>
		</ul>
	</div>
	<div class="page_fishing app_fishing_ren">
		<div class="box">
			<div class="close"></div>
			<div class="title">扔炸弹</div>
			<div class="txt">请设置金额：</div>
			<ul class="amount">
				<li data='50' class="cur">0.5元</li>
				<li data='100'>1元</li>
				<li data='200'>2元</li>
				<li data='500'>5元</li>
			</ul>
			<div class="btn">扔出去</div>
		</div>
		<div class="rening"></div>
	</div>
	<div class="page_fishing app_fishing_kai">
		<div class="box">
			<div class="close"></div>
			<div class="title">温馨提示</div>
			<div class="pic"></div>
			<div class="txt">开箱子，有50%的概率爆炸（扣除对应额度红包），有50%的概率获得红包</div>
			<ul class="btn">
				<li id="btnGoon">继续打开</li>
				<li id="btnBack">扔回海里</li>
			</ul>
		</div>
		<div class="rening"></div>
	</div>

	<div class="page_fishing app_fishing_result">
		<div class="box">
			<div class="close"></div>
			<div class="title">红包呀</div>
			<div class="pic"><span></span></div>
			<div class="btn">继续捞</div>
		</div>
	</div>

	<div class="page_fishing app_fishing_rule">
		<div class="box">
			<div class="close"></div>
			<div class="title">游戏规则</div>
			<div class="txt">
				<p>1、扔炸弹：从您红包余额里扣除对应的金额，放入有炸弹的箱子扔进海里。</p>
				<p>2、捞红包：捞到小星星，说明什么也没捞着。捞到箱子，可以开箱子，有50%的机率获得红包，有50%的机率输掉红包（从您红包余额扣除）。</p>
				<p>3、如您红包余额过低，可能什么也捞不着。</p>
			</div>
		</div>
	</div>

</body>
</html>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js"></script>
<script type="text/javascript" src="/static/js/group_fishing.js?<?=time()?>"></script>