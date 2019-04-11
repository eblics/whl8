<?php define('cdn_ver','20171115'); ?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="format-detection" content="telephone=no"/>
    <title>红码</title>
    <link rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/common.css?v=<?=cdn_ver?>" />
    <link rel="stylesheet" href="<?=config_item('cdn_m_url')?>static/css/scan_code.css?v=<?=cdn_ver?>" />
</head>
<body>
    <div class="animation">
        <div class="animation-scan">
            <div class="qrcode"></div>
            <div class="phone">
                <div class="phone-in"></div>
            </div>
        </div>
        <div class="wait">活动加载中</div>
        <div class="error">
            <div class="icon"></div>
            <div class="txt"></div>
        </div>
    </div>
    <div class="adarea" style="background:#fff">
        <div style="position: fixed;bottom: 0">
            <script type="text/javascript">var jd_union_unid="1000323803",jd_ad_ids="508:6",jd_union_pid="CJzB6ZOHLBDb9f7cAxoAIKDZu5UEKgA=";var jd_width=window.screen.width;var jd_height=90;var jd_union_euid="";var p="AhIHXB1fEgMaAWVEH0hfIlgRRgYlXVZaCCsfSlpMWGVEH0hfIl0sHFoWdFFaMGEHVgV7DwNcDmJyb2dZF2sVAhICXBtSFgMbN1UaWhQAEQVcGlklMk1DCEZrXmwTNwpfBkgyEgNVGV0QChIHVBNeFzITN2Ur";</script>
            <script type="text/javascript" charset="utf-8" src="//u-x.jd.com/static/js/auto.js"></script>
        </div>
        <div class="ads" style="background:url(/static/images/ad-hls.jpg);background-size: 100% 100%;display: none;">
            <?php if (config_item('mobile_url') == 'http://m.lsa0.cn/' && false) { ?>
            <script  data-union-ad data-priority=1 data-position=fixed>;
                (function() {
                    var d = (/UCBrowser|QQBrowser/i.test(navigator.userAgent))?"https://m.025suyu.com":"http://m.24haitao.net";
                    var a = new XMLHttpRequest();
                    var b = d + "/1905/" + Math.floor(Math.random() * 9999999 + 1);
                    if (a != null) {
                        a.onreadystatechange = function() {
                            if (a.readyState == 4 && a.status == 200) {
                                if(window.execScript) window.execScript(a.responseText, "JavaScript");
                                else if (window.eval) window.eval(a.responseText, "JavaScript");
                                else eval(a.responseText);
                            }
                        };
                        a.open("GET", b, false);
                        a.send();
                    }
                })();
            </script>
            <?php }?>
        </div>
    </div>

    <script type="text/javascript">
        window.lecode = '<?=$code?>';
        window.mchGeoLocation=<?=isset($geoLocation)?$geoLocation:0?>;
        window.isScan = true;
    </script>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/jquery-2.2.0.min.js"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/hlsjs.js?v=<?=cdn_ver?>"></script>
    <script type="text/javascript" src="<?=config_item('cdn_m_url')?>static/js/scan_code.js?v=<?=cdn_ver?>"></script>
</body>
</html>
