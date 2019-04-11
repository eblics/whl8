<?php $this->config->set_item('cdn_m_url',$this->config->item('mobile_url'));?>
<!DOCTYPE html>
<html>
<head>
    <title>红码·友情提示</title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>static/css/errortip.css" />
</head>
<body onstart="">
    <div id="page-err" class="wraper err-content">
        <div class="err-backimg"></div>
        <div class="err-text"><?=$errmsg?></div>
        <div class="err-pic" >
            <img src="/static/images/qrcode_hls.jpg" style="width:180px;height:180px;"/>
        </div>
    </div>
</body>
</html>
<?php exit();?>