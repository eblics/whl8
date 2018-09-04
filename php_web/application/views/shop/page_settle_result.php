<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/wait_confirm.css?t=<?=time()?>">
<div class="hls-page">
	<header>
		<img alt="images" src="/static/images/1.png" />
		<p class="first green">提交成功</p>
		<p class="txt-gray">待公司确认</p>
	</header>
	<div class="tip">
		<img alt="images" src="/static/images/tishi.png">
		<span class="txt-gray">请等待公司审核！（24小时内）</span>
	</div>
	<button id="detail_btn" class="hls-btn">核销明细</button>
</div>
<script type="text/javascript">
$('body').addClass('hls-back-gray');
$('#detail_btn').on('touchend', function() {
	location.replace('/settle/notes');
});
</script>
<?php include 'footer.php';?>