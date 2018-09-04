<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/lib/css/mobiscroll.custom-2.5.2.min.css"/>
    <title>个人信息</title>
    <script type="text/javascript" src="/static/lib/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/static/lib/mobiscroll.custom-2.5.2.min.js"></script>
    <script type="text/javascript" src="/static/lib/mobiscroll.datetime-2.5.1-zh.js"></script>
    <script type="text/javascript" src="/static/js/loader.js"></script>
    <script type="text/javascript">
    loadStyle(['/static/css/personalinfo.css']);
    var birthday='<?=$birthday?>';
    var areacode=[<?=$provincecode?>,<?=$citycode?>];
    var mchid=<?=$mchid?>;
    </script>
</head>
<body style="display: none;">
    <div class="content">
        <div class="block">
            <div class="list">
                <div class="item">
                    <span class="info">真实姓名</span>
                    <input id="realname" placeholder="你的姓名" maxLength="20" type="text" class="textbox" 
                        value="<?=$realname?>"/>
                </div>
                <div class="item">
                    <span class="info">手机号</span>
                    <input id="mobile" placeholder="你的手机号" type="number" class="textbox" 
                        value="<?=$mobile?>"/>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="list">
                <div class="item">
                    <span class="info">城市</span>
                    <span id="cityinfo" placeholder="你的城市" class="textspan"><?=$cityinfo?></span>
                </div>
                <div class="item">
                    <span class="info">生日</span>
                    <span id="birthday" placeholder="你的生日" class="textspan"><?=$birthday?></span>
                </div>
            </div>
        </div>
        <div class="block">
            <div class="list">
                <div class="item textarea">
                    <span class="info">收货地址</span>
                    <div id="address" placeholder="你的地址" class="textbox" maxLength="200"><?=$address?></div>
                </div>
            </div>
        </div>
        <div class="save disabled">保存</div>
        <input id="birthtext" type="text" style="display:none"/>
        <ul id="citymenu" style="display:none">
        <?php
            $areasHtml='';
            foreach ($areas as $area) {
                $areasHtml.='<li data-val="'.$area['code'].'">';
                $areasHtml.=    $area['name'];
                $areasHtml.=    '<ul>';
                foreach ($area['children'] as $city) {
                    $areasHtml.=    '<li data-val="'.$city['code'].'">';
                    $areasHtml.=        $city['name'];
                    $areasHtml.=    '</li>'; }
                $areasHtml.=    '</ul>';
                $areasHtml.='</li>'; }
            print $areasHtml;
        ?>
        </ul>
    </div>
    <script type="text/javascript">
        loadScript(['/static/js/personalinfo.js']);
        $('body').fadeIn();
    </script>
</body>
</html>