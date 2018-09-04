<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/wait_confirm.css?t=<?=time()?>">
<div class="hls-page">
	<header>
		<img alt="images" src="/static/images/1.png" />
		<p class="first green">扫码成功</p>
		<p class="txt-gray">兑换成功</p>
	</header>
	<div class="tip">
		<img alt="images" src="/static/images/tishi.png">
		<span class="txt-gray">正在等待对方确认操作！</span>
	</div>
	<button id="finish_back_btn" class="hls-btn disable">返回主页</button>
</div>
<input id="role" type="hidden" value="<?=$role?>" />
<input id="mch_id" type="hidden" value="<?=$mch_id?>" />
<script type="text/javascript" src="/static/js/wait_confirm.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>