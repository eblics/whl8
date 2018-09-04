<!DOCTYPE html>
<html>
<head>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <title>扫一扫</title>
    <script src="<?=$this->config->item('mobile_url')?>static/js/jquery-2.1.1.min.js" ></script>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="/static/lib/layer/layer.js"></script>
    <script type="text/javascript" src="<?=$this->config->item('mobile_url')?>static/js/util.js"></script>
    <script>
        // hls.util.Dialog.showLoading();
        wx.config({
            debug: <?=isset($_REQUEST['debug'])?'true':'false'?>,
			appId: '<?=$appId?>',
			timestamp:<?=$timestamp?>,
			nonceStr: '<?=$nonceStr?>',
			signature: '<?=$signature?>',
			jsApiList: ['scanQRCode']
		});
        wx.error(function(res){
               console.log(res);
        });
		wx.ready(function(){
			wx.scanQRCode({
				needResult: <?=isset($deal_with_js)? 1: 0?>, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
				scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
				success: function (res) {
					var result = res.resultStr;
				}
			});
		});
    </script>
</head>
<body>
    <div id="pageScanByJssdk" class="wraper center">
        
    </div>
</body>
</html>
