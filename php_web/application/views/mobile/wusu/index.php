<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="target-densitydpi=320,width=640,user-scalable=no"/>
    <meta name="format-detection" content="telephone=no"/>
    <link rel="stylesheet" href="/static/css/animate.css">
    <title>乌苏兄弟连</title>
    <script type="text/javascript" src="/static/js/loader.js"></script>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/hlsjs.js"></script>
    <script type="text/javascript">
        loadStyle(['/h5/wusu-xiongdilian/css/homePage.css']);
    </script>
</head>
<body style="display: none;">
    <div class="introduce">
        <div class="close"></div>
    </div>
    <div class="logo"></div>
    <div class="title"></div>
    <div class="credits_bg">
        <div class="credits">昵称：<span id="nickname"><?=$currentUser->nickName?></span></div>
    </div>
    <div class="btn">
        <a href="javascript:;">
            <div class="btn_part" id="introduce"></div>
        </a>
        <a href="/wusu/zhuwei">
            <div class="btn_part"></div>
        </a>
        <a href="https://item.jd.com/3917383.html">
            <div class="btn_part"></div>
        </a>
        <a href="/app/mall/home.html?mallid=<?=$mall_id?>">
            <div class="btn_part"></div>
        </a>
        <a href="http://mp.weixin.qq.com/s/DiMB61ivssJ1SM3X3fr_bA">
            <div class="btn_part"></div>
        </a>
    </div>
    <div class="btn_bottom">
        <a href="javascript:;">
            <div class="homepage">
                <div>首页</div>
            </div>
        </a>

        <a href="/user/points?mch_id=<?=$currentUser->mchId?>">
            <div class="personal_center">
                <div>个人中心</div>
            </div>
        </a>
    </div>
    <script type="text/javascript">
        $(window).load(function() {
            $('body').fadeIn();
        });

        $("#introduce").on("click",function () {
            $(".introduce").show();
            $(".close").on("click",function () {
                $(".introduce").hide();
            })
        })
    </script>
</body>
</html>
