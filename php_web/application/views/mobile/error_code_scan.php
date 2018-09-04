<!DOCTYPE html>
<html>
<head>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/errortip.css" />
    <title>消息</title>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/jquery-2.2.0.min.js"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/error.js"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/hlsjs.js"></script>
    <script type="text/javascript">
        <?php if(isset($appId)){?>
            hlsjs.ready({appid:'<?=$appId?>'});
        <?php }?>
    </script>
</head>
<body onstart="">
    <div id="page-err" class="wraper err-content">
        <div class="err-backimg"></div>
        <div class="err-false"></div>
        <div class="err-text"><?=$errmsg?></div>
        <?php if(isset($appId)){?>
        <div class="err-button" >
            <div id="reScan">重新扫码</div>
        </div>
        <?php }?>
    </div>
</body>
</html>
<?php exit(); ?>