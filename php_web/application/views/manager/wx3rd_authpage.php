<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/wx3rd.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">微信公众号授权</span>
            </div>
            <div class="h40"></div>
            <div ID="authResult">“<?=$data->name?>” <?=$data->errmsg?> ，立即<a style="text-decoration:underline;" class="blue" href="<?=$data->url?>">完善信息</a>！</div> 
            <div class="h40"></div>
         </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>
