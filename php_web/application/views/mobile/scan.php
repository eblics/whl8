<?php define('cdn_ver','3.7'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/common.css?v=<?=cdn_ver?>" />
    <link type="text/css" rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/errortip.css?v=<?=cdn_ver?>" />
    <link type="text/css" rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/scan.css?v=<?=cdn_ver?>" />
    <?php if (! isset($gmLoading)):?>
        <title>欢乐扫</title>
    <?php else:?>
        <title>Loading...</title>
    <?php endif;?>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/jquery-2.2.0.min.js"></script>
</head>
<body>
    <div id="loading">
        <div class="content">
            <?php if (! isset($gmLoading)):?>
                <img class="goldenCoin goldenCoin1" src="/static/images/loading/goldencoin.png">
                <img class="goldenCoin goldenCoin2" src="/static/images/loading/goldencoin.png">
                <img class="goldenCoin goldenCoin3" src="/static/images/loading/goldencoin.png">
                <img class="goldenCoin goldenCoin4" src="/static/images/loading/goldencoin.png">
                <div class="character">
                    <img class="body" src="/static/images/loading/content.png">
                    <img class="foot_left" src="/static/images/loading/L.png">
                    <img class="foot_right" src="/static/images/loading/R.png">
                </div>
                <img class="text" src="/static/images/loading/text.png">
            <?php else:?>
                <style type="text/css">
                    body {
                        background-image: url(/static/images/loading-gm.jpg?v=1.0);
                        background-position: center;
                        background-size: 100% 100%;
                    }
                </style>
            <?php endif;?>
        </div>
    </div>
    <div id="page-err" class="err-content" style="display:none;">
        <div class="err-backimg"></div>
        <div class="err-false"></div>
        <div class="err-text">失败了</div>
        <?php if($geoLocation!=1){?>
        <div class="err-button" >
            <div id="reScan">重新扫码</div>
        </div>
        <?php }?>
    </div>

    <script type="text/javascript">
        $(".content").fadeIn("slow");
        window.lecode = '<?=$code?>';
        window.mchGeoLocation=<?=$geoLocation?>;
        window.isScan = true;
    </script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/hlsjs.js?v=<?=cdn_ver?>"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/error.js?v=<?=cdn_ver?>"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/scan.js?v=<?=cdn_ver?>"></script>
</body>
</html>
