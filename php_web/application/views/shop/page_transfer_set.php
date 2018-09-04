<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/transfer_set.css">
<div class="hls-page">
	<header>
		<span class="hls-tag"><?=$prize_name?></span>
		<input id="trans_num" value="<?=$total_num?>" type="text" />
	</header>	
	<button id="btn_transfer_confirm" class="hls-btn">确认转出</button>
</div>
<script type="text/javascript">
var width = $('.hls-tag').width();
$('#trans_num').css('paddingLeft', (width + 20) + 'px');	
</script>
<script type="text/javascript" src="/static/js/transfer_set.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>