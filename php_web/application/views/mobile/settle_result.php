<?php include 'header.php';?>
<style type="text/css">
.hls-page {text-align: center;}
.header h3 {padding: 25px;border-bottom: 1px solid silver;margin: 0 15px;font-size: 16px;}
.header h2 {padding-top: 30px;color: #333;}
.header h2 span {font-size: 24px;color: #e0353c;}
.header p  {padding-bottom: 25px;}
.line-10 {position: absolute;height: 10px;background-color: #f0f1f3;width: 100%;left: 0;}
.content {text-align: left;padding: 20px 10px;}
.content p {line-height: 24px;color: #999;}
.content p span {color: #333;}
.hls-btn {position: absolute;bottom: 15%;left: 50%;margin-left: -35%;}
</style>
<div class="hls-page">
	<div class="header">
		<h3><?=$platform?></h3>
		<h2><span><?=$amount?></span><?=$type?></h2>
		<p>兑换完成</p>
	</div>
	<div class="line-10"></div>
	<div class="content">
		<p>交易类型：<span>兑换</span></p>
		<p>卡券种类：<span><?=$card_title?></span></p>
		<p>交易时间：<span><?=$event_time?></span></p>
		<?php if ($online):?>
			<button class="hls-btn" onclick="javascript:history.back()">返回</button>
		<?php else:?>
			<button class="hls-btn" onclick="location.href = '/app/mall/order.html?mallid=<?=$mall_id?>'">查看订单</button>
		<?php endif;?>
	</div>
<?php include 'footer.php';?>