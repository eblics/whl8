<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/red_packet_logs.css?v=<?=$v?>">
<div class="hls-page wraper">
	<article id="scroll_container">
		<!-- <div class="line-10"></div> -->
		<ul id="red_packet_list" class="trans-list">
			<!-- List Container-->
		</ul>
	</article>
	<div class="copyright"><a href="/about.html" target="_blank">爱创科技</a> · 提供技术支持</div>
</div>
<input id="mch_id" type="hidden" name="mch_id" value="<?=$mch_id?>">
<script type="text/javascript" src="/static/js/red_packet_logs.js?v=<?=$v?>"></script>
<?php include 'footer.php';?>