<?php
if($subscribe){
	$qrTip='进入公众号后，请允许使用您的地理位置。';
	$qrTxt='长按、识别二维码，进入公众号。';
}else{
	$qrTip='关注公众号后，请允许使用您的地理位置。';
	$qrTxt='长按、识别二维码，关注公众号。';
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$merchant->name?></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
	<link type="text/css" rel="stylesheet" href="/static/css/subscribe.css" />
	<script type="text/javascript"" src="/static/js/jquery-2.2.0.min.js"></script>
	<script type="text/javascript"" src="/static/js/common.js"></script>
</head>
<body>
	<div id="pageSubscribe" class="wraper center">
		<div class="qrtip"><?=$qrTip?></div>
		<div class="qrcode"><img src="<?='/files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl?>" /></div>
		<div class="qrtxt"><?=$qrTxt?></div>
	</div>
</body>
</html>
