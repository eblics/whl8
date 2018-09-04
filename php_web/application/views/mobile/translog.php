<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/translog.css?v=<?=$v?>">
<div class="hls-page wraper">
	<!-- <header>
		<nav class="flex">
			<a id="all" class="selected" href="javascript:;">全部</a>
			<a id="prize" href="javascript:;">中奖乐券</a>
			<a id="trans_in" href="javascript:;">转入乐券</a>
			<a id="trans_out" href="javascript:;">转出乐券</a>
		</nav>
	</header> -->
	<article id="scroll_container">
		<!-- <div class="line-10"></div> -->
		<ul id="trans_list" class="trans-list">
			<!-- List Container-->
		</ul>
	</article>
	<div class="copyright"><a href="/about.html" target="_blank">爱创科技</a> · 提供技术支持</div>
</div>
<script type="text/javascript" src="/static/js/translog.js?v=<?=$v?>"></script>
<?php include 'footer.php';?>