<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <title>欢乐扫</title>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js" ></script>
    <script type="text/javascript" src="/static/lib/layer/layer.js"></script>
</head>
<body>

    <div id="pageScanByJssdk" class="wraper center" style="padding-top: 50%;color: #ccc;text-align: center;">
        正在启动扫一扫...
    </div>

    <script type="text/javascript" src="<?=config_item('mobile_url')?>static/js/util.js"></script>
    <script>
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
                needResult: <?=isset($deal_with_js)? 1: 0?>,
                scanType: ["qrCode", "barCode"], 
                success: function (res) {
                    var result = res.resultStr;
                    $.post('/transfer/mark_scan', {ticket: result}, function(resp) {
                        if (resp.errcode == 0) {
                            if (resp.data.type == hls.enum.ScanResEnum.Transfer) {
                                location.replace("/transfer/wait_confirm?mch_id=<?=isset($mch_id)?$mch_id:''?>");
                            } 
                        } else {
                            alert(resp.errmsg);
                        }
                    }).fail(function(error) {
                        alert('无法连接服务器！');
                    });
                }
            });
        });
    </script>
</body>
</html>
