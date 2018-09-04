$(function() {
    $(document).on("touchstart touchmove mousedown mousemove", function(event) { //阻止默认事件
        var thistag = event.target.tagName;
        var tagid = event.target.id;
        // if (thistag != "INPUT") { //排除input标签
        //  event.preventDefault();
        //  $('input').blur();
        // }
    }); //阻止默认事件
    $('body').height($(window).height()); //固定内容高度
    //清除对应页面动画方法
    var pagedom = [];
    /*mySwiper初始化*/
    window.mySwiper = new Swiper('.swiper-container', { //mySwiper初始化
        direction: 'vertical',
        mousewheelControl: true,
        watchSlidesProgress: true,
        resistanceRatio: 0,
        onInit: function(swiper) {
            swiper.myactive = 0;
            var slides = swiper.slides;
            for (var i = 0; i < slides.length; i++) {
                pagedom[i] = $(slides[i]).html();
            }
            swiper._slideTo = swiper.slideTo;
            swiper.slideTo = function(i) {
                this.unlockSwipes();
                this._slideTo(i);
            }
            swiper._slidePrev = swiper.slidePrev;
            swiper.slidePrev = function(i) {
                this.unlockSwipes();
                this._slidePrev(i);
            }
            swiper._slideNext = swiper.slideNext;
            swiper.slideNext = function(i) {
                this.unlockSwipes();
                this._slideNext(i);
            }
        },
        onProgress: function(swiper) {
            for (var i = 0; i < swiper.slides.length; i++) {
                var slide = swiper.slides[i];
                var progress = slide.progress;
                var translate, boxShadow;
                translate = progress * swiper.height * 0.8;
                scale = 1 - Math.min(Math.abs(progress * 0.2), 1);
                boxShadowOpacity = 0;
                slide.style.boxShadow = '0px 0px 10px rgba(0,0,0,' + boxShadowOpacity + ')';
                if (i == swiper.myactive) {
                    es = slide.style;
                    es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = 'translate3d(0,' + (translate) + 'px,0) scale(' + scale + ')';
                    es.zIndex = 0;
                } else {
                    es = slide.style;
                    es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = '';
                    es.zIndex = 1;
                }
            }
        },
        onTransitionEnd: function(swiper, speed) {
            swiper.myactive = swiper.activeIndex;
            /*执行对应页面事件*/
            var pagenow = swiper.activeIndex;
            page(pagenow + 1);
        },
        onSetTransition: function(swiper, speed) {
            for (var i = 0; i < swiper.slides.length; i++) {
                es = swiper.slides[i].style;
                es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = speed + 'ms';
            }
        }
    });

    //接口数据请求(模拟数据)
    // $.ajax({
    //  type: 'GET',
    //  url: '',
    //  dataType: 'json',
    //  success: function(obj){
    //      console.log(obj);
    //  }
    // });

    //返回数据模拟
    var errCode = 0; //错误码，0表示正常
    var subscribe = 1; //是否关注公众号 1是 0否
    var scanNum = 0; //已扫码次数
    fromScan = 1; //是否是扫码进入H5  1是 0否
    var isWinner = 1; //是否中奖 1是 0否
    var amount = 1.08; //中奖金额
    var geted = 1; //红包是否发放 1是 2否 （未关注）
    var userInfo = { //用户信息
        "nickname": "阿丫", //用户昵称
        "sex": 1, //用户性别 1男 0女
        "city": "海淀", //市
        "province": "北京", //省
        "country": "中国", //国家
        "headimgurl": "http://wx.qlogo.cn/mmopen/klvXicE/0" //微信头像
    }

    //规则点击事件
    $('.page1 .ruleBtn').off().on('tap', function() {
        $('.ruleMsk').fadeIn(200);
        $('.ruleMsk .closeBtn, .ruleMsk').off().on('tap', function(event) {
            event.stopPropagation();
            event.preventDefault();
            // console.log(event.target.className.indexOf("mskA"));
            // console.log(event.target.className.indexOf("closeBtnA"));
            if (event.target.className.indexOf('closeBtn') < 0 && event.target.className.indexOf("ruleMsk") < 0) {
                return;
            }
            $('.ruleMsk').fadeOut(200);
        });
    });

    //分享点击事件
    $('.page1 .shareBtn,.page2 .shareBtn').off().on('tap', function() {
		window.open("http://ok.jd.com/m/index-73455.htm");
    });
    //点击中奖事件
    $('.page1 .hongbao').off().on('tap', function() {
        $('.page1 .hongbao').on('tap',null);
        hlsjs.ready(function() {
            hlsjs.takeActivity(function(result) {
                $('.page1 .content .point').fadeOut(200);
                console.log(result);
                //判断是否中奖
                if (result.errcode == 0) {
                    $('.page1 .succ .mon').html(result.amount);
                    $('.page1 .succ').fadeIn(200);
                    //二维码显示
                    //$('.page1 .showText,.page1 .shareBtn').fadeOut(200);
                    //$('.page1 .code').attr('src', result.wx_qrcode_url);
                    $('.page1 .code').fadeIn(200);
                } else {
                    $('.page1 .defeat .sorrText').html(result.errmsg);
                    $('.page1 .defeat ').fadeIn(200);
                    $('.page1 .showText').fadeOut(200);
                }
            });
        });
    });



    function cleanpage(p) { //传入页面数字用以恢复到动画执行前
        var slide = mySwiper.slides[p - 1];
        $(slide).html(pagedom[p - 1]);
    }
    //添加禁止滑动类
    (function() {
        $(document).on('touchstart touchend', function() {
            var pagenow = mySwiper.activeIndex;
            var slide = mySwiper.slides[pagenow];
            if ($(slide).hasClass('stop-next')) {
                mySwiper.lockSwipeToNext();
            } else {
                mySwiper.unlockSwipeToNext();
            }
            if ($(slide).hasClass('stop-prev')) {
                mySwiper.lockSwipeToPrev();
            } else {
                mySwiper.unlockSwipeToPrev();
            }
        });
    })();
    /*加载进度条*/
    (function() {
        Pace.options = { //pace配置
            ajax: false,
            restartOnPushState: false,
        }
        Pace.on('done', function() { //加载完成
            console.log('done');
            $('#swiper-container').removeClass('hidden');
            if (fromScan == 1) {
                page(1)
            } else {
                page(2);
            }
        });
    })();

    /*各页面效果*/
    function page(p) {
        if (p == 1) {
            $('.page1 .jbLeft').addClass('leftIn');
            $('.page1 .jbRight').addClass('rightIn');
            console.log('page1');
        } else if (p == 2) {
            mySwiper.slideTo(1);
        }
    }
});
