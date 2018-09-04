<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript">
    var globals = <?=json_encode($appinst->config)?>;
</script>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_app.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">应用实例编辑</span>
        </div>
        <div class="h20"></div>
        <div class="content">
            <?php if (file_exists('./app_config/' . $appinst->config_path . '.html')):?>
                <iframe name="ifm-app-config" src="/app_config/<?=$appinst->config_path?>.html" style="width:100%;height:600px;border:none;"></iframe>
            <?php else:?>
                <iframe name="ifm-app-config" src="/app_config/default.html" style="width:100%;height:600px;border:none;"></iframe>
            <?php endif;?>
        </div>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
