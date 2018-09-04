<?php include 'header.php';?>
<link rel="stylesheet" type="text/css" href="/static/css/settle.css?t=<?=time()?>">
<div class="hls-page">
<header>
	<div class="outter has-line">核销人信息 
		<span class="c-line">.</span>
		<a id="btn-edit" href="javascript:void(0)">编辑</a>
	</div>
	<div class="outter hls-form-container">
		<form id="salesman_info_from" method="post">
			<div>
				<span>姓　名</span>
				<input id="realname" readonly="readonly" type="text" value="<?=$salesman->realName?>" />
			</div>
			<div class="center">
				<span>手机号</span>
				<input id="mobile" readonly="readonly" type="text" value="<?=$salesman->mobile?>" />
			</div>
			<div>
				<span>身份证</span>
				<input id="id_card_no" readonly="readonly" type="text" value="<?=$salesman->idCardNo?>" />
			</div>
		</form>
		<button id="btn-save" class="hls-btn hls-hidden">保存</button>
	</div>
</header>

<div class="list-container">
	<p class="list-title">核销产品</p>
	<ul id="cards_container"></ul>
</div>
<div class="footer"></div>
<div class="btn-container">
	<a id="btn_settle" class="hls-btn no-shadow" href="javascript:void(0)">核销乐券</a><!-- 
	--><a class="hls-btn no-shadow" href="/settle/notes">核销记录</a>
</div>
</div>
<script type="text/javascript" src="/static/js/settle.js?t=<?=time()?>"></script>
<?php include 'footer.php';?>