<!DOCTYPE html>
<html>
<head>
	<title>我的红包</title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/common.css" />
	<link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/account.css?v=1.0" />
	<script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>static/js/jquery-2.2.0.min.js"></script>
	<script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>static/js/common.js"></script>
	<script type="text/javascript">
		var accountData = {
			'mchId': <?=$mchId?>,
			'wxRpTotalNum': <?=$wxRpTotalNum?>,
			'normalAmount': <?=$normalAmount?>,
			'groupAmount': <?=$groupAmount?>,
			'withdrawLimit': <?=$withdrawLimit?>
		};
	</script>
	<script type="text/javascript" src="<?=$this->config->item('mobile_url')?>static/js/account.js?v=1.2"></script>
</head>
<body style="display: none;">
	<div id="pageAccount" class="wraper noselect">
		<div class="balance">
			<div class="user-info">
				<img src="<?=$headImg?>" />
				<span> <?=$nickname . ' (' .$wxAccountName . ')'?></span>
			</div>
			<dl class="sum">
				<dt>
					<h1 id="normalAmount"><?=bcdiv($normalAmount,100,2)?></h1>
					<div class="h5"></div>
					<h3>普通红包 ( 元 )</h3>
				</dt>
				<dd>
					<h1 id="groupAmount"><?=bcdiv($groupAmount,100,2)?></h1>
					<div class="h5"></div>
					<h3>裂变红包 ( 元 )</h3>
				</dd>
			</dl>
			<div class="ann"><?=$wxSendTip?></div>
		</div>
		<ul class="tab">
			<li class="current">普通红包</li>
			<li>裂变红包</li>
		</ul>
		<div class="tab-float">
			<div class="block"></div>
		</div>
		<div class="tab-con">
			<div class="con current">
				<h3>提现金额（元）</h3>
				<div class="input"><input class="input-val" id="normalVal" type="text" maxlength="8" autocomplete="off" /><em>&yen;</em></div>
				<h3 class="tip"><em>注：</em>1、单次提取现金红包限额<?=bcdiv($withdrawLimit,100,2)?>-200元<br/><em></em>2、提现将以"微信红包"发送给您，注意查收！</h3>
				<div class="h30"></div>
				<div id="normalTake" class="btn disabled noselect">立即提取</div>
				<div class="h20"></div>
				<div class="noselect btnlist">红包明细</div>
			</div>
			<div class="con">
				<h3>提现金额（元）</h3>
				<div class="input"><input class="input-val" id="groupVal" type="text" maxlength="8" autocomplete="off" /><em>&yen;</em></div>
				<h3 class="tip"><em>注：</em>1、单次提取现金红包限额<?=$wxRpTotalNum?>-200元<br/><em></em>2、提现将以"微信红包"发送给您，注意查收！</h3>
				<div class="h30"></div>
				<div id="groupTake" class="btn disabled noselect">立即提取</div>
				<div class="h20"></div>
				<div class="noselect btnlist">红包明细</div>
			</div>
		</div>
		<div class="copyright"><a href="/about.html" target="_blank">爱创科技</a> · 提供技术支持</div>
	</div>
	<div class="pay_by_hls">
		<div class="txt">
			本次提现将由「欢乐扫」平台代为发放<br/><font color="gray">长按识别二维码关注公众号，领取红包</font>
		</div>
		<div class="qrcode">
			<img src="/static/images/qrcode_hls.jpg" />
		</div>
	</div>

<script type="text/javascript">
	$('body').fadeIn();
</script>
</body>
</html>
