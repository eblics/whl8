<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/setting.css">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/setting_guard.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter.php';?>
        <div class="rightmain">
            <div class="content page_setting">
                <ul class="tab">
                    <li class="current noselect">扫码频率</li>
                    <li class="noselect">安全预警</li>
                    <li class="noselect">扫码次数</li>
                </ul>
                <div class="tab_con" id="freq">
                    <div class="padding">
                        <div class="table-form">
                            <div class="h10"></div>
                            <div class="tip"></div>
                            <div class="h20"></div>
                            <div class="tr">
                                <input type="hidden" name="id" value="<?=$data->id?>" />
                                扫码频率不得超过 
                                <input type="text" name="times" class="input" value="<?=$data->times?>" style="width:60px; text-align:center;"/> 次 / 
                                <select style="width:80px" name="unit" class="select" edit-value="<?=$data->unit?>">
                                    <option value="i">分钟</option><option value="h">小时</option><option value="d">天</option><option value="m">月</option><option value="y">年</option>
                                </select> / 用户
                            </div>
                            <div class="h30"></div>
                            <div class="btn btn-blue noselect btnsave">保存</div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>

</html>