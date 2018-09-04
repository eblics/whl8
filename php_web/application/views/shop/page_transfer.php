<?php include 'header.php';?>
<script type="text/javascript" src="/static/lib/jquery.qrcode.min.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/transfer.css">
<div class="hls-page hls-center">
	<h3>方法一</h3>
	<p class="hls-small">让对方扫描下方的二维码即可</p>
	<div id="hls-qr-container" class="hls-qr-container hls-center"></div>
	<div class="hls-distance"></div>
	<!-- <h3>方法二</h3>
	<p class="hls-small">在线转移二维码即可兑换</p> -->
</div>
<input id="transfer_ticket" type="hidden" value="<?=$ticket?>" />
<script type="text/javascript" src="/static/js/transfer.js?t=<?time()?>"></script>
<?php include 'footer.php';?>