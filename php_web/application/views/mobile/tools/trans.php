<?php include 'header.php';?>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
wx.config({
    debug: <?=isset($_REQUEST['debug'])?'true':'false'?>,
	appId: '<?=$appId?>',
	timestamp:<?=$timestamp?>,
	nonceStr: '<?=$nonceStr?>',
	signature: '<?=$signature?>',
	jsApiList: ["scanQRCode"]
});
</script>
<style type="text/css">
	.layermbox0 .layermchild {
		width: 75%;
	}
</style>
<div class="hls-page">
	<input id="lecode_input" type="text" />
	<button id="open_scan" class="hls-btn">扫一扫</button>
	<button id="outter2inner" class="hls-btn">获取明码</button>
</div>
<script type="text/javascript" src="/static/js/tools.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>