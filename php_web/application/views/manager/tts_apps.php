<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/tts_app.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_user.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">TTS接口</span>
            </div>
            <div class="h20"></div>
            
            <form type="validate">
            <table class="table-form">
                    <tr>
                        <td class="name" width="150">appId：</td>
                        <td class="value" width="350">
                            <input class="input" type="text" id="appid" value="******" disabled="disabled" name="appid"/>
                        </td>
                        <td class="tip">appId默认隐藏</td>
                    </tr>
                    <tr>
                        <td class="name" width="150">appSecret：</td>
                        <td class="value" width="350">
                            <input class="input" type="text" id="appsecret" value="******" disabled="disabled" name="appsecret"/>
                        </td>
                        <td class="tip">appSecret默认隐藏</td>
                    </tr>
                    <tr>
                        <td></td><td><input class="btn btn-blue show" style="float:right;" type="button" id="sub" value="点击显示"></td><td></td>
                    </tr>
                </table>
                
            </form>
        
         </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>