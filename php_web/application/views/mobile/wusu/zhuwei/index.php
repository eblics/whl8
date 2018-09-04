<!doctype html>
<html lang="zh-cn">
<head>
    <?php include VIEWPATH . '/wusu/templates.php'; ?>
    <script type="text/javascript">
        loadStyle(['/h5/wusu-zhuwei/css/index1.css']);
    </script>
</head>
<body style="display: none;">
    <div class="logo"></div>
    <div class="slogan"></div>
    <!--page1-->
    <div class="people"></div>
    <div class="title"></div>
    <div class="title_2"></div>
    <div class="product"></div>
    <!--page2-->
    <div class="msk_bg">
        <span class="down">新疆飞虎</span><input name="badge" class="input down inputDown" value=""><span>!</span></br>
        <span class="span2">* 嘿兄弟，字数有限，请勿输入超过8个字符 *</span>
    </div>
    <div class="title2"></div>
    <div class="title2_bottom"></div>
    <!--page3-->
    <div class="title3"></div>
    <div class="title3_left" id='share'></div>
    <div class="title3_right"></div>
    <dir class="my-badge-container">
        <div class="page3_bg">
            <div class="badge"></div>
            <p id="my_badge" class="my-badge">--</p>
            <div class="cutOff"></div>
            <div class="user">
                <img src="<?=$currentUser->headimgurl?>">
                <p style="opcity:.8"><?=$currentUser->nickName?></p>
            </div>
            <p class="ranking" style="opcity:.8">您是第 <span id="ranking" style="color: #e7cd85">--</span> </br>新疆的劳道兄弟</p>
        </div>
        <div class="erweima2">
            <img src="/h5/wusu-zhuwei/images/erweima2.png" alt="">
            <span class="jietu">截图保存并分享</span>
        </div>
    </dir>

    <!--page4-->
<!--    <div class="msk4"></div>-->
    <div class="msk"></div>
    <div class="stands"></div>
    <div class="ball"></div>
    <div class="title4"></div>
    <!--page5-->
    <div class="product5"></div>
    <div class="title5"></div>
    <a href="/wusu">
        <div class="forward5"></div>
    </a>
    <!--page6-->
    <div class="award"></div>
    <div class="forward"></div>
    <!--page7-->
    <div class="msk_bg7">
        <span class="down">姓名</span><input name="realname" class="input down3 inputDown"></br>
        <span class="down">电话</span><input name="mobile" class="input down3 inputDown"></br>
        <span class="down">地址</span><input name="address" class="input down3 inputDown"></br>
        <span class="info">*请务必填写正确的个人信息，以确保奖品顺利送达*</span>
    </div>
    <div class="title7_bottom"></div>
    <!--page8-->
    <div class="product8"></div>
    <div class="record"></div>
    <a href="/wusu">
        <div class="forward8"></div>
    </a>

    <div class="btn_music"></div>

<!--    分享蒙层-->
<!--    <div class="share">-->
<!--        <div class="share_text"></div>-->
<!--        <div class="jiantou"></div>-->
<!--    </div>-->


<script type="text/javascript">
    $('.btn_music').click(function() {
        $(this).toggleClass('run');
        if (window.audio.paused) {
            window.audio.play();
        } else {
            window.audio.pause()
        }
    });
</script>
<script>
    $(window).load(function() {

        /**
         * 分享蒙层
         *
         */
        $("#share").on("click",function () {
            $(".msk").show();
            $(".stands").show();
            $(".ball").show();
            $(".title4").show();

            $(".stands,.msk,.ball,.title4").on("click",function () {
                $(".msk").hide();
                $(".stands").hide();
                $(".ball").hide();
                $(".title4").hide();
            })
        })


        $('body').show();
        window.audio = new Audio();
        window.audio.src = '/h5/wusu-zhuwei/audio/audio.mp3';
        var clicked = false;
        $('body').on('touchstart', function() {
            if (clicked) return;
            clicked = true;
            $('.btn_music').toggleClass('run');
            window.audio.play();
        });

        hlsjs.ready(function() {
            setTimeout(function() {
                wx = hlsjs.wx();
                wx.onMenuShareAppMessage({
                    title: '<?=$title?>',
                    desc: '<?=$desc?>',
                    link: location.href,
                    imgUrl: 'http://' + location.host + '/h5/wusu-zhuwei/images/wusu.png',
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            }, 3000);
        });
    });

    /**
     * 获取该用户的名次
     *
     */
    var getRanking = function() {
        var params = {
            "badge": $('input[name=badge]').val()
        };
        if (params.badge.length > 8) {
            alert('您的助威语句太长，请输入不多于8个字符。');
            return;
        }
        $.get('/wusu/api/ranking.get', params, function(resp) {
            if (resp.errcode === 0) {
                showMyBadge(resp.data.ranking, resp.data.badge);
            } else {
                alert(resp.errmsg + '。');
            }
        }).fail(function() {
            alert('无法连接服务器。');
        });
    };

    var tryPrize = function() {
        $.post('/wusu/api/prize.try', {}, function(resp) {
            $(".title3").hide();
            $(".title3_left").hide();
            $(".title3_right").hide();
            $(".msk").hide();
            $(".stands").hide();
            $(".ball").hide();
            $(".title4").hide();
            if (resp.errcode === 0) {
                if (resp.data.prize !== 0) {
                    $('.award').css({
                        'background-image': 'url(/h5/wusu-zhuwei/images/award' + resp.data.prize + '.png)'
                    });
                    $(".award").show();
                    $(".forward").show();
                    $(".forward").click(function () {
                        $(".award").hide();
                        $(".forward").hide();

                        $(".msk_bg7").show();
                        $(".title7_bottom").show();

                        $(".title7_bottom").click(function () {
                            saveUserInfo();
                        })
                    });
                } else {
                    $(".product5").show();
                    $(".title5").show();
                    $(".forward5").show();
                }
            } else {
                alert(resp.errmsg);
            }
        }).fail(function() {
            alert('无法连接服务器。');
        });
    };

    /**
     * 保存中奖后用户的邮寄信息
     *
     */
    var saveUserInfo = function() {
        var realname = $('input[name=realname]').val();
        var mobile = $('input[name=mobile]').val();
        var address = $('input[name=address]').val();
        var validate = true, numberReg = /^\d+$/;
        if (realname.length < 2 || realname.length > 8) {
            alert('姓名必须包含2-8个字符');
            validate = false;
        }
        if ((validate && mobile.length !== 11) || (validate && ! numberReg.test(mobile))) {
            alert('请输入有效的手机号');
            validate = false;
        }
        if (validate && address.length < 5) {
            alert('请输入有效的收货地址');
            validate = false;
        }
        if (! validate) {
            return;
        }
        var params = {
            "realname": realname,
            "mobile": mobile,
            "address": address,
        };
        $.post('/wusu/api/user.save', params, function(resp) {
            $(".title3").hide();
            $(".title3_left").hide();
            $(".title3_right").hide();
            $(".msk").hide();
            $(".stands").hide();
            $(".ball").hide();
            $(".title4").hide();
            if (resp.errcode === 0) {
                showSaveSuccess();
            }
        }).fail(function() {
            alert('无法连接服务器。');
        });
    };

    var showSaveSuccess = function() {
        $(".msk_bg7").hide();
        $(".title7_bottom").hide();

        $(".product8").show();
        $(".record").show();
        $(".forward8").show();
    };

    var showMyBadge = function(ranking, badge) {
        $(".msk_bg").hide();
        $(".title2").hide();
        $(".title2_bottom").hide();

        $(".title3").show();
        $(".title3_left").show();
        $(".title3_right").show();
        $('#ranking').text(ranking);
        $('#my_badge').text(badge);
        $(".my-badge-container").show();

        $(".title3_right").click(function () {
            $(".my-badge-container").hide();
            // $(".msk").show();
            // $(".stands").show();
            // $(".ball").show();
            // $(".title4").show();

            tryPrize();
        });
    };

    $(function () {

        var timer = setTimeout(function () {
            $('body').hide();
            $(".wrapper").css('backgroundImage','url(./images/bg2.jpg)');
            $('body').fadeIn();
            $(".people").hide();
            $(".title").hide();
            $(".title_2").hide();
            $(".product").hide();

            $(".msk_bg").show();
            $(".title2").show();
            $(".title2_bottom").show();

            // 助威语句输入完毕，点击提交响应
            $(".title2_bottom").click(function () {
                getRanking();
            });
        },15000);

        $(".title_2").on('click',function () {
            $('body').hide();
            $(".wrapper").css('backgroundImage','url(./images/bg2.jpg)');
            $('body').fadeIn();
            $(".people").hide();
            $(".title").hide();
            $(".title_2").hide();
            $(".product").hide();

            $(".msk_bg").show();
            $(".title2").show();
            $(".title2_bottom").show();

            // 助威语句输入完毕，点击提交响应
            $(".title2_bottom").click(function () {
                getRanking();
            });

            window.clearTimeout(timer);
        })
    });
</script>
</body>
</html>
