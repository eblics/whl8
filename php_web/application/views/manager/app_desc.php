<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/app_lists.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/app_desc.js"></script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_app.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">应用 > 扫码排行</span>
        </div>
        <div class="content app-desc">
            <img src="/static/images/<?=$app->path?>.jpg" />
            <div class="app-desc-txt">
                <h2><?=$app->name?></h2>
                <h3>应用简介：</h3>
                <div>
                    <p>【<?=$app->desc?>】</p>
                </div>
                <h3 class="price">
                    <?=$app->price != 0 ? '￥' . sprintf('%1.2f', $app->price / 100, 2) : '免费'?>
                </h3>
                    <?php if (isset($app->hold)):?>
                        <button data-id="<?=$app->id?>" id="view_btn" class="btn btn-blue">查　看</button>
                    <?php elseif (isset($app->trash)):?>
                        <button data-id="<?=$app->id?>" id="re_apply_btn" class="btn btn-blue">应　用</button>
                    <?php else:?>
                        <button data-id="<?=$app->id?>" id="apply_btn" class="btn btn-blue">
                        <?php if ($app->price):?>
                            购　买
                        <?php else:?>
                            添　加
                        <?php endif;?>
                        </button>
                    <?php endif;?>
            </div>
            <div class="recommend">
                <h3>推荐应用</h3>
                <ul id="recommend" class="fix">
                    <?php foreach ($recommend_apps as $recommend_app):?>
                    <li id="<?=$recommend_app->id?>">
                        <div>
                            <img id="<?=$recommend_app->id?>" src="/static/images/<?=$recommend_app->path?>.jpg" />
                            <h2><span class="app-name"><?=$recommend_app->name?></span></h2>
                            <div>
                                <p><?=$recommend_app->desc?></p>
                                <button id="<?=$recommend_app->id?>" class="btn btn-blue btn-recommend price">
                                    <?=$recommend_app->price != 0 ? '￥' . sprintf('%1.2f', $recommend_app->price / 100, 2) : '免费'?>
                                </button>
                            </div>
                        </div>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="pay-layer hls-hidden">
    <div class="label">
        <p class="tip">支付<?php echo sprintf("%1.2f", $app->price / 100);?>元</p>
        <h3>微信扫码支付</h3>
        <div id="qrcode_container" class="qrcode"></div>
        <p class="expire"><span>60s</span>后此二维码失效</p>
        <button id="pay_layer_close" class="btn btn-blue">关闭</button>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>