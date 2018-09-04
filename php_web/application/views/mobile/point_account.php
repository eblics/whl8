<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/point_account.css?t=<?=time()?>">
<div class="hls-page wraper">
	<section class="up">
		<div class="header">
			<img class="hls-photo" src="<?=isset($userinfo->headimgurl) ? $userinfo->headimgurl : '#'?>" />
			<p class="nickname">
				<span class="name-str"><?=isset($userinfo->nickName) ? $userinfo->nickName : '微信用户'?></span><br/>
				<span class="type-cards-num">积分：<i id="card_num" class="text-gold"><?=$points?></i></span><br/>
			<?php 
			if ($mch_id == 0 || $mch_id == 173):
			?>
			<span class="tips">注：累计100积分以上（含100）<br/>即可兑换现金红包</span></p>
			<button onclick="javascript:exchangePoint()">兑换红包</button>
			<script type="text/javascript">
				function exchangePoint() {
					var yes = window.confirm('每积分兑换1分钱现金红包，确认兑换所有积分吗？')
					if (! yes) return;
					var num = $('#card_num').text();
					num = parseInt(num);
					if (num < 100) {
						alert('积分大于100才能兑换！');
						return;
					}
					if (num > 20000) {
						alert('每次最多可兑换20000积分！');
					}
					$.post('/user/exchange_point/<?=$mch_id?>', {}, function(resp) {
						if (resp.errcode == 0) {
							alert('兑换成功！');
							location.reload();
						} else {
							alert(resp.errmsg + '！');
						}
					}).fail(function(err) {
						alert('无法连接服务器！');
					});
				}
			</script>
			<?php else:?>
				</p>
			<?php endif;?>
		</div>
	</section>
	<div class="hr"></div>
	<section class="down">
		<p class="trans-title">积分明细</p>
		<ul class="trans-list">
			<?php if (count($logs) === 0):?>
			<li class="text-center" style="color: silver;border-bottom: none;">
				你还没有积分记录。
			</li>
			<?php endif;?>
			<?php foreach ($logs as $log): ?>
			<li>
				<p>
					<?php 
					$extra = '';
					$wxErrcode = '';
					if ($log['wxStatus'] == 2) {
						$extra = '(失败)';
						if ($log['title'] == '现金兑换') {
					    	$wxErrcode = '<em class="errcode" data="'. $log['wx_errcode'] .'">?</em>';
					    }
					}
					if ($log['wxStatus'] === 0 || $log['wxStatus'] === '0' || $log['wxStatus'] == 3) {
						$extra = '(处理中)';
					}
					 ?>
					<span><?=$log['title'] . $extra . $wxErrcode?></span>
					<span class="card-type-title"></span>
					<?php if ($log['amount'] > 0): ?>
						<span class="text-gold">+<?=$log['amount']?></span>
					<?php else: ?>
						<span><?=$log['amount']?></span>
					<?php endif; ?>
				</p>
				<small><?=$log['actionTime']?></small>
				<?php 
					if ($log['title'] == '现金兑换') {
						if ($log['wxStatus'] == '1') {
							print '<em>提醒：请在公众号内领取或留意微信系统消息</em>';
						} else if ($log['wxStatus'] === '2') {
							if ($log['wx_errcode'] == 'NO_AUTH') {
								print '<em class="text-red">您的微信账号活跃度过低，提现失败，已退回账户余额</em>';
							} else {
								print '<em class="text-red">提现失败，已退回账户余额</em>';
							}
						}
					}
				?>
			</li>
			<?php endforeach;?>
			<script type="text/javascript">
				$('.trans-list .errcode').on('tap',function() {
					var tip = $(this).attr('data');
					if (tip == 'NO_AUTH')
						common.alert(0, '<div style="padding:0 10px;text-align:left;">提取失败！微信公司对活跃度过低的微信进行了红包拦截。<BR>以下方法能提高微信活跃度<BR>1.保持每天登陆，经常与好友聊天互动<BR>2.绑定实名认证的银行卡<BR>正常使用一段时间后，微信公司就会帮您提升活跃度。</div>', 1);
					if (tip == 'SENDNUM_LIMIT')
						common.alert(0, '提取失败！<br/><br/>您今日领取红包个数达到上限，明天再试吧。', 1);
					if (tip == 'FREQ_LIMIT')
						common.alert(0, '提取失败！<br/><br/>提现过于频繁，请稍后再试吧。', 1);
					if (tip == 'NOTENOUGH')
						common.alert(0, '提取失败！<br/><br/>红包发放助手今天忘带钱包了，明天再试吧。', 1);
					if (tip == 'MONEY_LIMIT')
						common.alert(0, '提取失败！<br/><br/>红包金额不在微信限制范围内，明天再试吧。', 1);
					if (tip == null || tip == "null") {
						common.alert(0, '提取失败！<br/><br/>您的微信账号活跃度过低<br />提现失败，已退回账户余额。', 1);
					}
				}).removeClass('errcode').addClass('errcode_done');
			</script>
			<?php if (count($logs) > 5):?>
			<li class="btn-more">
				<a href="/user/point_logs/<?=$mch_id?>">查看更多明细</a>
			</li>
			<?php endif;?>
		</ul>
	</section>
	<div class="copyright"><a href="/about.html" target="_blank">爱创科技</a> · 提供技术支持</div>
</div>
<script type="text/javascript" src="/static/js/point_account.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>