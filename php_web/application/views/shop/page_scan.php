<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/scan.css?t=<?=time()?>">
<script type="text/javascript">
layer.open({type: 2});

var config = <?=$signPackage?>;
</script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<div class="hls-page">
	<img class="hls-photo" src="<?=$userinfo->headimgurl?>">
	<div class="hls-prize">
		我领到&nbsp;<span id="hls_prize">Loading...</span>啦~
		到<span class="hls-red"> "我的账户" </span>查看
	</div>
	<div class="hls-prize-detail">
		<p class="title">您已成功获得</p>
		<p class="amount">Loading...<span></span></p>
		<p class="prize-name">乐券</p>
	</div>
	<div class="btn-container">
		<button class="hls-btn rescan" onclick="scan()">重新扫码</button>
		<?php if ($role == ROLE_WAITER):?>
		<button class="hls-btn" onclick="javascript:location.replace('/account/waiter?mch_id=<?=$mch_id?>')">我的账户</button>
		<?php elseif ($role == ROLE_SALESMAN):?>
		<button class="hls-btn" onclick="javascript:location.replace('/account/salesman?mch_id=<?=$mch_id?>')">我的账户</button>
		<?php endif;?>
	</div>
</div>
<section class="hls-page" id="error_section">
	<img src="/static/images/xiong.png" />
	<div class="txt-container">
		<p id="error_message">此码不合法</p>
		<p>请点击按钮重新扫码</p>
	</div>
	<div class="btn-container">
		<button class="hls-btn" onclick="scan()">重新扫码</button>
	</div>
</section>
<input id="hidden_role" type="hidden" value="<?=$role?>" />
<input id="hidden_action" type="hidden" value="<?=$action?>" />
<input id="hidden_mch_id" type="hidden" value="<?=$mch_id?>" />
<input id="hidden_jsonp_url" type="hidden" value="<?=$jsonp_url?>" />
<script type="text/javascript" src="/static/js/scan.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>