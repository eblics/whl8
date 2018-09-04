<!DOCTYPE html>
<html>
<head>
    <title>余额</title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no, address=no">
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>css/common.css" />
    <link type="text/css" rel="stylesheet" href="<?=$this->config->item('cdn_m_url')?>css/extract.css" />
    <script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>js/jquery-2.2.0.min.js"></script>
    <script type="text/javascript" src="<?=$this->config->item('cdn_m_url')?>js/extract.js"></script>
</head>
<body>
    <div id="page-ext" class="wraper ext-content">
        <div class="ext-a1">
            <div class="ext-a1b0"></div>
            <div class="ext-a1b1">
                <div class="ext-a1b1c0"></div>
                <div class="ext-a1b1c1">
                    <div class="ext-a1b1c1d0"></div>
                    <div class="ext-a1b1c1d1">
                        <div class="ext-totalname"><div class="ext-tn1">总金额(元)</div></div>
                        <!-- <div class="ext-total"><input type="text" placeholder="232.21"></div> -->
                        <div class="ext-total">221112.22</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ext-a2">
            <div class="ext-a2b1"></div>
            <div class="ext-a2b2">金额大于一元即可提取现金红包</div>
        </div>
        <div class="ext-a3">
            <div class="ext-a3b1">
                <div class="ext-a3b1c1">提现金额(元)</div>
            </div>
            <div class="ext-a3b2">
                <div class="ext-a3b2c1">&yen;</div>
                <div class="ext-a3b2c2"><input type="text" id="ext_value"></div>
            </div>
            <div class="ext-a3b3"></div>
            <div class="ext-a3b4">请输入大于一元的提现金额</div>
        </div>
        <div class="ext-a4">
            <div class="ext-a4b1" id="ext-click">立即提取</div>
        </div>
    </div>
</body>
</html>
